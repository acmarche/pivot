<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Repository\HadesRepository;
use AcMarche\Pivot\Utils\Cache;
use Symfony\Component\String\Inflector\FrenchInflector;
use Symfony\Contracts\Cache\CacheInterface;

class HadesFiltres
{
    public const COMMUNE = 263;
    public const MARCHE = 134;
    public const PAYS = 9;
    public const HEBERGEMENTS_KEY = 'hebergements';
    public const RESTAURATIONS_KEY = 'restaurations';
    public const EVENEMENTS_KEY = 'evenements';
    public const BOUGER_KEY = 'evenements';

    public const EVENEMENTS = [
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

    public const RESTAURATIONS = [
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

    public const HEBERGEMENTS = [
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

    public array|object|null $filtres;
    private HadesRepository $hadesRepository;
    private CacheInterface $cache;

    public function __construct()
    {
        $this->hadesRepository = new HadesRepository();
        $this->filtres = $this->hadesRepository->getFiltresHades();
        $this->cache = Cache::instance();
    }

    public function setCounts(): void
    {
        $this->cache->get(
            'visit_filtres',
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
                if (property_exists($category, 'count') && null !== $category->count && $category->count > 0) {
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
            if ($pluriel = $this->particularPluriels($value)) {
                $data[$key] = $pluriel;
                continue;
            }
            $textes = explode(' ', $value);
            $join = [];
            foreach ($textes as $text) {
                if (\strlen($text) > 1) {
                    $result = $inflector->pluralize($text);
                    $join[] = [] !== $result ? $result[0] : $text;
                } else {
                    $join[] = $text;
                }
            }
            $join = implode(' ', $join);
            $data[$key] = $join;
        }

        return $data;
    }

    public static function groupedFilters(): array
    {
        return [
            self::HEBERGEMENTS_KEY => self::HEBERGEMENTS,
            self::RESTAURATIONS_KEY => self::RESTAURATIONS,
            self::EVENEMENTS_KEY => self::EVENEMENTS,
        ];
    }

    private function particularPluriels(string $mot): ?string
    {
        return match ($mot) {
            'Salons de dégustation' => $mot,
            'Restauration rapide' => $mot,
            'Bars à vins' => $mot,
            'Gîte à la ferme' => 'Gîtes à la ferme',
            'Terrain de camp' => 'Terrains de camp',
            'Meublé de tourisme' => 'Meublés de tourisme',
            'Meublés de vacances' => 'Meublés de vacances',
            'Autre hébergement non reconnu' => 'Autres hébergements non reconnus',
            default => null,
        };
    }
}
