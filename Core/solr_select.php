<?php

namespace rs\solr\Core;

class solr_select 
{
    protected $oConfig = null;

    /**
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function getConfig()
    {
        if ($this->oConfig == null) {
            $this->oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        }
        return $this->oConfig;
    }
    
    const aDefaultSearchColumn=[
        'oxarticles__oxartnum' => 10.0,
        'oxarticles__oxean' => 10.0,
        'oxarticles__oxtitle' => 5.0,
        'oxarticles__oxsearchkeys' => 8.0,
        'oxarticles__oxshortdesc' => 2.0,
        'oxcategories__oxtitle' => 3.0,
        'oxmanufacturers__oxtitle' => 3.0
    ];
        
    /**
     * @var \Solarium\Client
     */
    protected $oSolrClient = null;
    /**
     * @var \Solarium\Core\Query\AbstractQuery|\Solarium\QueryType\Select\Query\Query
     */
    protected $oSolrQuery = null;
    /**
     * @var \Solarium\Core\Query\Helper
     */
    protected $oSolrHelper = null;
    

    protected $bCache=true;
    protected $sCachePhrase=null;
    protected $sCachePrefix=null;
    protected $aCacheFacets=null;
    
    public function __construct() {
        $this->oSolrClient = solr_connector::getSolrClient();
        $this->oSolrQuery = $this->oSolrClient->createSelect();
        $this->oSolrHelper = $this->oSolrQuery->getHelper();
        $this->oSolrQuery->setFields(['oxarticles__oxid','oxarticles__oxparentid']);
        
        //group=true&group.field=oxarticles__oxparentid&group.main=true
        $oGrouping = $this->oSolrQuery->getGrouping();
        $oGrouping->setFields('oxarticles__oxparentid');
        //$oGrouping->setMainResult(true);
        $oGrouping->setSort('oxarticles__oxisparent '.$this->oSolrQuery::SORT_DESC);
        $oGrouping->setFacet(true);
        $oGrouping->setNumberOfGroups(true);
        
        $oFacetSet = $this->oSolrQuery->getFacetSet();
        $oFacetSet->setMinCount(1);
        $oFacetSet->setSort("index");
    }
    
    
    public function setSearchPhrase(string $sPhrase)
    {
        $sQuery=null;
        if(trim($sPhrase)!=="")
        {
            $sPhraseOriginal = $sPhrase;
            //remove special characters
            $special_characters = ['(',')','/',"\\",'&','!','.','-','+'];
            $sPhrase = str_replace($special_characters,' ',$sPhrase);
                        
            $aPhrases = array_filter(array_map('trim',explode(" ",trim($sPhrase))));
            $sPhraseFull = implode(" ",$aPhrases);

            $aColumns = self::aDefaultSearchColumn;

            $aQuery = [];
            foreach($aColumns as $sColumn => $fPrio)
            {
                if(count($aPhrases)>1)
                {
                    $aQuery[] = $sColumn.":(\"".$sPhraseFull."\"~5)^".$fPrio;
                }

                foreach($aPhrases as $sPhrase)
                {
                    $sPhrase = trim($this->oSolrHelper->escapePhrase($sPhrase),'"');

                    $aQuery[] = $sColumn.":(".$sPhrase."~5)^".$fPrio;
                    $aQuery[] = $sColumn.":(*".$sPhrase."*)^".$fPrio;
                }

                if($sColumn=="oxarticles__oxartnum" || $sColumn=="oxarticles__oxean")
                {
                    $aQuery[] = $sColumn.":(\"".trim($this->oSolrHelper->escapePhrase($sPhraseOriginal),'"')."\")^".$fPrio;
                }
            }
            $sQuery = implode(" or ", $aQuery);
            $this->sCachePhrase=$sQuery;
        }
        $this->oSolrQuery->setQuery($sQuery??"*");
    }
    public function setSearchFilter($aFilter)
    {
        if($aFilter!==null && is_array($aFilter))
        {
            foreach($aFilter as $type => $values)
            {
                $sQuery = "";
                foreach($values as $value)
                {
                    if($value!=="")
                    {
                        if($sQuery!="") $sQuery.=" or ";
                        $value = $this->oSolrHelper->escapePhrase($value);
                        $sQuery.=$type.':'.$value;
                    }
                }
                if($sQuery!="")
                {
                    $this->oSolrQuery->createFilterQuery($type)->setQuery($sQuery);
                    $this->bCache = false;
                }  
            }
        }
    }

    public function setSearchFilterFixed($sPrefixKey, $sPrefixValue)
    {
        if($sPrefixKey!==null && $sPrefixValue!==null)
        {
            $value = trim($this->oSolrHelper->escapePhrase($sPrefixValue),'"');
            $sQuery=$sPrefixKey.':'.$value;
            $this->sCachePrefix=$sQuery;
            
            $this->oSolrQuery->createFilterQuery("prefix")->setQuery($sQuery);
        }
    }
    
    protected $iRows = 10;
    public function setLimit(int $iStart, int $iRows)
    {
        if($iRows<=0) $iRows=$this->iRows;
        $this->iRows=$iRows;
        
        if($iStart<=0) $iStart=0;
        
        $this->oSolrQuery->setStart($iStart);
        $this->oSolrQuery->setRows($iRows);
    }
    
    public function setSortString($sSort)
    {
        $sSortColumn="";
        $sSortDirection="";        
        $sSort = strtolower($sSort);
        if ($sSort && strstr($sSort, "@@") !== false) {
            list($sSortColumn, $sSortDirection) = explode("@@", $sSort);
        }
        
        if($sSortColumn!="")
        {
            $sTmp1="";
            $sTmp2="";
            if($sSortColumn=="title" || $sSortColumn=="oxtitle")
            {
                $sTmp1="oxarticles__oxtitle_sort";
            }
            elseif($sSortColumn=="price" || $sSortColumn=="oxvarminprice")
            {
                $sTmp1="oxarticles__oxprice_sort";
            }
            elseif($sSortColumn=="score")
            {
                $sTmp1="score";
            }
            if($sSortDirection=="asc")
            {
                $sTmp2=$this->oSolrQuery::SORT_ASC;
            }
            else
            {
                $sTmp2=$this->oSolrQuery::SORT_DESC;
            }

            if($sTmp1!="" && $sTmp2!=="")
            {
                $this->oSolrQuery->addSort($sTmp1,$sTmp2);
            }
        }
    }
    
    public function setFacetFields($aFilterSettings)
    {
        $oFacetSet = $this->oSolrQuery->getFacetSet();
        if($aFilterSettings)
        {
            $this->aCacheFacets=$aFilterSettings;
            foreach($aFilterSettings as $sFacet)
            {
                $oFacetSet->createFacetField($sFacet)->setField($sFacet);
            }
        }
    }

    protected function _preExecute()
    {
        if($this->sCachePrefix===null)
        {
            //only use on search page
            $sQuery='oxarticles__oxissearch:true';
            $this->oSolrQuery->createFilterQuery("prefix")->setQuery($sQuery);
        }
    }

    protected function _trackQueryValue($v)
    {
        if(is_array($v))
        {
            $v = implode(", ",$v);
        }
        return $v;
    }
    protected function _trackQuery($aParam)
    {
        if(!(bool) $this->getConfig()->getConfigParam('rs-solr_tracking_enable'))
            return;

        /** @var \rs\solr\Application\Model\rssolr_requests $oRequest */
        $oRequest = oxNew(\rs\solr\Application\Model\rssolr_requests::class);
        $oRequest->assign([
            'rssolr_requests__oxid' => uniqid(""),
            'rssolr_requests__rsparam_q' => $this->_trackQueryValue($aParam['q']??null),
            'rssolr_requests__rsparam_fq' => $this->_trackQueryValue($aParam['fq']??null),
            'rssolr_requests__rsparam_sort' => $this->_trackQueryValue($aParam['sort']??null),
            'rssolr_requests__rsresult_count' => $this->_trackQueryValue($aParam['resultCount']??0),
            'rssolr_requests__rsresult_error' => $this->_trackQueryValue($aParam['error']??null),
            'rssolr_requests__rscached' => $this->_trackQueryValue($aParam['cache']??0),
            'rssolr_requests__rsview' => $this->_trackQueryValue(get_class($this->getConfig()->getTopActiveView()))
        ]);
        $oRequest->save();
    }
    
    public function execute()
    {
        $this->_preExecute();


        $aFacets = null;
        $aResult = null;
        $iFound = 0;
        $iPages = 0;
        $sError = null;

        $oSolrBuilder = $this->oSolrQuery->getRequestBuilder()->build($this->oSolrQuery);
        $sQuery = urldecode($oSolrBuilder->getQueryString());

        //die($sQuery);

        //full caching
        $sPathCacheFull = $this->getCacheDirectory()."/full_".md5($sQuery);
        if(file_exists($sPathCacheFull))
        {
            $aRet = unserialize(file_get_contents($sPathCacheFull));
            list($iFound,$aResult, $aFacets, $sQuery, $iPages, $sError) = $aRet;
            $this->_trackQuery(array_merge(['resultCount' => $iFound, 'error' => $sError, 'cache' => 1],$oSolrBuilder->getParams()));
            return $aRet;
        }

        try 
        {
            //execute search
            $oResult = $this->oSolrClient->select($this->oSolrQuery);

            $oGroup = $oResult->getGrouping()->getGroup("oxarticles__oxparentid");
            $iFound = $oGroup->getNumberOfGroups();

            /** @var \Solarium\Component\Result\Grouping\ValueGroup $oGroupValue */
            foreach($oGroup->getValueGroups() as $oGroupValue)
            {
                $v = trim($oGroupValue->getValue());
                if($v!=="")
                    $aResult[]=$v;
            }

            //extract facets
            $aFacetsTmp = $oResult->getFacetSet()->getFacets();
            if(is_array($aFacetsTmp))
            {
                foreach($aFacetsTmp as $sName => $oFacetTmp)
                {
                    foreach($oFacetTmp as $sValue => $iCount)
                    {
                        $aFacets[$sName][$sValue]=$iCount;
                    }
                }
            }
            
            
            //create cacheid
            $aCache = [];
            if($this->aCacheFacets !== null ) $aCache=array_merge($aCache,$this->aCacheFacets);
            if($this->sCachePhrase !== null && $this->sCachePrefix === null ) $aCache[]=$this->sCachePhrase;
            if($this->sCachePrefix !== null ) $aCache[]=$this->sCachePrefix;
            $aCache = array_unique($aCache);
            sort($aCache);
            $sCacheId = md5(implode("|",$aCache));
            
            //caching (only if set phrase or fixed)
            $bLoadCache=true;
            $sPath = $this->getCacheDirectory()."/".$sCacheId;
            if($this->bCache 
                && $this->aCacheFacets 
                && (
                    ($this->sCachePhrase===null && $this->sCachePrefix===null)
                    ||
                    (
                        ($this->sCachePhrase!==null || $this->sCachePrefix!==null)
                        &&
                        ($this->sCachePhrase===null || $this->sCachePrefix===null)
                    )
                )
            )
            {
                //write into the cache
                if($aFacets)
                {
                    $aTmp = [];
                    foreach($aFacets as $sName=>$aValues)
                    {
                        foreach($aValues as $sValue=>$iCount)
                        {
                            $aTmp[$sName][]=$sValue;
                        }
                    }
                    file_put_contents($sPath, json_encode($aTmp));
                    $bLoadCache=false;
                }   
            }
            
            if($bLoadCache)
            {
                //load from cache
                if(file_exists($sPath))
                {
                    $aTmp = json_decode(file_get_contents($sPath),true);
                    if(is_array($aTmp))
                    {
                        foreach($aTmp as $sName=>$aValues)
                        {
                            foreach($aValues as $sValue)
                            {
                                if(!isset($aFacets[$sName][$sValue]))
                                    $aFacets[$sName][$sValue]=0;
                            }
                        }
                    }
                }
            }
            
            //sort facets
            if($aFacets)
            {
                foreach($aFacets as $name => $values)
                {
                    $iSort = SORT_NUMERIC;
                    foreach($values as $key => $iCount)
                    {
                        if(!is_numeric($key))
                        {
                            $iSort = SORT_NATURAL;
                        }
                    }
                    ksort($values, $iSort);
                    $aFacets[$name] = $values;
                }
                
                //enrich
                foreach($aFacets as $name => $values)
                {
                    $aFacets[$name] = $this->enrichFacetData($name, $values);
                }  
                
                
                //sort facets within the same order like requested
                $tmp = [];
                if(is_array($this->aCacheFacets))
                {
                    foreach($this->aCacheFacets as $name)
                    {
                        if(isset($aFacets[$name]))
                            $tmp[$name]=$aFacets[$name];
                    }
                } 
                $aFacets=$tmp;
            }
            
            //calculate pages
            $iRows = $this->iRows;
            $iPages = ceil($iFound / $iRows);
            
        } catch (\Throwable $ex) {
            $sError = $ex->getMessage();
            $aFacets = null;
            $aResult = null;
            $iFound = 0;
        }

        $this->_trackQuery(array_merge(['resultCount' => $iFound, 'error' => $sError],$oSolrBuilder->getParams()));

        //create full cache
        $aRet = array($iFound, $aResult, $aFacets, $sQuery, $iPages, $sError);
        if(!$sError)
        {
            file_put_contents($sPathCacheFull, serialize($aRet));
        }

        return $aRet;
    }
    
    
    protected function getCacheDirectory()
    {
        /**
         * @var \OxidEsales\Eshop\Core\Config $oConfig
         */
        $oConfig = $this->getConfig();

        $sPath = $oConfig->getConfigParam('sCompileDir')."rs-solr";
        @mkdir($sPath);
        return $sPath;
    }
    
    
    
    public function enrichFacetData($name, $data)
    {
        //select title as value which has to sort for
        $list = null;
        if($name==="oxcategories_main__oxid") $name="oxcategories__oxid";

        if($name==="oxmanufacturers__oxid" || $name==="oxcategories__oxid")
        {
            $keys = implode(',', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray(array_keys($data)));
            //$keys = "'".implode("','",array_keys($data))."'";
            $table = explode("__",$name)[0];
            $sSql="select oxid, oxtitle from ".$table." where oxid in (".$keys.") order by oxtitle";
            $tmp = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sSql);
            foreach($tmp as $row)
            {
                $list[$row['oxid']]=$row['oxtitle'];
            }
        }
            
        
        foreach($data as $value=>$count)
        {
            $item=[];
            $item['count']=$count;
            $item['value']=$value;
            $item['label']=$list[$item['value']]??$value;
            $list[$item['value']]=$item;
        }
        
        return $list;
    }
}

