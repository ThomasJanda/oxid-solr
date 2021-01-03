<?php

namespace rs\solr\Application\Model;

class ArticleList extends ArticleList_parent
{
    protected $_aSolrFacets = null;
    protected $_aSolrFilterSettings = null;
    protected $_aSolrFilterSettingsType = null;
    protected $_aSolrFilter = null;
    
    
    public function setSolrFacets($aFacets)
    {
        $this->_aSolrFacets = $aFacets;
    }
    public function getSolrFacets()
    {
        return $this->_aSolrFacets;
    }
    
    
    public function setSolrFilterSettings($aFilter)
    {
        $this->_aSolrFilterSettings = $aFilter;
    }
    public function getSolrFilterSettings()
    {
        return $this->_aSolrFilterSettings;
    }
    
    
    public function setSolrFilter($aFilter)
    {
        $this->_aSolrFilter = $aFilter;
    }
    public function getSolrFilter()
    {
        return $this->_aSolrFilter;
    }
    public function hasSolrFilterSet()
    {
        return (is_array($this->_aSolrFilter) && count($this->_aSolrFilter)>0?count($this->_aSolrFilter):false);
    }
    
    
    public function setSolrFilterSettingsType($aFilter)
    {
        $this->_aSolrFilterSettingsType = $aFilter;
    }
    public function getSolrFilterSettingsType()
    {
        return $this->_aSolrFilterSettingsType;
    }
    
    
    public function loadIdsAndSort($aIds)
    {
        if (!is_array($aIds) || !count($aIds)) {
            $this->clear();

            return;
        }

        $oBaseObject = $this->getBaseObject();
        $sArticleTable = $oBaseObject->getViewName();
        $sArticleFields = $oBaseObject->getSelectFields();

        $oxIdsSql = implode(',', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds));

        $sSelect = "select $sArticleFields from $sArticleTable ";
        $sSelect .= "where $sArticleTable.oxid in ( " . $oxIdsSql . " ) and ";
        $sSelect .= $oBaseObject->getSqlActiveSnippet();
        $sSelect .= " order by field($sArticleTable.oxid,$oxIdsSql) ";

        $this->selectString($sSelect);
    }
    
}
