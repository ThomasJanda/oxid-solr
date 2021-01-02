<?php

namespace rs\solr\Application\Model;


class rssolr_facets_categories extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    protected $_sClassName = 'rssolr_facets_categories';

    public function __construct()
    {
        parent::__construct();
        $this->init('rssolr_facets_categories');
    }
    
    
    public function loadByCategorieFacete($foxcategorie, $facete)
    {
        $sSql="select oxid from rssolr_facets_categories where f_oxcategories=? and rsfacete=?";
        $oxid = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getOne($sSql, [$foxcategorie, $facete]);
        
        return $this->load($oxid);
    }
}
