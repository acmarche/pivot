<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Offre;
use AcMarche\Pivot\Entities\OffreInterface;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Filtre\HadesFiltres;
use AcMarche\Pivot\Utils\Cache;
use AcMarche\Pivot\Utils\Mailer;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class HadesRepository
{
    private CacheInterface $cache;
    private HadesRemoteRepository $hadesRemoteRepository;

    public function __construct()
    {
        $this->hadesRemoteRepository = new HadesRemoteRepository();
        $this->cache = Cache::instance();
    }

    /**
     * @return OffreInterface[]
     *
     * @throws InvalidArgumentException
     */
    public function getOffres(array $types = [], bool $onlyFilters = false): array
    {
        $xmlString = $this->hadesRemoteRepository->getOffres($types);
        if (null === $xmlString) {
            return [];
        }
        $domdoc = $this->loadXml($xmlString);
        if (!$domdoc instanceof \DOMDocument) {
            return [];
        }
        $data = $domdoc->getElementsByTagName('offres');
        $offresXml = $data->item(0);
        $offres = [];
        foreach ($offresXml->childNodes as $offre) {
            if (XML_ELEMENT_NODE === $offre->nodeType) {
                if ($onlyFilters) {
                    $offres[] = Offre::createFromDomForFilters($offre, $domdoc);
                } else {
                    $offres[] = Offre::createFromDom($offre, $domdoc);
                }
            }
        }

        return $offres;
    }

    /**
     * @return OffreInterface[]
     *
     * @throws InvalidArgumentException
     */
    public function getEvents(array $types = []): array
    {
        $types = [] === $types ? HadesFiltres::EVENEMENTS : $types;

        return $this->cache->get(
            'events_hades',
            function () use ($types) {
                $events = [];
                $offres = $this->getOffres($types);
                foreach ($offres as $offre) {
                    EventUtils::sortDates($offre);
                    if (EventUtils::isEventObsolete($offre)) {
                        continue;
                    }
                    $events[] = $offre;
                }

                return EventUtils::sortEvents($events);
            }
        );
    }

    public function getHebergements(array $types = []): array
    {
        $types = [] === $types ? HadesFiltres::HEBERGEMENTS : $types;

        return $this->cache->get(
            'hebergement_hades',
            fn() => $this->getOffres($types)
        );
    }

    public function getRestaurations(array $types = []): array
    {
        $types = [] === $types ? HadesFiltres::RESTAURATIONS : $types;

        return $this->cache->get(
            'resto_hades',
            fn() => $this->getOffres($types)
        );
    }

    public function loadXml(string $xmlString): ?DOMDocument
    {
        try {
            libxml_use_internal_errors(true);
            $domdoc = new DOMDocument();
            $domdoc->loadXML($xmlString);
            $errors = libxml_get_errors();

            libxml_clear_errors();
            if ([] !== $errors) {
                $stingError = '';
                foreach ($errors as $error) {
                    //symbole &
                    if (68 === $error->code) {
                        continue;
                    }
                    if ($error->level > LIBXML_ERR_WARNING) {
                        $stingError .= $error->message.' code '.$error->code.' line '.$error->line.' col '.$error->column;
                    }
                }
                if (\strlen($stingError) > 0) {
                    global $wp;
                    $url = home_url($wp->request);
                    Mailer::sendError('xml error hades', $url.' error: '.$stingError.'contenu: '.$xmlString);
                }

                return null;
            }

            return $domdoc;
        } catch (Exception $exception) {
            Mailer::sendError('Erreur avec le xml hades', $exception->getMessage());

            return null;
        }
    }

    public function getOffre(string $id): ?OffreInterface
    {
        return $this->cache->get(
            'offre_hades-'.$id,
            function () use ($id) {
                $xmlString = $this->hadesRemoteRepository->getOffreById($id);

                if (null === $xmlString) {
                    return null;
                }
                //  echo($xmlString);
                $domdoc = $this->loadXml($xmlString);
                if (!$domdoc instanceof \DOMDocument) {
                    return null;
                }
                $data = $domdoc->getElementsByTagName('offres');
                $offresXml = $data->item(0);

                foreach ($offresXml->childNodes as $offre) {
                    if (XML_ELEMENT_NODE === $offre->nodeType) {
                        return Offre::createFromDom($offre, $domdoc);
                    }
                }

                return null;
            }
        );
    }

    public function getOffreWithChildrenAndParents(string $id): ?OffreInterface
    {
        $offre = $this->getOffre($id);
        if (null !== $offre) {
            $this->setParentsAndChildren($offre);
        }

        return $offre;
    }

    public function setParentsAndChildren(Offre $offre): void
    {
        foreach ($offre->enfantIds as $enfantId) {
            if (($enfant = $this->getOffre($enfantId)) !== null) {
                $offre->enfants[] = $enfant;
            }
        }
        foreach ($offre->parentIds as $parentId) {
            if (($parent = $this->getOffre($parentId)) !== null) {
                $offre->parents[] = $parent;
            }
        }
    }

    /**
     * @param int $offre
     * @param string $language
     *
     * @return OffreInterface[]|null
     *
     * @throws InvalidArgumentException
     */
    public function getOffresSameCategories(OffreInterface $offre): array
    {
        $categories = [];
        foreach ($offre->categories as $category) {
            $categories[] = $category->id;
        }

        return $this->getOffres($categories);
    }

    public function countOffres(string $category): ?int
    {
        return $this->cache->get(
            $category,
            function () use ($category) {
                $xmlString = $this->hadesRemoteRepository->loadOffres([
                    'cat_id' => $category,
                ], 'digest');
                if (null === $xmlString) {
                    return null;
                }
                $domdoc = $this->loadXml($xmlString);
                if (!$domdoc instanceof \DOMDocument) {
                    return null;
                }
                $data = $domdoc->getElementsByTagName('tot');
                if (!$data instanceof DOMNodeList) {
                    return null;
                }
                $totDom = $data->item(0);
                if ($totDom instanceof DOMElement) {
                    return $totDom->nodeValue;
                }
                $data = $domdoc->getElementsByTagName('nb_offres');
                if (!$data instanceof DOMNodeList) {
                    return null;
                }
                $totDom = $data->item(0);
                if ($totDom instanceof DOMElement) {
                    return $totDom->nodeValue;
                }

                return null;
            }
        );
    }

    /**
     * @return array|string[]
     *
     * @throws InvalidArgumentException
     */
    public function extractCategories(string $language): array
    {
        return $this->cache->get(
            'hades_categories_'.$language,
            function () use ($language) {
                $categories = [];
                foreach ($this->getOffres([], true) as $offre) {
                    foreach ($offre->categories as $category) {
                        $categories[$category->id] = $category->getLib($language);
                    }
                }
                asort($categories);

                return $categories;
            }
        );
    }

    public function getFiltresHades()
    {
        /*
         * @var wpdb $wpdb
         */
        global $wpdb;

        return $wpdb->get_results('SELECT * FROM hades');
    }
}
