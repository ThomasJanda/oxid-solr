<?php

namespace rs\solr\Application\Controller;

class SearchController extends SearchController_parent
{
    
    public function render()
    {
        $ret = parent::render();
        
        $oArtList = $this->getArticleList();
        $this->addTplParam("facets", $oArtList->getSolrFacets());
        $this->addTplParam('filter', $oArtList->getSolrFilter());
        $this->addTplParam("hasfilterset", $oArtList->hasSolrFilterSet());
        $this->addTplParam('filtersettings', $oArtList->getSolrFilterSettings());
        $this->addTplParam('filtersettingstype', $oArtList->getSolrFilterSettingsType());
       
        return $ret;
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
