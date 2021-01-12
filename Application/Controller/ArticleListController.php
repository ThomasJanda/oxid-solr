<?php

namespace rs\solr\Application\Controller;

class ArticleListController extends ArticleListController_parent
{
    
    /**
     * need information for custom templates
     * @param type $name
     * @return type
     */
    public function getSolrFilterSetting($name)
    {
        /**
         * @var rs\solr\Application\Model\rssolr_facets_categories $oFacetDefinition
         */
        $oFacetDefinition = oxNew(\rs\solr\Application\Model\rssolr_facets_categories::class);
        $oFacetDefinition->loadByCategorieFacete($this->getCategoryId(), $name);
        return $oFacetDefinition;
    }

    
    protected $_getSolrFilterSettingName=null;
    /**
     * translate ids to human readable names
     * @param type $key
     * @return type
     */
    public function getSolrFilterSettingName($key)
    {
        if($this->_getSolrFilterSettingName===null)
        {
            $oSolrImport = \rs\solr\Core\solr_connector::getImport();
            $this->_getSolrFilterSettingName = $oSolrImport->getAllAttributesIds();
        }
        return $this->_getSolrFilterSettingName[$key];
    }
    

    
    
    
    
    /**
     * Loads and returns article list of active category.
     *
     * @param Category $category category object
     *
     * @return oxArticleList
     * @deprecated underscore prefix violates PSR12, will be renamed to "loadArticles" in next major
     */
    protected function _loadArticles($category) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $config = $this->getConfig();
        $numberOfCategoryArticles = (int) $config->getConfigParam('iNrofCatArticles');
        $numberOfCategoryArticles = $numberOfCategoryArticles ? $numberOfCategoryArticles : 1;

        $iPgNr = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgnr');
        $iPgNr = ($iPgNr < 0) ? 0 : $iPgNr;

        $aFilter=\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('filter');
        if(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('filterclear')==="1")
            $aFilter = null;
        
        $aFacetList = oxNew(\rs\solr\Application\Model\rssolr_facets_categories_list::class);
        $aFacetList->ListByCategorie($category->getId());
        $aFilterSettingsType=[];
        foreach($aFacetList as $oFacet)
        {
            $aFilterSettingsType[$oFacet->rssolr_facets_categories__rsfacete->value] = $oFacet->rssolr_facets_categories__rstype->value;
        }
        $aFilterSettings=array_keys($aFilterSettingsType);

        $sSort = $this->getSortingSql($this->getSortIdent());

        $oSolrSearch = \rs\solr\Core\solr_connector::getSearch();
        $oSolrSearch->setSearchFilter($aFilter);
        $oSolrSearch->setLimit($numberOfCategoryArticles * $iPgNr, $numberOfCategoryArticles);
        if($sSort!="")
            $oSolrSearch->setSortString($sSort);
        
        if(!empty($aFilterSettings))
            $oSolrSearch->setFacetFields($aFilterSettings);

        $oSolrSearch->setSearchFilterFixed('oxcategories__oxid', $category->getId());
        
        list($iFound,$aResult, $aFacets, $sQuery, $iPages, $sError)  = $oSolrSearch->execute();
        
        $this->_iAllArtCnt = $iFound;
        $this->_iCntPages = ceil($this->_iAllArtCnt / $numberOfCategoryArticles);

        // load only articles which we show on screen
        /** @var \OxidEsales\Eshop\Application\Model\ArticleList|\rs\solr\Application\Model\ArticleList $articleList */
        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->loadIdsAndSort($aResult);
        $oArtList->setSolrFacets($aFacets);
        $oArtList->setSolrFilter($aFilter);
        $oArtList->setSolrFilterSettings($aFilterSettings);
        $oArtList->setSolrFilterSettingsType($aFilterSettingsType);
        $this->addTplParam("facets", $oArtList->getSolrFacets());
        $this->addTplParam('filter', $oArtList->getSolrFilter());
        $this->addTplParam("hasfilterset", $oArtList->hasSolrFilterSet());
        $this->addTplParam('filtersettings', $oArtList->getSolrFilterSettings());
        $this->addTplParam('filtersettingstype', $oArtList->getSolrFilterSettingsType());
        
        return $oArtList;
    }



    /* bug in oxid */
    public function getActPage()
    {
        if ($this->_iActPage === null) {
            $tmp = $this->getConfig()->getRequestParameter('pgnr');
            if($tmp=="")
                $this->getConfig()->getRequestParameter('pgNr');
            $this->_iActPage = (int) $tmp;
            $this->_iActPage = ($this->_iActPage < 0) ? 0 : $this->_iActPage;
        }

        return $this->_iActPage;
    }
    
    
    
#region "sort"
    public function getSortColumns()
    {
        parent::getSortColumns();
        $this->_aSortColumns = \rs\solr\Core\solr_connector::OxidSortColumns();
        return $this->_aSortColumns;
    }
    private function isAllowedSortingOrder($sortOrder)
    {
        $allowedSortOrders = array_merge((new \OxidEsales\EshopCommunity\Core\SortingValidator())->getSortingOrders(), ['']);
        return in_array(strtolower($sortOrder), $allowedSortOrders);
    }
    public function getSortingSql($ident)
    {
        $sorting = $this->getSorting($ident);
        if (is_array($sorting)) {
            $sortDir = isset($sorting['sortdir']) ? $sorting['sortdir'] : '';
            if ($this->isAllowedSortingOrder($sortDir)) {
                $sortBy = $sorting['sortby'];
                return trim($sortBy . '@@' . $sortDir);
            }
        }
    }
    public function getSorting($sortIdent)
    {
        $sorting = null;

        if ($sorting = $this->getUserSelectedSorting()) {
            $this->setItemSorting($sortIdent, $sorting['sortby'], $sorting['sortdir']);
        } elseif (!$sorting = $this->getSavedSorting($sortIdent)) {
            $sorting = $this->getDefaultSorting();
        }

        if ($sorting) {
            $this->setListOrderBy($sorting['sortby']);
            $this->setListOrderDirection($sorting['sortdir']);
        }

        return $sorting;
    }
    public function getUserSelectedSorting()
    {
        $request = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Request::class);
        $sortBy = $request->getRequestParameter($this->getSortOrderByParameterName());
        $sortOrder = $request->getRequestParameter($this->getSortOrderParameterName());

        if ((new \rs\solr\Core\SortingValidator())->isValid($sortBy, $sortOrder)) {
            return ['sortby' => $sortBy, 'sortdir' => $sortOrder];
        }
    }
#endregion
    
}