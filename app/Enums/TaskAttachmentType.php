<?php

namespace App\Enums;

/**
 * The four kinds of file a volunteer may attach to task work.
 *
 * The type is not just a label: it carries the accepted MIME list and the size
 * ceiling, so the upload endpoint, the Filament form and any future importer all
 * validate identically from one place. Storing the category alongside the raw
 * `mime_type` lets the app group a gallery ("3 photos, 1 video") without parsing
 * MIME strings on the client.
 *
 * Size ceilings are deliberately conservative. This app is used in the field in
 * Afghanistan, often on 3G and on prepaid data — a 200MB video that fails at 90%
 * costs a volunteer real money and gets the app uninstalled. Raise these only
 * with that in mind.
 */
enum TaskAttachmentType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case AUDIO = 'audio';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Photo',
            self::VIDEO => 'Video',
            self::DOCUMENT => 'Document',
            self::AUDIO => 'Audio',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IMAGE => 'heroicon-o-photo',
            self::VIDEO => 'heroicon-o-video-camera',
            self::DOCUMENT => 'heroicon-o-document-text',
            self::AUDIO => 'heroicon-o-musical-note',
        };
    }

    /**
     * MIME types accepted for this category.
     *
     * An allow-list, not a deny-list: anything not named here is refused. That
     * is the only safe direction for a public upload endpoint.
     *
     * @return array<int, string>
     */
    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::IMAGE => [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/heic',  // default on iPhone cameras
                'image/heif',
            ],
            self::VIDEO => [
                'video/mp4',
                'video/quicktime', // .mov, default on iPhone
                'video/webm',
                'video/3gpp',      // common on low-end Android
            ],
            self::DOCUMENT => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'text/csv',
            ],
            self::AUDIO => [
                'audio/mpeg',
                'audio/mp4',
                'audio/aac',
                'audio/ogg',
                'audio/wav',
                'audio/webm',
                'audio/3gpp',      // voice notes from low-end Android
            ],
        };
    }

    /** Upload ceiling in kilobytes — the unit Laravel's `max:` rule expects. */
    public function maxSizeKb(): int
    {
        return match ($this) {
            self::IMAGE => 10 * 1024,     // 10 MB — a modern phone photo, uncompressed
            self::VIDEO => 64 * 1024,     // 64 MB — roughly a minute at 1080p
            self::DOCUMENT => 25 * 1024,  // 25 MB
            self::AUDIO => 25 * 1024,     // 25 MB — a long voice note
        };
    }

    /**
     * Laravel validation rules for a file of this type.
     *
     * @return array<int, string>
     */
    public function validationRules(): array
    {
        return [
            'file',
            'mimetypes:'.implode(',', $this->allowedMimeTypes()),
            'max:'.$this->maxSizeKb(),
        ];
    }

    public function accepts(string $mimeType): bool
    {
        return in_array(strtolower($mimeType), $this->allowedMimeTypes(), strict: true);
    }

    /**
     * Classify an uploaded file by its MIME type.
     *
     * Returns null when nothing accepts it, so the caller refuses the upload
     * rather than quietly filing an executable as a "document".
     */
    public static function fromMimeType(?string $mimeType): ?self
    {
        if (blank($mimeType)) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->accepts($mimeType)) {
                return $case;
            }
        }

        return null;
    }

    /** Every MIME type the system accepts, across all four categories. */
    public static function allAllowedMimeTypes(): array
    {
        return array_merge(...array_map(
            fn (self $case) => $case->allowedMimeTypes(),
            self::cases(),
        ));
    }

    /** @return array<string, string> value => label, for select inputs. */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
