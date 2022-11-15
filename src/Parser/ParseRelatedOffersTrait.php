<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Document;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnList;
use Symfony\Component\String\UnicodeString;

trait ParseRelatedOffersTrait
{
    /**
     * @required
     */
    public PivotRepository $pivotRepository;

    /**
     * @param Offre[] $offres
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function parseRelatedOffers(Offre $offre): void
    {
        foreach ($offre->relOffre as $relation) {
            $item = $relation->offre;
            $code = $item['codeCgt'];
            try {
                $relatedOffer = $this->pivotRepository->getOffreByCgt($code, class: Offre::class);
            } catch (\Exception $exception) {
                continue;
            }
            if (!$relatedOffer) {
                continue;
            }
            //todo
          //  $this->specitificationsByOffre($relatedOffer);
            $specificationMedias = $this->findByUrn($relatedOffer, UrnList::URL->value);
            foreach ($specificationMedias as $specificationMedia) {
                $value = str_replace("http:", "https:", $specificationMedia->data->value);
                $string = new UnicodeString($value);
                $extension = $string->slice(-3);
                $document = new Document();
                $document->extension = $extension;
                $document->url = $value;

                if (in_array($extension, ['jpg', 'png'])) {
                    $offre->images[] = $value;
                } else {
                    $offre->documents[] = $document;
                }
            }

            $specificationImages = $this->findByUrn($relatedOffer, UrnList::MEDIAS_PARTIAL->value, contains: true);
            foreach ($specificationImages as $specificationImage) {
                $value = str_replace("http:", "https:", $specificationImage->data->value);
                $offre->images[] = $value;
            }
            if (count($offre->images) > 0) {
                $offre->image = $offre->images[0];
            }

            if ($relation->urn == UrnList::CONTACT_DIRECTION->value) {
                if (isset($relatedOffer->offre[0])) {
                    $offre->contact_direction = $relatedOffer->offre[0];
                }
            }
            if ($relation->urn === UrnList::POIS->value) {
                if (isset($relatedOffer->offre[0])) {
                    $offre->pois[] = $relatedOffer->offre[0];
                }
            }
            if ($relation->urn == UrnList::MEDIAS_AUTRE->value) {
                if (isset($relatedOffer->offre[0])) {
                    $offre->autres[] = $relatedOffer->offre[0];
                }
            }
            if ($relation->urn == UrnList::MEDIA_DEFAULT->value) {
                if (isset($relatedOffer->offre[0])) {
                    $offre->media_default = $relatedOffer->offre[0];
                }
            }
        }
    }

}