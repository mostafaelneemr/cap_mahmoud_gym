<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles machine / equipment photos attached to exercises.
 *
 * Files live on the "public" disk under exercises/machines/.
 * Newly stored files are tracked so they can be discarded if the
 * surrounding DB transaction fails, and replaced files are only
 * removed after the exercise row has been saved.
 */
class ExerciseMachineImageService
{
    public const DISK = 'public';
    public const DIRECTORY = 'exercises/machines';
    public const ALLOWED_MIMES = 'jpg,jpeg,png,webp';
    public const MAX_KB = 5120;

    /** Paths stored during the current request (for rollback). */
    protected array $storedPaths = [];

    /** Validation rule string reused by the form requests. */
    public static function rule(): string
    {
        return 'nullable|image|mimes:' . self::ALLOWED_MIMES . '|max:' . self::MAX_KB;
    }

    public function store(UploadedFile $file): string
    {
//        $path = $file->store(self::DIRECTORY, self::DISK);
        $path = saveCompressedWebpImage($file, self::DIRECTORY, self::DISK);
        $this->storedPaths[] = $path;

        return $path;
    }

    public function delete(?string $path): void
    {
        if (!empty($path) && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /**
     * Resolve the machine image for an exercise payload.
     *
     * - New upload            => stores it, sets `machine_image`, old file queued for deletion.
     * - remove_machine_image  => sets `machine_image` to null, old file queued for deletion.
     * - Nothing               => `machine_image` is left untouched.
     *
     * @return array{0: array, 1: string|null} [cleaned payload, old path to delete after save]
     */
    public function resolve(array $exData, ?Exercise $exercise = null): array
    {
        $file = $exData['machine_image'] ?? null;
        $remove = filter_var($exData['remove_machine_image'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $currentPath = $exercise?->machine_image;
        $pathToDelete = null;

        // Never trust a non-file value coming from the request.
        unset($exData['machine_image'], $exData['remove_machine_image']);

        if ($file instanceof UploadedFile && $file->isValid()) {
            $exData['machine_image'] = $this->store($file);
            $pathToDelete = $currentPath;
        } elseif ($remove && $currentPath) {
            $exData['machine_image'] = null;
            $pathToDelete = $currentPath;
        }

        return [$exData, $pathToDelete];
    }

    /** Remove every file stored during this request (call on rollback). */
    public function discardStored(): void
    {
        foreach ($this->storedPaths as $path) {
            $this->delete($path);
        }
        $this->storedPaths = [];
    }
}
