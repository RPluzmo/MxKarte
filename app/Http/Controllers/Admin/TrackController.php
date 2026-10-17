<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::with('owner')->latest()->paginate(25);

        return view('admin.tracks.index', compact('tracks'));
    }

    public function create()
    {
        return view('admin.tracks.form', [
            'track' => new Track,
            'owners' => User::whereIn('role', ['owner', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTrack($request);
        $validated['slug'] = Str::slug($validated['slug']);

        $track = Track::create($this->trackAttributes($validated));
        $this->saveImages($request, $track, $validated);

        return redirect()->route('admin.tracks.index')->with('status', 'Trase izveidota.');
    }

    public function edit(Track $track)
    {
        return view('admin.tracks.form', [
            'track' => $track,
            'owners' => User::whereIn('role', ['owner', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Track $track)
    {
        $validated = $this->validateTrack($request, $track);
        $validated['slug'] = Str::slug($validated['slug']);
        $this->assertGalleryCapacity($track, $validated);
        $track->update($this->trackAttributes($validated));
        $this->saveImages($request, $track, $validated);

        return redirect()->route('admin.tracks.index')->with('status', 'Trase atjaunota.');
    }

    public function destroy(Track $track)
    {
        $paths = $track->images()->pluck('path')->all();
        $track->delete();
        Storage::disk('public')->delete($paths);

        return redirect()->route('admin.tracks.index')->with('status', 'Trase izdzēsta.');
    }

    private function validateTrack(Request $request, ?Track $track = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        $ownerRule = Rule::exists('users', 'id')->where(function ($query) {
            $query->whereIn('role', ['owner', 'admin']);
        });

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tracks', 'slug')->ignore($track?->id)],
            'lat' => ['required', 'numeric', 'between:55.6,58.1'],
            'lng' => ['required', 'numeric', 'between:20.8,28.3'],
            'description' => ['nullable', 'string'],
            'surface_type' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', $ownerRule],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:4'],
            'gallery_images.*' => ['image', 'max:5120'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:track_images,id'],
        ]);
    }

    private function trackAttributes(array $validated): array
    {
        return array_intersect_key($validated, array_flip([
            'name', 'slug', 'lat', 'lng', 'description', 'surface_type', 'user_id',
        ]));
    }

    private function saveImages(Request $request, Track $track, array $validated): void
    {
        $removeIds = $validated['remove_gallery_ids'] ?? [];
        $imagesToRemove = $track->images()
            ->where('type', 'gallery')
            ->whereIn('id', $removeIds)
            ->get();
        $remainingGalleryCount = $track->images()->where('type', 'gallery')->count() - $imagesToRemove->count();
        $newGalleryImages = $request->file('gallery_images', []);

        if ($remainingGalleryCount + count($newGalleryImages) > 4) {
            throw ValidationException::withMessages([
                'gallery_images' => 'Galerijā drīkst būt ne vairāk kā četri attēli.',
            ]);
        }

        Storage::disk('public')->delete($imagesToRemove->pluck('path')->all());
        foreach ($imagesToRemove as $image) {
            $image->delete();
        }

        if ($cover = $request->file('cover_image')) {
            $path = $cover->store("tracks/{$track->id}", 'public');
            $oldCover = $track->images()->where('type', 'cover')->first();
            $track->images()->updateOrCreate(
                ['type' => 'cover'],
                ['path' => $path, 'sort_order' => 0]
            );

            if ($oldCover && $oldCover->path !== $path) {
                Storage::disk('public')->delete($oldCover->path);
            }
        }

        $usedOrders = $track->images()->where('type', 'gallery')->pluck('sort_order')->all();
        foreach ($newGalleryImages as $image) {
            $sortOrder = collect(range(1, 4))->first(fn (int $order) => ! in_array($order, $usedOrders, true));
            $path = $image->store("tracks/{$track->id}", 'public');

            $track->images()->create([
                'type' => 'gallery',
                'path' => $path,
                'sort_order' => $sortOrder,
            ]);
            $usedOrders[] = $sortOrder;
        }
    }

    private function assertGalleryCapacity(Track $track, array $validated): void
    {
        $removeIds = $validated['remove_gallery_ids'] ?? [];
        $removedCount = $track->images()
            ->where('type', 'gallery')
            ->whereIn('id', $removeIds)
            ->count();
        $remainingCount = $track->images()->where('type', 'gallery')->count() - $removedCount;
        $newCount = count($validated['gallery_images'] ?? []);

        if ($remainingCount + $newCount > 4) {
            throw ValidationException::withMessages([
                'gallery_images' => 'Galerijā drīkst būt ne vairāk kā četri attēli.',
            ]);
        }
    }
}
