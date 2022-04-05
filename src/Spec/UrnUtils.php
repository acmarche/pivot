<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\Response\UrnResponse;
use AcMarche\Pivot\Entities\Pivot\UrnDefinition;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class UrnUtils
{
    use SpecTrait;

    /**
     * @var array|UrnDefinition[]
     */
    public array $urns = [];

    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private SerializerInterface $serializer,
        private CacheInterface $cache
    ) {
    }

    /**
     * @return array|UrnDefinition[]
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function loadAll(): array
    {
        return $this->cache->get('54alkllUrnsdsd789', function () {
            $data = $this->pivotRemoteRepository->getThesaurus('urn');
            $urnResponse = $this->serializer->deserialize($data, UrnResponse::class, 'json');

            return $urnResponse->spec;
        });
    }

    public function getInfosUrn(string $urnKey, bool $value = false): ?UrnDefinition
    {
        if (count($this->urns) === 0) {
            $this->urns = $this->loadAll();
        }

        foreach ($this->urns as $urn) {
            if ($urn->urn === $urnKey) {
                if ($value) {
                    return $urn->value;
                }

                return $urn;
            }
        }

        return null;

    }

}
