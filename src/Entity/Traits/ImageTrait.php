<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Image;

trait ImageTrait
{
    /** @var Image[] */
    public array $images = [];

    /** @var Image[] Non-image media files (GPX, PDF, etc.) */
    public array $documents = [];

    public function addImage(Image $image): void
    {
        $this->images[] = $image;
    }

    public function addDocument(Image $image): void
    {
        $this->documents[] = $image;
    }

    public function getFirstImage(): ?Image
    {
        if ($image = $this->getDefaultImage()) {
            return $image;
        }

        return $this->images[0] ?? null;
    }

    public function getDefaultImage(): ?Image
    {
        foreach ($this->images as $image) {
            if ($image->isDefault) {
                return $image;
            }
        }

        return $this->images[0] ?? null;
    }

    /**
     * @return Image[]
     */
    public function getGpxFiles(): array
    {
        return array_filter($this->documents, function (Image $doc) {
            $path = parse_url($doc->url ?? '', PHP_URL_PATH);

            return $path && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'gpx';
        });
    }

    /**
     * @return string[]
     */
    public function getImageUrls(): array
    {
        return array_filter(array_map(fn(Image $image) => $image->url, $this->images));
    }
}
