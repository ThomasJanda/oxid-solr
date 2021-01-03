<?php

namespace rs\solr\Application\Model;

class Search extends Search_parent
{
    protected $_iSolrCount=0;
    
    public function getSearchArticles($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        // sets active page
        $this->iActPage = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgNr');
        $this->iActPage = ($this->iActPage < 0) ? 0 : $this->iActPage;
        
        $aFilter=\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('filter');
        if(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('filterclear')==="1")
            $aFilter = null;

        // load only articles which we show on screen
        //setting default values to avoid possible errors showing article list
        $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;
        
        
        $aFilterSettingsType=[];
        if($this->getConfig()->getConfigParam('rs-solr_search_display_price'))
            $aFilterSettingsType['oxarticles__oxprice'] = 'range_slider_currency';
        
        if($this->getConfig()->getConfigParam('rs-solr_search_display_categories'))
            $aFilterSettingsType['oxcategories__oxid'] = 'checkbox_list';

        if($this->getConfig()->getConfigParam('rs-solr_search_display_categories_main'))
            $aFilterSettingsType['oxcategories_main__oxid'] = 'checkbox_list';
        
        if($this->getConfig()->getConfigParam('rs-solr_search_display_manufacturers'))
            $aFilterSettingsType['oxmanufacturers__oxid'] = 'checkbox_list';
        
        $aFilterSettings=array_keys($aFilterSettingsType);
        
        $oSolrSearch = \rs\solr\Core\solr_connector::getSearch();
        $oSolrSearch->setSearchPhrase($sSearchParamForQuery);
        $oSolrSearch->setSearchFilter($aFilter);
        $oSolrSearch->setLimit($iNrofCatArticles * $this->iActPage, $iNrofCatArticles);
        $oSolrSearch->setSortString($sSortBy);
        $oSolrSearch->setFacetFields($aFilterSettings);

        
        list($iFound,$aResult, $aFacets, $sQuery, $iPages, $sError)  = $oSolrSearch->execute();
        $this->_iSolrCount = $iFound;
        //die($sQuery);
        
        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->loadIdsAndSort($aResult);
        $oArtList->setSolrFacets($aFacets);
        $oArtList->setSolrFilter($aFilter);
        $oArtList->setSolrFilterSettings($aFilterSettings);
        $oArtList->setSolrFilterSettingsType($aFilterSettingsType);

        return $oArtList;
    }
    
    
    public function getSearchArticlesSuggest($sSearchParamForQuery = false)
    {
        $iNrofCatArticles = 10;
        
        $aFilterSettings=[
            'oxmanufacturers__oxid',
            'oxcategories__oxid',
            'oxcategories_main__oxid'
        ];
        
        $oSolrSearch = \rs\solr\Core\solr_connector::getSearch();
        $oSolrSearch->setSearchPhrase($sSearchParamForQuery);
        $oSolrSearch->setLimit(0, $iNrofCatArticles);
        $oSolrSearch->setSortString("score@@asc");
        $oSolrSearch->setFacetFields($aFilterSettings);

        
        list($iFound,$aResult, $aFacets, $sQuery, $iPages, $sError)  = $oSolrSearch->execute();
        $this->_iSolrCount = $iFound;
        //die($sQuery);
        
        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->loadIdsAndSort($aResult);
        $oArtList->setSolrFacets($aFacets);
        $oArtList->setSolrFilterSettings($aFilterSettings);

        return $oArtList;
    }

    /**
     * Returns the amount of articles according to search parameters.
     *
     * @param string $sSearchParamForQuery       query parameter
     * @param string $sInitialSearchCat          initial category to seearch in
     * @param string $sInitialSearchVendor       initial vendor to seearch for
     * @param string $sInitialSearchManufacturer initial Manufacturer to seearch for
     *
     * @return int
     */
    public function getSearchArticleCount($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false)
    {
        return $this->_iSolrCount;
    }
}