<?php


namespace AcMarche\Pivot\Utils;


use AcMarche\Pivot\Repository\HadesRepository;
use VisitMarche\Theme\Lib\WpRepository;

class CategoryUtils
{
    /**
     * @var array|object|null
     */
    public $categories;
    /**
     * @var array
     */
    public $lvl2;
    /**
     * @var array
     */
    public $lvl3;
    /**
     * @var array
     */
    public $root;
    /**
     * @var array
     */
    public $lvl4;
    /**
     * @var array
     */
    public $tree;
    /**
     * @var WpRepository
     */
    private $wpRepository;
    /**
     * @var HadesRepository
     */
    private $hadesRepository;
    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private $cache;

    public function __construct()
    {
        $this->wpRepository = new WpRepository();
        $this->hadesRepository = new HadesRepository();
        $this->categories = $this->wpRepository->getCategoriesHades();
        $this->tree = [];
        $this->cache = Cache::instance();
    }

    public function setCounts(): void
    {
        $this->cache->get(
            'visit_categories'.time(),
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

    public function getNameByKey(string $key): string
    {
        foreach ($this->categories as $category) {

            if ($category->category_id == $key) {
                if ($category->lvl1) {
                    return $category->lvl1;
                }
                if ($category->lvl2) {
                    return $category->lvl2;
                }
                if ($category->lvl3) {
                    return $category->lvl3;
                }
                if ($category->lvl4) {
                    return $category->lvl4;
                }
            }
        }

        return $key;
    }

    public function getNamesByKey(array $keys): array
    {
        $names = [];
        foreach ($keys as $key) {
            $names[] = $this->getNameByKey($key);
        }

        return $names;
    }

    function initLvl()
    {
        $this->lvl2 = [];
        $this->lvl3 = [];
        $this->lvl4 = [];
    }

    function initLvl1(object $category)
    {
        $this->root = [];
        $this->root['name'] = $category->lvl1;
        $this->lvl2 = [];
    }

    function addLevel2(object $category)
    {
        $this->lvl2['name'] = $category->lvl2;
    }

    public function addLevel3($category)
    {
        $this->lvl3[] = $category->lvl3;
    }

    public function finishLvl3()
    {
        $this->lvl2['items2'] = $this->lvl3;
        $this->lvl3 = [];
    }

    public function addItem(object $category, ?object $parent, int $level)
    {
        if ($parent == null) {
            $this->tree[] = $category;
        }
    }
}
