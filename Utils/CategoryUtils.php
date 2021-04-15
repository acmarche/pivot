<?php


namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Hades;
use AcMarche\Pivot\Repository\HadesRepository;
use Symfony\Contracts\Cache\CacheInterface;
use VisitMarche\Theme\Inc\CategoryMetaBox;
use VisitMarche\Theme\Lib\LocaleHelper;
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
            'visit_categories22'.time(),
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

    public function getFiltresCategory(int $categoryId): array
    {
        $filtresString = get_term_meta($categoryId, CategoryMetaBox::KEY_NAME_HADES, true);
        if (!$filtresString) {
            return [];
        }

        $all = Hades::allCategories();
        if (isset($all[$filtresString])) {
            $filtres = $all[$filtresString];
        } else {
            $filtres = explode(',', $filtresString);
            $filtres = array_combine($filtres, $filtres);
        }

        $language = LocaleHelper::getSelectedLanguage();
        $filtres = $this->translateFiltres($filtres, $language);

        return $filtres;
    }

}
