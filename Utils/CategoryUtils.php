<?php


namespace AcMarche\Pivot\Utils;


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

    public function __construct()
    {
        $hadesRepository = new WpRepository();
        $this->categories = $hadesRepository->getCategoriesHades();
        $this->tree = [];
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
        $this->lvl2[] = $category->lvl2;
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
