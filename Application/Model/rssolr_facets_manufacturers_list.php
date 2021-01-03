<?php

namespace rs\solr\Application\Model;

class rssolr_facets_manufacturers_list extends \OxidEsales\Eshop\Core\Model\ListModel
{
    
    public function __construct()
    {
        parent::__construct(\rs\solr\Application\Model\rssolr_facets_manufacturers::class);
    }
    
    public function ListByManufacturer($f_oxmanufacturer)
    {
        $oListObject = $this->getBaseObject();
        $sFieldList = $oListObject->getSelectFields();
        
        $sQ = "select $sFieldList 
        from " . $oListObject->getViewName() . "
        where f_oxmanufacturer=?
        order by rssort";
        $this->selectString($sQ, [$f_oxmanufacturer]);

        return $this;
    }
}
