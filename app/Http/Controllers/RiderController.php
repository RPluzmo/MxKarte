<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Track;
use App\Rules\PersonName;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RiderController extends Controller
{
    private const GUEST_RIDER_SESSION_KEY = 'guest_rider_ids';
    private const GUEST_IDENTITY_SESSION_KEY = 'guest_rider_identity';

    public function store(Request $request, Track $track)
    {
        if ($user = $request->user()) {
            if (blank($user->surname) || blank($user->category) || blank($user->experience_level)) {
                return redirect()
                    ->route('profile.edit')
                    ->with('status', 'Pirms pieteikšanās, lūdzu, pabeidz profilu: uzvārds, motocikla kategorija un pieredze.');
            }

            $request->merge([
                'name' => $user->name,
                'surname' => $user->surname,
                'club_id' => $user->club_id,
                'category' => $user->category,
                'experience_level' => $user->experience_level,
            ]);
        }

        $request->merge([
            'name' => PersonName::normalize($request->input('name')),
            'surname' => PersonName::normalize($request->input('surname')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', new PersonName(), 'max:255'],
            'surname' => ['required', 'string', new PersonName(), 'max:255'],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'])],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'])],
            'ride_date' => ['required', 'date_format:Y-m-d', Rule::in(array_keys(Rider::bookableDates()))],
            'ride_time' => ['required', 'date_format:H:i', 'after_or_equal:06:00', 'before_or_equal:23:59'],
            'replace_existing' => ['sometimes', 'boolean'],
        ]);

        if ($validated['ride_date'] === today()->toDateString() && $validated['ride_time'] <= now()->format('H:i')) {
            throw ValidationException::withMessages(['ride_time' => 'Šodien nevar pieteikties jau pagājušam laikam.']);
        }

        $registrationAttributes = $validated;
        unset($registrationAttributes['replace_existing']);

        $userId = $request->user()?->id;
        $guestKey = null;

        if (! $userId) {
            $guestIdentity = $request->session()->get(self::GUEST_IDENTITY_SESSION_KEY);

            if (! is_string($guestIdentity) || strlen($guestIdentity) < 64) {
                $guestIdentity = Str::random(64);
                $request->session()->put(self::GUEST_IDENTITY_SESSION_KEY, $guestIdentity);
            }

            $guestKey = hash('sha256', $guestIdentity);
        }

        $registrationKey = Rider::registrationKeyFor([
            ...$registrationAttributes,
            'user_id' => $userId,
            'guest_key' => $guestKey,
        ]);

        $replaceExisting = $request->boolean('replace_existing');

        try {
            $result = DB::transaction(function () use ($validated, $registrationAttributes, $request, $track, $registrationKey, $replaceExisting, $guestKey, $userId): array {
                $existingRiders = $this->sameDayRegistrations(
                    $validated,
                    $userId,
                    $guestKey,
                    $request->session()->get(self::GUEST_RIDER_SESSION_KEY, [])
                );

                if ($existingRiders->isNotEmpty() && ! $replaceExisting) {
                    return ['conflict' => $existingRiders->first()->load('track'), 'replaced_ids' => []];
                }

                $replacedIds = $existingRiders->modelKeys();
                $existingRiders->each->delete();

                $rider = $track->riders()->create([
                    ...$registrationAttributes,
                    'registration_key' => $registrationKey,
                    'track_id' => $track->id,
                    'user_id' => $request->user()?->id,
                ]);

                return ['rider' => $rider, 'replaced_ids' => $replacedIds];
            });
        } catch (QueryException $exception) {
            $existingRider = $this->sameDayRegistrations(
                $validated,
                $userId,
                $guestKey,
                $request->session()->get(self::GUEST_RIDER_SESSION_KEY, [])
            )->first();

            if (! $existingRider) {
                throw $exception;
            }

            return $this->confirmReplacement($track, $validated, $existingRider->load('track'));
        }

        if (isset($result['conflict'])) {
            return $this->confirmReplacement($track, $validated, $result['conflict']);
        }

        $rider = $result['rider'];
        $guestRiderIds = collect($request->session()->get(self::GUEST_RIDER_SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => in_array($id, $result['replaced_ids'], true));

        if (! $request->user()) {
            $guestRiderIds->push((int) $rider->id);
        }

        if (! $request->user() || $result['replaced_ids'] !== []) {
            $request->session()->put(
                self::GUEST_RIDER_SESSION_KEY,
                $guestRiderIds->unique()->values()->all()
            );
        }

        return redirect()->route('tracks.show', $track)->with(
            'status',
            $result['replaced_ids'] ? 'Iepriekšējais pieteikums aizstāts ar jauno.' : 'Pieteikums saglabāts.'
        );
    }

    public function destroy(Request $request, Rider $rider): RedirectResponse
    {
        $guestRiderIds = array_map(
            'intval',
            $request->session()->get(self::GUEST_RIDER_SESSION_KEY, [])
        );
        $isAuthenticatedOwner = $rider->user_id !== null
            && (int) $rider->user_id === (int) $request->user()?->id;
        $isGuestOwner = $rider->user_id === null
            && in_array((int) $rider->id, $guestRiderIds, true);

        abort_unless($isAuthenticatedOwner || $isGuestOwner, 403);

        $track = $rider->track;
        $riderId = (int) $rider->id;
        $rider->delete();

        if ($isGuestOwner) {
            $request->session()->put(
                self::GUEST_RIDER_SESSION_KEY,
                array_values(array_diff($guestRiderIds, [$riderId]))
            );
        }

        return redirect()->route('tracks.show', $track)->with(
            'status',
            'Pieteikums ir atsaukts un dzēsts.'
        );
    }

    private function sameDayRegistrations(array $validated, ?int $userId, ?string $guestKey, array $guestRiderIds)
    {
        $query = Rider::query()
            ->where('ride_date', $validated['ride_date'])
            ->lockForUpdate();

        if ($userId) {
            return $query
                ->where(function ($identityQuery) use ($userId, $guestRiderIds) {
                    $identityQuery->where('user_id', $userId);

                    if ($guestRiderIds !== []) {
                        $identityQuery->orWhere(fn ($guestQuery) => $guestQuery
                            ->whereNull('user_id')
                            ->whereIn('id', $guestRiderIds));
                    }
                })
                ->get();
        }

        $registrationKey = Rider::registrationKeyFor([
            ...$validated,
            'user_id' => null,
            'guest_key' => $guestKey,
        ]);

        return $query
            ->whereNull('user_id')
            ->where(function ($identityQuery) use ($registrationKey, $guestRiderIds) {
                $identityQuery->where('registration_key', $registrationKey);

                if ($guestRiderIds !== []) {
                    $identityQuery->orWhereIn('id', $guestRiderIds);
                }
            })
            ->get();
    }

    private function confirmReplacement(Track $track, array $validated, Rider $existingRider): RedirectResponse
    {
        return redirect()->route('tracks.show', $track)
            ->withInput()
            ->with('rider_conflict', [
                'track_name' => $existingRider->track->name,
                'ride_date' => $validated['ride_date'],
                'ride_date_label' => Rider::bookableDates()[$validated['ride_date']] ?? $validated['ride_date'],
                'ride_time' => substr($existingRider->ride_time, 0, 5),
            ]);
    }
}
