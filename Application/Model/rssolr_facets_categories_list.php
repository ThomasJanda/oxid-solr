<?php

namespace rs\solr\Application\Model;

class rssolr_facets_categories_list extends \OxidEsales\Eshop\Core\Model\ListModel
{
    
    public function __construct()
    {
        parent::__construct(\rs\solr\Application\Model\rssolr_facets_categories::class);
    }
    
    public function ListByCategorie($f_oxcategories)
    {
        $oListObject = $this->getBaseObject();
        $sFieldList = $oListObject->getSelectFields();
        
        $sQ = "select $sFieldList 
        from " . $oListObject->getViewName() . "
        where f_oxcategories=?
        order by rssort";
        $this->selectString($sQ, [$f_oxcategories]);

        return $this;
    }
}
