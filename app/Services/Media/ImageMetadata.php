<?php

namespace App\Services\Media;

use Illuminate\Support\Carbon;

/**
 * What could be learned about one photograph.
 *
 * A value object rather than a loose array so callers cannot mistake a client
 * -supplied timestamp for one the camera wrote — `source` is carried alongside
 * the values and is the whole point of the distinction.
 */
class ImageMetadata
{
    public function __construct(
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?int $gpsAccuracy = null,
        public readonly ?Carbon $capturedAt = null,
        public readonly ?string $deviceMake = null,
        public readonly ?string $deviceModel = null,
        public readonly ?string $deviceOs = null,
        public readonly ?string $appVersion = null,
        /** 'exif' | 'client' | 'none' */
        public readonly string $source = 'none',
        /** @var array<string, mixed>|null the raw block, minus binary payloads */
        public readonly ?array $exif = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Fill the gaps in this set from another.
     *
     * Used to layer client-supplied values under EXIF: the camera's own record
     * wins wherever it exists, and the device fills in what it stripped. The
     * source is downgraded to 'client' only when EXIF supplied nothing at all,
     * because a set that is half-camera is not fully trustworthy either.
     */
    public function mergedWith(self $fallback): self
    {
        $hadExif = $this->source === 'exif';

        return new self(
            latitude: $this->latitude ?? $fallback->latitude,
            longitude: $this->longitude ?? $fallback->longitude,
            gpsAccuracy: $this->gpsAccuracy ?? $fallback->gpsAccuracy,
            capturedAt: $this->capturedAt ?? $fallback->capturedAt,
            deviceMake: $this->deviceMake ?? $fallback->deviceMake,
            deviceModel: $this->deviceModel ?? $fallback->deviceModel,
            deviceOs: $this->deviceOs ?? $fallback->deviceOs,
            appVersion: $this->appVersion ?? $fallback->appVersion,
            source: $hadExif ? 'exif' : ($fallback->source === 'none' ? 'none' : 'client'),
            exif: $this->exif,
            width: $this->width ?? $fallback->width,
            height: $this->height ?? $fallback->height,
        );
    }

    /** @return array<string, mixed> the columns to persist. */
    public function toColumns(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'gps_accuracy' => $this->gpsAccuracy,
            'captured_at' => $this->capturedAt,
            'device_make' => $this->deviceMake,
            'device_model' => $this->deviceModel,
            'device_os' => $this->deviceOs,
            'app_version' => $this->appVersion,
            'metadata_source' => $this->source,
            'exif' => $this->exif,
        ];
    }

    /** A single readable line for the panel: "Samsung SM-A155F · Android 14". */
    public function deviceLabel(): ?string
    {
        $parts = array_filter([
            trim(($this->deviceMake ?? '').' '.($this->deviceModel ?? '')),
            $this->deviceOs,
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }
}
