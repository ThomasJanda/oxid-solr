<?php

namespace rs\solr\Core;

/** @var \Solarium\Client $oSolrClient */
$oSolrClient = null;

class solr_connector
{

    /**
     * @return \rs\solr\Core\solr_search
     */
    public static function getSearch()
    {
        return new solr_select();
    }
    
    /**
     * @return \rs\solr\Core\solr_import
     */    
    public static function getImport()
    {
        return new solr_import();
    }
    
    
    
    /**
     * return solr connector solarium
     * 
     * @global \Solarium\Client $oSolrClient
     * @return \Solarium\Client
     */
    public function getClient()
    {
        global $oSolrClient;
        
        if($oSolrClient===null)
        {
            $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
            
            // create a client instance
            $adapter = new \Solarium\Core\Client\Adapter\Curl(); // or any other adapter implementing AdapterInterface
            $eventDispatcher = new \rs\solr\Core\solarium\solarium_dispatcher();
            $config = array(
                'endpoint' => array(
                    'localhost' => array(
                        'host' => $oConfig->getConfigParam('rs-solr_server_host'),
                        'port' => (int) $oConfig->getConfigParam('rs-solr_server_port'),
                        'path' => $oConfig->getConfigParam('rs-solr_server_path'),
                        'core' => $oConfig->getConfigParam('rs-solr_server_core'),
                        // For Solr Cloud you need to provide a collection instead of core:
                        // 'collection' => 'techproducts',
                    )
                )
            );
            
            $oSolrClient = new \Solarium\Client($adapter, $eventDispatcher, $config);
        }
        return $oSolrClient;
    }
    
    /**
     * return solr connector solarium
     * 
     * @return \Solarium\Client
     */
    public static function getSolrClient()
    {
        $o = new solr_connector();
        return $o->getClient();
    }
    
    /**
     * ping solr server and return information about status
     * 
     * @return boolean
     */
    public static function ping()
    {
        $oClient = self::getSolrClient();
        
        // create a ping query
        $ping = $oClient->createPing();

        // execute the ping query
        try {
            $result = $oClient->ping($ping);
            if($result->getData()['status']=="OK")
                return true;
        } catch (Exception $e) {}
        return false;
    }

    
    public static function deleteAll()
    {
        $oSolrClient = self::getSolrClient();
        
        // get an update query instance
        $update = $oSolrClient->createUpdate();

        // add the delete query and a commit command to the update query
        $update->addDeleteQuery('*:*');
        $update->addCommit();

        // this executes the query and returns the result
        $oSolrClient->update($update);
    }
    
    
    
    public static function OxidSortColumns()
    {
        $aSort = array_merge(['score'],\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aSortCols'));
        return $aSort;
    }

}