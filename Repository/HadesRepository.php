<?php


namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Offre;
use AcMarche\Pivot\Entities\OffreInterface;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Hades;
use AcMarche\Pivot\Utils\Cache;
use AcMarche\Pivot\Utils\Mailer;
use DOMDocument;
use DOMNodeList;
use Exception;
use Symfony\Contracts\Cache\CacheInterface;
use VisitMarche\Theme\Inc\RouterHades;
use VisitMarche\Theme\Lib\LocaleHelper;
use wpdb;

class HadesRepository
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var HadesRemoteRepository
     */
    private $hadesRemoteRepository;

    public function __construct()
    {
        $this->hadesRemoteRepository = new HadesRemoteRepository();
        $this->cache = Cache::instance();
    }

    /**
     * @param array $types
     *
     * @return OffreInterface[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffres(array $types = []): array
    {
        $xmlString = $this->hadesRemoteRepository->getOffres($types);
        if ($xmlString == null) {
            return [];
        }
        $domdoc = $this->loadXml($xmlString);
        if ($domdoc === null) {
            return [];
        }
        $data = $domdoc->getElementsByTagName('offres');
        $offresXml = $data->item(0);
        $offres = [];

        foreach ($offresXml->childNodes as $offre) {
            if ($offre->nodeType == XML_ELEMENT_NODE) {
                $offres[] = Offre::createFromDom($offre, $domdoc);
            }
        }

        return $offres;
    }

    /**
     * @param array $types
     *
     * @return OffreInterface[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getEvents(array $types = []): array
    {
        $types = count($types) === 0 ? array_keys(Hades::EVENEMENTS) : $types;

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
                $events = EventUtils::sortEvents($events);

                return $events;
            }
        );
    }

    public function getHebergements(array $types = []): array
    {
        $types = count($types) === 0 ? array_keys(Hades::HEBERGEMENTS) : $types;

        return $this->cache->get(
            'hebergement_hades',
            function () use ($types) {
                return $this->getOffres($types);
            }
        );
    }

    public function getRestaurations(array $types = []): array
    {
        $types = count($types) === 0 ? array_keys(Hades::RESTAURATIONS) : $types;

        return $this->cache->get(
            'resto_hades',
            function () use ($types) {
                return $this->getOffres($types);
            }
        );
    }

    /**
     * @param string $xmlString
     *
     * @return DOMDocument|null
     */
    public function loadXml(string $xmlString): ?DOMDocument
    {
        try {
            libxml_use_internal_errors(true);
            $domdoc = new DOMDocument();
            $domdoc->loadXML($xmlString);
            $errors = libxml_get_errors();

            libxml_clear_errors();
            if (count($errors) > 0) {
                $stingError = '';
                foreach ($errors as $error) {
                    if ($error->level != LIBXML_ERR_WARNING) {
                        $stingError .= $error->message;
                    }
                }
                global $wp;
                $url = home_url($wp->request);
                Mailer::sendError('xml error hades', 'error: '.$stingError.'contenu: '.$xmlString.' url '.$url);

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
                if ($xmlString === null) {
                    return null;
                }
                //  echo($xmlString);
                $domdoc = $this->loadXml($xmlString);
                if ($domdoc === null) {
                    return null;
                }
                $data = $domdoc->getElementsByTagName('offres');
                $offresXml = $data->item(0);

                foreach ($offresXml->childNodes as $offre) {
                    if ($offre->nodeType == XML_ELEMENT_NODE) {
                        return Offre::createFromDom($offre, $domdoc);
                    }
                }

                return null;
            }
        );
    }

    public function getOffresSameCategories(OffreInterface $offre, int $categoryWpId): ?array
    {
        $recommandations = [];
        $language = 'fr';
        if (class_exists(LocaleHelper::class)) {
            $language = LocaleHelper::getSelectedLanguage();
        }
        $categories = [];
        foreach ($offre->categories as $category) {
            $categories[] = $category->id;
        }
        $offres = $this->getOffres($categories);
        foreach ($offres as $item) {
            if ($offre->id == $item->id) {
                continue;
            }
            $url = RouterHades::getUrlOffre($item, $categoryWpId);
            $recommandations[] = [
                'title' => $item->getTitre($language),
                'url' => $url,
                'image' => $item->firstImage(),
                'categories' => $item->categories,
            ];
        }

        return $recommandations;
    }

    public function countOffres(string $category): ?int
    {
        return $this->cache->get(
            $category,
            function () use ($category) {
                $xmlString = $this->hadesRemoteRepository->loadOffres(['cat_id' => $category], 'digest');
                if ($xmlString === null) {
                    return null;
                }
                $domdoc = $this->loadXml($xmlString);
                if ($domdoc === null) {
                    return null;
                }
                $data = $domdoc->getElementsByTagName('tot');
                if (!$data instanceof DOMNodeList) {
                    return null;
                }
                $totDom = $data->item(0);
                if ($totDom instanceof \DOMElement) {
                    return $totDom->nodeValue;
                }
                $data = $domdoc->getElementsByTagName('nb_offres');
                if (!$data instanceof DOMNodeList) {
                    return null;
                }
                $totDom = $data->item(0);
                if ($totDom instanceof \DOMElement) {
                    return $totDom->nodeValue;
                }

                return null;
            }
        );
    }

    public function extractCategories(string $language)
    {
        return $this->cache->get(
            'hades_categories_'.$language,
            function () use ($language) {

                $categories = [];
                foreach ($this->getOffres() as $offre) {
                    foreach ($offre->categories as $category) {
                        $categories[$category->id] = $category->getLib($language);
                    }
                }
                asort($categories);

                return $categories;
            }
        );
    }

    public function getCategoriesHades()
    {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM hades");
    }
}
