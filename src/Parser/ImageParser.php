<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\Image;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\RelatedOffer;

class ImageParser
{
    private const array IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'];

    public function parseForOffer(Offer $offer): void
    {
        foreach ($offer->relOffre as $relOffer) {
            if (!$relOffer->isMediaRelation() || $relOffer->offre === null) {
                continue;
            }

            $image = $this->extractImage($relOffer);
            if ($image === null) {
                continue;
            }

            if ($this->isImageUrl($image->url)) {
                $offer->addImage($image);
            } else {
                $offer->addDocument($image);
            }
        }
    }

    private function isImageUrl(?string $url): bool
    {
        if ($url === null) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if ($path === null || $path === false) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::IMAGE_EXTENSIONS, true);
    }

    /**
     * @param Offer[] $offers
     */
    public function parseForOffers(array $offers): void
    {
        foreach ($offers as $offer) {
            $this->parseForOffer($offer);
        }
    }

    /**
     * @return string[]
     */
    public function extractAllImageUrls(Offer $offer): array
    {
        $urls = [];

        foreach ($offer->relOffre as $relOffer) {
            if (!$relOffer->isMediaRelation() || $relOffer->offre === null) {
                continue;
            }

            $url = $this->findSpecValue($relOffer->offre, 'urn:fld:url');
            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    private function extractImage(RelatedOffer $relOffer): ?Image
    {
        $mediaOffer = $relOffer->offre;
        $url = $this->findSpecValue($mediaOffer, 'urn:fld:url');

        if ($url === null) {
            return null;
        }

        return new Image(
            url: $url,
            codeCgt: $mediaOffer->codeCgt,
            name: $mediaOffer->nom,
            copyright: $this->findSpecValue($mediaOffer, 'urn:fld:copyr'),
            isDefault: $relOffer->urn === 'urn:lnk:media:defaut',
        );
    }

    private function findSpecValue(Offer $offer, string $urn): ?string
    {
        return $offer->getSpecValue($urn);
    }
}
