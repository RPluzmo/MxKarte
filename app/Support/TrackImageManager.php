<?php

namespace App\Support;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class TrackImageManager
{
    public function sync(Track $track, Request $request, array $validated, array $attributes = []): void
    {
        $stagedPaths = [];
        $coverPath = null;
        $galleryPaths = [];
        $directory = 'tracks/'.($attributes['slug'] ?? $track->slug);

        try {
            if ($cover = $request->file('cover_image')) {
                $coverPath = $this->store($cover, $directory, 'cover_image');
                $stagedPaths[] = $coverPath;
            }

            foreach ($request->file('gallery_images', []) as $image) {
                $path = $this->store($image, $directory, 'gallery_images');
                $galleryPaths[] = $path;
                $stagedPaths[] = $path;
            }

            $pathsToDelete = DB::transaction(function () use ($track, $validated, $attributes, $coverPath, $galleryPaths): array {
                $lockedTrack = Track::query()->whereKey($track->getKey())->lockForUpdate()->firstOrFail();

                if ($attributes !== []) {
                    $lockedTrack->fill($attributes)->save();
                }

                $images = $lockedTrack->images()->lockForUpdate()->get();
                $removeIds = $validated['remove_gallery_ids'] ?? [];
                $galleryToRemove = $images
                    ->where('type', 'gallery')
                    ->whereIn('id', $removeIds);
                $galleryCount = $images->where('type', 'gallery')->count() - $galleryToRemove->count();

                if ($galleryCount + count($galleryPaths) > 4) {
                    throw ValidationException::withMessages([
                        'gallery_images' => 'Galerijā drīkst būt ne vairāk kā četri attēli.',
                    ]);
                }

                $pathsToDelete = $galleryToRemove->pluck('path')->all();
                foreach ($galleryToRemove as $image) {
                    $image->delete();
                }

                $oldCover = $images->firstWhere('type', 'cover');
                $removeCover = $validated['remove_cover'] ?? false;

                if ($coverPath !== null && $oldCover && ! $removeCover) {
                    throw ValidationException::withMessages([
                        'cover_image' => 'Lai aizstātu titulattēlu, vispirms atzīmē pašreizējā titulattēla noņemšanu.',
                    ]);
                }

                if ($coverPath !== null) {
                    if ($oldCover) {
                        $pathsToDelete[] = $oldCover->path;
                        $oldCover->update([
                            'path' => $coverPath,
                            'sort_order' => 0,
                        ]);
                    } else {
                        $lockedTrack->images()->create([
                            'type' => 'cover',
                            'path' => $coverPath,
                            'sort_order' => 0,
                        ]);
                    }
                } elseif ($removeCover) {
                    if ($oldCover) {
                        $pathsToDelete[] = $oldCover->path;
                        $oldCover->delete();
                    }
                }

                $usedOrders = $lockedTrack->images()
                    ->where('type', 'gallery')
                    ->pluck('sort_order')
                    ->all();

                foreach ($galleryPaths as $path) {
                    $sortOrder = collect(range(1, 4))
                        ->first(fn (int $order): bool => ! in_array($order, $usedOrders, true));

                    $lockedTrack->images()->create([
                        'type' => 'gallery',
                        'path' => $path,
                        'sort_order' => $sortOrder,
                    ]);
                    $usedOrders[] = $sortOrder;
                }

                return array_values(array_unique($pathsToDelete));
            });
        } catch (Throwable $exception) {
            $this->deleteFiles($stagedPaths);

            throw $exception;
        }

        $this->deleteFiles($pathsToDelete);
    }

    public function deleteTrack(Track $track): void
    {
        $pathsToDelete = DB::transaction(function () use ($track): array {
            $lockedTrack = Track::query()
                ->whereKey($track->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $paths = $lockedTrack->images()->pluck('path')->all();
            $lockedTrack->delete();

            return $paths;
        });

        $this->deleteFiles($pathsToDelete);
    }

    public function retryPendingDeletes(): int
    {
        $disk = Storage::disk('public');
        $deletedCount = 0;

        DB::table('pending_file_deletions')->orderBy('id')->chunkById(100, function ($pendingFiles) use ($disk, &$deletedCount): void {
            foreach ($pendingFiles as $pendingFile) {
                try {
                    if ($disk->exists($pendingFile->path) && ! $disk->delete($pendingFile->path)) {
                        throw new \RuntimeException('Storage refused to delete the file.');
                    }

                    DB::table('pending_file_deletions')->where('id', $pendingFile->id)->delete();
                    $deletedCount++;
                } catch (Throwable $exception) {
                    Log::warning('Neizdevās atkārtoti izdzēst trases attēla failu.', [
                        'path' => $pendingFile->path,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            }
        });

        return $deletedCount;
    }

    private function store(UploadedFile $image, string $directory, string $field): string
    {
        $path = $image->store($directory, 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                $field => 'Attēlu neizdevās saglabāt. Lūdzu, mēģini vēlreiz.',
            ]);
        }

        return $path;
    }

    private function deleteFiles(array $paths): void
    {
        if ($paths === []) {
            return;
        }

        $disk = Storage::disk('public');

        foreach (array_unique($paths) as $path) {
            try {
                if (! $disk->delete($path)) {
                    throw new \RuntimeException('Storage refused to delete the file.');
                }

                DB::table('pending_file_deletions')->where('path', $path)->delete();
            } catch (Throwable $exception) {
                Log::warning('Neizdevās izdzēst trases attēla failu.', [
                    'path' => $path,
                    'exception' => $exception->getMessage(),
                ]);

                try {
                    DB::table('pending_file_deletions')->updateOrInsert(
                        ['path' => $path],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                } catch (Throwable $queueException) {
                    Log::error('Neizdevās reģistrēt trases attēla faila dzēšanas atkārtojumu.', [
                        'path' => $path,
                        'exception' => $queueException->getMessage(),
                    ]);
                }
            }
        }
    }
}
