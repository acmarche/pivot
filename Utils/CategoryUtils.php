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

    function initLvl(object $category)
    {
        $this->lvl = [];
        $this->lvl['name'] = $category->lvl1;
        $this->lvl2 = [];
        $this->lvl3 = [];
    }

    function addLevel2(object $category)
    {
        $this->lvl2[] = $category->lvl2;
    }

    public function finishLvl(): array
    {
        $this->lvl['items'] = $this->lvl2;

        return $this->lvl;
    }

    public function addLevel3($category)
    {
        $this->lvl3[] = $category->lvl3;
    }

    public function finishLevle3() {
        $this->lvl2['items'] = $this->lvl3;
        $this->lvl3=[];
    }

}
