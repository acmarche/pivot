<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Repository\HadesRepository;
use AcMarche\Pivot\Utils\Cache;
use Symfony\Component\String\Inflector\FrenchInflector;
use Symfony\Contracts\Cache\CacheInterface;
use VisitMarche\Theme\Inc\RouterHades;
use VisitMarche\Theme\Lib\WpRepository;

class HadesFiltres
{
    const COMMUNE = 263;
    const MARCHE = 134;
    const PAYS = 9;
    const HEBERGEMENTS_KEY = 'hebergements';
    const RESTAURATIONS_KEY = 'restaurations';
    const EVENEMENTS_KEY = 'evenements';
    const BOUGER_KEY = 'evenements';

    const EVENEMENTS = [
        'evt_sport',
        'cine_club',
        'conference',
        'exposition',
        'festival',
        'fete_festiv',
        'anim_jeux',
        'livre_conte',
        'manifestatio',
        'foire_brocan',
        'evt_promenad',
        'spectacle',
        'stage_ateli',
        'evt_vis_guid',
    ];

    const RESTAURATIONS = [
        'barbecue',
        'bar_vin',
        'brass_bistr',
        'cafe_bar',
        'foodtrucks',
        'pique_nique',
        'restaurant',
        'resto_rapide',
        'salon_degus',
        'traiteur',
    ];

    const HEBERGEMENTS = [
        //Hébergements de vacances
        'aire_motorho',
        'camping',
        'centre_vac',
        'village_vac',
        //Hébergements insolites
        'heb_insolite',
        //Chambres
        'chbre_chb',
        'chbre_hote',
        //Gites
        'git_ferme',
        'git_citad',
        'git_big_cap',
        'git_rural',
        'mbl_trm',
        'mbl_vac',
        'hotel',
    ];

    /**
     * @var array|object|null
     */
    public $filtres;
    /**
     * @var HadesRepository
     */
    private $hadesRepository;
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct()
    {
        $this->hadesRepository = new HadesRepository();
        $this->filtres = $this->hadesRepository->getFiltresHades();
        $this->cache = Cache::instance();
    }

    public function setCounts(): void
    {
        $this->cache->get(
            'visit_filtres'.time(),
            function () {
                foreach ($this->filtres as $category) {
                    $category->count = 0;
                    if ($category->category_id) {
                        $count = $this->hadesRepository->countOffres($category->category_id);
                        $category->count = $count;
                    }
                }
            }
        );
    }

    public function getFiltresNotEmpty(): array
    {
        $notEmpty = [];
        foreach ($this->filtres as $category) {
            if ($category->category_id) {
                if (isset($category->count) && $category->count > 0) {
                    $notEmpty[] = $category;
                }
            } else {
                $notEmpty[] = $category;
            }
        }

        return $notEmpty;
    }

    public function translateFiltres(array $filtres, string $language = 'fr'): array
    {
        $allFiltres = $this->hadesRepository->extractCategories($language);
        $data = [];
        foreach ($filtres as $filtre) {
            if (isset($allFiltres[$filtre])) {
                $data[$filtre] = $allFiltres[$filtre];
            }
        }

        //restaurant,barbecue,traiteur pluriels
        $inflector = new FrenchInflector();

        foreach ($data as $key => $value) {
            $result = $inflector->pluralize($value);
            $data[$key] = count($result) > 0 ? $result[0] : $value;
        }

        return $data;
    }

    public function getCategoryFilters(int $categoryId, string $language = 'fr'): array
    {
        $filtres = [];
        $filtresString = get_term_meta($categoryId, FiltreMetaBox::KEY_NAME_HADES, true);
        if ($filtresString) {
            $groupedFilters = self::groupedFilters();
            $filtres = $groupedFilters[$filtresString] ?? explode(',', $filtresString);
            $filtres = $this->translateFiltres($filtres, $language);
        }

        $wpRepository = new WpRepository();
        $children = $wpRepository->getChildrenOfCategory($categoryId);
        foreach ($children as $child) {
            $filtres[$child->cat_ID] = $child->name;
        }

        asort($filtres);

        return $filtres;
    }

    public static function groupedFilters(): array
    {
        return [
            self::HEBERGEMENTS_KEY => self::HEBERGEMENTS,
            self::RESTAURATIONS_KEY => self::RESTAURATIONS,
            self::EVENEMENTS_KEY => self::EVENEMENTS,
        ];
    }


}
