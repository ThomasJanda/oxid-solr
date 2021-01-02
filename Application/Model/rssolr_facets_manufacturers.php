<?php

namespace rs\solr\Application\Model;


class rssolr_facets_manufacturers extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    protected $_sClassName = 'rssolr_facets_manufacturers';

    public function __construct()
    {
        parent::__construct();
        $this->init('rssolr_facets_manufacturers');
    }
    
    
    public function loadByCategorieFacete($foxcategorie, $facete)
    {
        $sSql="select oxid from rssolr_facets_manufacturers where f_oxmanufacturer=? and rsfacete=?";
        $oxid = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getOne($sSql, [$foxcategorie, $facete]);
        
        return $this->load($oxid);
    }
}
