<?php
namespace rs\solr\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Request;

class rs_solr_test extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{

    protected $_sThisTemplate="rs_solr_test.tpl";

    public function render() 
    {
        $sType = '';
        $sValue = '';
        $sArtCat = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("art_category");
        if ($sArtCat && strstr($sArtCat, "@@") !== false) {
            list($sType, $sValue) = explode("@@", $sArtCat);
        }
        $this->_aViewData["art_category"] = $sArtCat;
        $this->_aViewData["cattree"] = $this->getCategoryList($sType, $sValue);
        $this->_aViewData["mnftree"] = $this->getManufacturerlist($sType, $sValue);
        return parent::render();
    }
    public function getCategoryList($sType, $sValue)
    {
        /** @var \OxidEsales\Eshop\Application\Model\CategoryList $oCatTree parent category tree */
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $oCatTree->loadList();
        if ($sType === 'cat') {
            foreach ($oCatTree as $oCategory) {
                if ($oCategory->oxcategories__oxid->value == $sValue) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }

        return $oCatTree;
    }
    public function getManufacturerList($sType, $sValue)
    {
        $oMnfTree = oxNew(\OxidEsales\Eshop\Application\Model\ManufacturerList::class);
        $oMnfTree->loadManufacturerList();
        if ($sType === 'mnf') {
            foreach ($oMnfTree as $oManufacturer) {
                if ($oManufacturer->oxmanufacturers__oxid->value == $sValue) {
                    $oManufacturer->selected = 1;
                    break;
                }
            }
        }

        return $oMnfTree;
    }
    
   public function pingResult()
   {
       $result = \rs\solr\Core\solr_connector::ping();
       
       if($result===false)
           $result = "Can connect to server";
       else
           $result = "Server avaiable";
       
       return $result;
   }
   
   public function getObject($key, $id)
   {
       $id = trim($id);
       if($key=="oxcategories__oxid")
       {
           $o=oxnew('oxcategory');
           if($o->load($id))
           {
               return $o;
           }
       }
       if($key=="oxmanufacturers__oxid")
       {
           $o=oxnew('oxmanufacturer');
           if($o->load($id))
           {
               return $o;
           }
       }
       if($key=="oxarticles__oxid")
       {
           $o=oxnew('oxarticle');
           if($o->load($id))
           {
               return $o;
           }
       }
       return null;
   }
   
   public function search()
   {
        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        $oRequest = oxNew(\OxidEsales\Eshop\Core\Request::class);
        
        $sPhrase = $oRequest->getRequestParameter("phrase");
        $aFilter = $oRequest->getRequestParameter("filter");
        $aFilterSettings = $oRequest->getRequestParameter("filtersettings");    
        $aFilterSettingsType = $oRequest->getRequestParameter("filtersettingstype");    
        $sSort = $oRequest->getRequestParameter("sort","");
        $iRows = (int) $oRequest->getRequestParameter("rows",25);

        $sType = '';
        $sValue = '';
        $sArtCat = $oRequest->getRequestParameter("art_category");
        if ($sArtCat && strstr($sArtCat, "@@") !== false) {
            list($sType, $sValue) = explode("@@", $sArtCat);
        }
        
        
        $oSolrSearch = \rs\solr\Core\solr_connector::getSearch();
        $oSolrSearch->setSearchPhrase($sPhrase);
        $oSolrSearch->setSearchFilter($aFilter);
        $oSolrSearch->setLimit(0, $iRows);
        $oSolrSearch->setSortString($sSort);
        $oSolrSearch->setFacetFields($aFilterSettings);

        if($sType=="cat")
        {
            $oSolrSearch->setSearchFilterFixed('oxcategories__oxid', $sValue);
        }
        elseif($sType=="mnf")
        {
            $oSolrSearch->setSearchFilterFixed('oxmanufacturers__oxid', $sValue);
        }

        
        list($iFound,$aResult, $aFacets, $sQuery, $iPages, $sError)  = $oSolrSearch->execute();
        
        $this->_aViewData['resultQuery']=$sQuery;
        $this->_aViewData['result']=$aResult;
        $this->addTplParam("found", $iFound);
        $this->addTplParam('pages', $iPages);
        $this->addTplParam('phrase', $sPhrase);
        $this->addTplParam('filter', $aFilter);
        $this->addTplParam('sort', $sSort);
        $this->addTplParam('rows', $iRows);
        $this->_aViewData['facets']=$aFacets;
        $this->addTplParam('filtersettings', $aFilterSettings);
        $this->addTplParam('filtersettingstype', $aFilterSettingsType);
        $this->addTplParam('error', $sError);
   }

   public function getFilterSettingName($key)
   {
       $a = $this->getFilterSettings();
       return $a[$key];
   }
   
   
   public function getFilterSettings()
   {
       $oSolrImport = \rs\solr\Core\solr_connector::getImport();
       return $oSolrImport->getAllAttributesIds();
   }
}
