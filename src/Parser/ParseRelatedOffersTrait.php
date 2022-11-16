<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnList;

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
    public function parseRelatedOffers(Offre $offre): void
    {
        $docs2 = [];
        foreach ($offre->relOffre as $relation) {
            $codeCgt = $relation->offre['codeCgt'];
            try {
                $relatedOffer = $this->pivotRepository->fetchOffreByCgt($codeCgt, class: Offre::class);
            } catch (\Exception $exception) {
                continue;
            }
            if (!$relatedOffer instanceof Offre) {
                continue;
            }

            $this->specitificationsByOffre($relatedOffer);
            $docs2[] = $this->parseImages($relatedOffer);

            if ($relation->urn == UrnList::CONTACT_DIRECTION->value) {
                $offre->contact_direction = $relatedOffer;
            }
            if ($relation->urn === UrnList::POIS->value) {
                $offre->pois[] = $relatedOffer;
            }
            if ($relation->urn == UrnList::MEDIAS_AUTRE->value) {
                $offre->autres[] = $relatedOffer;
            }
            if ($relation->urn == UrnList::MEDIA_DEFAULT->value) {
                $offre->media_default = $relatedOffer;
            }
        }
        foreach ($docs2 as $doc) {
            foreach ($doc['images'] as $image) {
                $offre->images[] = $image;
            }
            foreach ($doc['documents'] as $document) {
                $offre->documents[] = $document;
            }
        }
    }

}