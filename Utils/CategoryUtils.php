<?php


namespace AcMarche\Pivot\Utils;


use AcMarche\Pivot\Repository\HadesRepository;
use Symfony\Contracts\Cache\CacheInterface;
use VisitMarche\Theme\Lib\WpRepository;

class CategoryUtils
{
    /**
     * @var array|object|null
     */
    public $categories;
    /**
     * @var WpRepository
     */
    private $wpRepository;
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
        $this->wpRepository = new WpRepository();
        $this->hadesRepository = new HadesRepository();
        $this->categories = $this->wpRepository->getCategoriesHades();
        $this->cache = Cache::instance();
    }

    public function setCounts(): void
    {
        $this->cache->get(
            'visit_categories',
            function () {
                foreach ($this->categories as $category) {
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
                continue;
            }
            $notEmpty[] = $category;
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

}
