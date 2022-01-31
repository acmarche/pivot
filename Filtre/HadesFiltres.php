<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Repository\HadesRepository;

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

    public function __construct()
    {
        $this->hadesRepository = new HadesRepository();
        $this->filtres = $this->hadesRepository->getAllFiltersFromDb();
    }

    public function translateFiltres(array $keywords, string $language = 'fr'): array
    {
        $data = [];
        $fieldName = 'name_'.$language;
        foreach ($keywords as $keyword) {
            if ($filter = $this->hadesRepository->findFilterByKeyword($keyword)) {
                $value = $filter->$fieldName;
                if (!$value) {
                    $value = $filter->name_original;
                }
                $data[$keyword] = $value;
            }
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
