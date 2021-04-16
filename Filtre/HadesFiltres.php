<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Repository\HadesRepository;
use AcMarche\Pivot\Utils\Cache;
use Symfony\Contracts\Cache\CacheInterface;

class HadesFiltres
{
    const COMMUNE = 263;
    const MARCHE = 134;
    const PAYS = 9;
    const HEBERGEMENTS_KEY = 'hebergements';
    const RESTAURATIONS_KEY = 'resaurations';
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
    public $categories;
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
        $this->categories = $this->hadesRepository->getCategoriesHades();
        $this->cache = Cache::instance();
    }

    public function setCounts(): void
    {
        $this->cache->get(
            'visit_categories'.time(),
            function () {
                foreach ($this->categories as $category) {
                    $category->count = 0;
                    if ($category->category_id) {
                        $count = $this->hadesRepository->countOffres($category->category_id);
                        $category->count = $count;
                    }
                }
            }
        );
    }

    public function getCategoriesNotEmpty(): array
    {
        $notEmpty = [];
        foreach ($this->categories as $category) {
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
        $allCategories = $this->hadesRepository->extractCategories($language);
        foreach ($filtres as $key => $filtre) {
            if (isset($allCategories[$key])) {
                $filtres[$key] = $allCategories[$key];
            }
        }

        return $filtres;
    }

    public function getCategoryFilters(int $categoryId, string $language = 'fr'): array
    {
        $filtresString = get_term_meta($categoryId, CategoryMetaBox::KEY_NAME_HADES, true);
        if (!$filtresString) {
            return [];
        }

        $all = self::groupedFilters();
        if (isset($all[$filtresString])) {
            $filtres = $all[$filtresString];
        } else {
            $filtres = explode(',', $filtresString);
            $filtres = array_combine($filtres, $filtres);
        }

        $filtres = $this->translateFiltres($filtres, $language);

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
