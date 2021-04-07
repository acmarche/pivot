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
    public $lvl;

    public function __construct()
    {
        $hadesRepository = new WpRepository();
        $this->categories = $hadesRepository->getCategoriesHades();
    }

    function initLvl()
    {
        $this->lvl = [];
        $this->lvl2 = [];
        $this->lvl3 = [];
    }

    function addLevel2(object $category)
    {
        $this->lvl2['name'] = $category->lvl2;
    }

    public function finishLvl()
    {
        $this->lvl['items'] = $this->lvl2;
    }

    public function addLevel3($category)
    {
        $this->lvl3[] = $category->lvl3;
    }

    public function finishLvl3() {
        $this->lvl2['items2'] = $this->lvl3;
        $this->lvl3=[];
    }

}
