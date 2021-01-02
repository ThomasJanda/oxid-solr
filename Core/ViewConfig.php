<?php

namespace rs\solr\Core;

class ViewConfig extends ViewConfig_parent
{
    
    protected function rsSolrGenerateParameter()
    {
        $aViews=['alist', 'search'];
        if(in_array($this->getTopActionClassName(),$aViews))
        {
            $aFilter=\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('filter');
            if($aFilter)
            {
                $tmp=[];
                $tmp['filter']=$aFilter;
                return $tmp;
            }
        }
        return null;
    }
    protected function getSolrGenerateParameterUrl()
    {
        $sParam = "";
        if($aFilter = $this->rsSolrGenerateParameter())
        {
            $sep = "&amp;";
            $sParam = $sep.http_build_query($aFilter,null,$sep);
        }
        return $sParam;
    }
    

    public function getAdditionalNavigationParameters()
    {
        $aParam = parent::getAdditionalNavigationParameters();
    
        if($aFilter = $this->rsSolrGenerateParameter())
        {
            $aParam = array_merge($aParam,$aFilter);
        }
        return $aParam;
    }

    public function getAdditionalParameters()
    {
        return parent::getAdditionalParameters().$this->getSolrGenerateParameterUrl();
    }

    public function addRequestParameters()
    {
        return parent::addRequestParameters().$this->getSolrGenerateParameterUrl();
    }
    
    public function getDynUrlParameters($listType)
    {
        return parent::getDynUrlParameters($listType).$this->getSolrGenerateParameterUrl();
    }
}