<?php
namespace rs\solr\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Request;

class rs_solr_import extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    protected $_sThisTemplate="rs_solr_import.tpl";
    
    public function deleteAll()
    {
        \rs\solr\Core\solr_connector::deleteAll();
    }
    
    public function setup()
    {
        $oSolrImport = \rs\solr\Core\solr_connector::getImport();
        $oSolrImport->setup();
    }
    
    public function import()
    {
        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        $oRequest = oxNew(\OxidEsales\Eshop\Core\Request::class);
        
        $iStart = $oRequest->getRequestParameter("offset",0);
        $iOffset = 30;
        
        $oSolrImport = \rs\solr\Core\solr_connector::getImport();
        if($iStart===0)
            $oSolrImport->deleteAll();
        
        $bContinue = $oSolrImport->import($iStart, $iOffset);
        
        $this->addTplParam("continue", $bContinue);
        $this->addTplParam("offset", $iStart + $iOffset);
    }
    
    
}