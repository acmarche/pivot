<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class OfferResponse
{
    /** @var Offer[] */
    public array $offre = [];

    public function __construct(
        public int $count = 0,
        public ?int $realCount = null,
        public ?int $itemsPerPage = null,
        public ?int $pagesCount = null,
        public ?string $token = null,
    ) {}

    public function addOffre(Offer $offer): void
    {
        $this->offre[] = $offer;
    }

    /**
     * @return Offer[]
     */
    public function getOffers(): array
    {
        return $this->offre;
    }

    public function getFirstOffer(): ?Offer
    {
        return $this->offre[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    public function hasMorePages(): bool
    {
        return $this->pagesCount !== null && $this->pagesCount > 1;
    }
}
