<?php

namespace rs\solr\Core;

class solr_import
{

    /**
     * @var \Solarium\Client
     */
    protected $oSolrClient = null;
    /**
     * @var \Solarium\Core\Query\Helper
     */
    protected $oSolrHelper = null;
    /**
     * @var \Solarium\Core\Query\AbstractQuery|\rs\solr\Core\solarium\solarium_update_query
     */
    protected $oSolrUpdate = null;

    protected $oConfig = null;

    public function __construct() {
        $this->oSolrClient = solr_connector::getSolrClient();
        //$this->oSolrUpdate = new \rs\solr\Core\solarium\solarium_update_query();
        $this->oSolrUpdate = $this->oSolrClient->createUpdate();
        $this->oSolrHelper = $this->oSolrUpdate->getHelper();
    }

    /**
     * @return \Solarium\Client
     */
    protected function getSolrClient()
    {
        return $this->oSolrClient;
    }

    /**
     * @return \Solarium\Core\Query\Helper
     */
    protected function getSolrHelper()
    {
        return $this->oSolrHelper;
    }

    /**
     * @return \Solarium\Core\Query\AbstractQuery|\Solarium\QueryType\Update\Query\Query
     */
    protected function getSolrUpdate()
    {
        return $this->oSolrUpdate;
    }

    /**
     * @return \OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface
     */
    protected function getDb()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
    }

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

    public function setup()
    {
        $oSolrClient = \rs\solr\Core\solr_connector::getSolrClient();

        //https://stackoverflow.com/questions/51781585/how-to-create-a-new-solr-core-in-laravel-solarium

        $sUrl = $oSolrClient->getEndpoint()->getBaseUri();

        $aJsons=[];

        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxid",
                "type" => "string",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxparentid",
                "type" => "string",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxisparent",
                "type" => "boolean",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxtitle",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxtitle_sort",
                "type" => "string",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxartnum",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxean",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxprice",
                "type" => "string",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxprice_sort",
                "type" => "pfloat",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxsearchkeys",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxshortdesc",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxarticles__oxvarselect",
                "type" => "strings",
                "stored" => true,
                "multiValued" => true,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxcategories__oxid",
                "type" => "string",
                "stored" => true,
                "multiValued" => true,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxcategories__oxtitle",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxmanufacturers__oxid",
                "type" => "string",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];
        $aJsons[] =[
            "add-field" => [
                "name" =>"oxmanufacturers__oxtitle",
                "type" => "text_general",
                "stored" => true,
                "multiValued" => false,
                "indexed" => true
            ]
        ];


        $a = array_keys($this->getAttributesIds());
        foreach($a as $key)
        {
            $aJsons[] =[
                "add-field" => [
                    "name" =>$key,
                    "type" => "strings",
                    "stored" => true,
                    "multiValued" => true,
                    "indexed" => true
                ]
            ];

        }

        foreach($aJsons as $aJson)
        {
            $sJson = json_encode($aJson);
            $cli = "curl -X POST -H 'Content-type:application/json' --data-binary '{$sJson}' {$sUrl}schema";
            //echo $cli;
            exec($cli);
        }
    }


    public function deleteAll()
    {
        \rs\solr\Core\solr_connector::deleteAll();
    }

    //protected $aSolrColumnType=[];
    protected $_tmp_convertValue=[];
    protected function _convertValue(string $table, string $column, $value, ?string $columnType=null)
    {
        $helper = $this->getSolrHelper();

        if($columnType==null && $table!="" && $column!="")
        {
            if(isset($this->_tmp_convertValue[$table."__".$column]))
            {
                $columnType=$this->_tmp_convertValue[$table."__".$column];
            }
            else
            {
                $oConfig = $this->getConfig();
                $oDb = $this->getDb();
                $sSql="SELECT 
                DATA_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE table_name = '$table'
                AND COLUMN_NAME = '$column' 
                and TABLE_SCHEMA='{$oConfig->getShopConfVar('dbName')}'";
                $columnType = strtolower($oDb->getOne($sSql));

                $this->_tmp_convertValue[$table."__".$column]=$columnType;
            }



            /*
            if($columnType==="tinyint")
            {
                $this->aSolrColumnType[$table."__".$column]="boolean";
            }
            elseif($columnType==="int")
            {
                $this->aSolrColumnType[$table."__".$column]="plong";
            }
            elseif($columnType==="datetime" || $columnType==="timestamp" || $columnType==="date")
            {
                $this->aSolrColumnType[$table."__".$column]="pdate";
            }
            elseif($columnType==="double" || $columnType==="float")
            {
                $this->aSolrColumnType[$table."__".$column]="pfloat";
            }
            else
            {
                $this->aSolrColumnType[$table."__".$column]="text_general";
            }
             */
        }


        if($value===null)
        {
            $value=null;
        }
        if($columnType==="tinyint")
        {
            $value=(bool)$value;
        }
        elseif($columnType==="int")
        {
            $value=(int)$value;
        }
        elseif($columnType==="datetime" || $columnType==="timestamp")
        {
            if($value=="0000-00-00 00:00:00")
            {
                $value=null;
            }
            else
            {
                $value = $helper->formatDate($value);
            }
        }
        elseif($columnType==="date")
        {
            if($value=="0000-00-00")
            {
                $value=null;
            }
            else
            {
                $value = $helper->formatDate($value." 00:00:00");
            }
        }
        elseif($columnType==="double" || $columnType==="float")
        {
            if($value===null)
                $value=0;
            $value = (float) $value;
        }
        elseif($columnType==="string")
        {
            //do nothing
            $value=$value;
        }
        else
        {
            $value = $helper->escapeTerm($value??"");
        }

        return $value;
    }

    protected function getArticleSql()
    {
        $bStockCheck = true;
        $bVariants = true;

        /** @var \OxidEsales\Eshop\Application\Model\Article $oArt */
        $oArt=oxNew('oxArticle');
        $sView=getViewName("oxarticles");
        $where=$oArt->getActiveCheckQuery();
        if($bStockCheck)
        {
            $where.=" ".$oArt->getStockCheckQuery()." ";
        }
        if(!$bVariants)
        {
            $where.=" and $sView.oxparentid='' ";
        }
        $where=" (".$where.") ";
        $sSql="select oxid from $sView where ".$where;
        return $sSql;
    }

    public function getAttributesIds()
    {
        //attributes
        $sTable = "oxattribute";
        $sSql="select 
        oxattribute.oxid,
        oxattribute.oxtitle
        from oxattribute
        join oxobject2attribute on oxattribute.oxid=oxobject2attribute.oxattrid
        where oxobject2attribute.oxobjectid in (".$this->getArticleSql().")
        group by oxattribute.oxid
        order by oxattribute.oxtitle
        ";
        $aRowsData = $this->getDb()->getAll($sSql);
        if(count($aRowsData))
        {
            foreach($aRowsData as $aRowData)
            {
                $aRowData = array_change_key_case($aRowData);
                $key = $sTable."__".$aRowData['oxid'];
                $a[$key]=$aRowData['oxtitle'];
            }
        }

        return $a;
    }

    public function getAllAttributesIds()
    {
        $a = [
            'oxmanufacturers__oxid' => 'Hersteller',
            'oxcategories__oxid' => 'Kategorie',
            'oxarticles__oxprice' => 'Preis',
            'oxarticles__oxvarselect' => 'Variants'
        ];

        return array_merge($a, $this->getAttributesIds());
    }

    public function import(int $iStart=0, int $iOffset=10)
    {
        $bContinue = false;
        if($iStart===0)
        {
            $this->deleteAll();
        }

        $sSql=$this->getArticleSql()." limit ".$iStart.",".$iOffset;
        if($aRows = $this->getDb()->getAll($sSql))
        {
            $aDocuments=[];

            foreach($aRows as $aRow)
            {
                $aRow = array_change_key_case($aRow);
                $sArticleOxid = $aRow['oxid'];

                $aDocuments[] = $this->generateArticle($sArticleOxid);
                $bContinue=true;
            }

            //add documents and commit
            $update = $this->getSolrUpdate();
            //$update->setSolrColumnTypes($this->aSolrColumnType);
            $update->addDocuments($aDocuments);
            $update->addCommit();

            //$xml = $update->getRequestBuilder()->build($update)->getRawData();
            //echo '<pre>'.htmlentities($xml);
            //die("");
            $this->getSolrClient()->update($update);
        }

        return $bContinue;
    }

    protected function generateArticle($sArticleOxid)
    {
        $oData = $this->getSolrUpdate()->createDocument();
        $oData->id=$sArticleOxid;
        $this->generateDataArticle($oData, $sArticleOxid);
        $this->generateDataCategory($oData, $sArticleOxid);
        $this->generateManufacturer($oData, $sArticleOxid);
        $this->generateAttributes($oData, $sArticleOxid);
        return $oData;
    }

    protected function generateDataArticle(&$oData, string $sArticleOxid)
    {
        //article
        $oArt=oxNew('oxArticle');
        $sView=$oArt->getViewName();
        $sTable = "oxarticles";
        $sTable_parent = "oxarticles_parent";
        /*
        $sSql="select "
                . "if($sTable.oxparentid is null or $sTable.oxparentid='',$sTable.oxid,$sTable.oxparentid) as oxparentid, "
                . "if($sTable.oxparentid is null or $sTable.oxparentid='',1,0) as oxisparent, "
                . "$sTable.oxtitle, "
                . "$sTable.oxartnum, "
                . "$sTable.oxean, "
                . "$sTable.oxprice, "
                . "$sTable.oxprice as oxprice_sort, "
                . "$sTable.oxsearchkeys, "
                . "$sTable.oxshortdesc, "
                . "$sTable.oxvarselect "
                . "from ".getViewName($sTable)." ".$sTable ." "
                . "left join ".getViewName($sTable)." $sTable_parent on $sTable_parent.oxid=$sTable.oxparentid "
                . "where $sTable.oxid=?";
        */

        $sSql="select 
        $sTable_parent.oxid,
        if($sTable_parent.oxid is null,$sTable.oxid,$sTable.oxparentid) as oxparentid, 
        if($sTable_parent.oxid is null,1,0) as oxisparent, 
        if($sTable_parent.oxid is null,$sTable.oxtitle,trim(concat($sTable_parent.oxtitle,' ',$sTable.oxtitle))) as oxtitle, 
        if($sTable_parent.oxid is null,$sTable.oxtitle,trim(concat($sTable_parent.oxtitle,' ',$sTable.oxtitle))) as oxtitle_sort, 
        $sTable.oxartnum, 
        $sTable.oxean, 
        $sTable.oxprice, 
        $sTable.oxprice as oxprice_sort, 
        $sTable.oxsearchkeys, 
        $sTable.oxshortdesc, 
        $sTable.oxvarselect 
        from ".getViewName($sTable)." $sTable 
        left join ".getViewName($sTable)." $sTable_parent on $sTable_parent.oxid=$sTable.oxparentid
        where $sTable.oxid=?";
        $aRowData = array_change_key_case($this->getDb()->getRow($sSql,[$sArticleOxid]));
        foreach($aRowData as $key=>$value)
        {
            if($key=="oxvarselect")
            {
                $value = explode("|",$value);
                $value = array_map("trim", $value);

                $tmp = [];
                foreach($value as $v)
                {
                    $tmp[] = $this->_convertValue($sTable, $key, $v, "string");
                }
                $value = $tmp;
            }
            else
            {
                $value = $this->_convertValue($sTable, $key, $value);
            }
            $key = $sTable."__".$key;
            $oData->$key = $value;
        }
    }
    protected function generateDataCategory(&$oData, string $sArticleOxid)
    {

        //main categorie
        $sTable = "oxcategories";
        $sSql="select oxtitle from ".getViewName($sTable)." $sTable 
        where oxid = (
            select 
            oxobject2category.oxcatnid 
            from oxobject2category 
            where oxobject2category.oxobjectid=(
                select if(oxparentid is null or oxparentid='',oxid,oxparentid) 
                from ".getViewName('oxarticles')." oxarticles where oxid=?
            ) order by oxtime asc
            limit 0,1
        )";
        $value = $this->getDb()->getOne($sSql,[$sArticleOxid]);
        //only the first i need the title
        $value = $this->_convertValue($sTable, "oxtitle", $value);
        $key = $sTable."__oxtitle";
        $oData->$key = $value;



        $sSql="select oxid from ".getViewName($sTable)." $sTable 
        where oxid in (
            select 
            oxobject2category.oxcatnid 
            from oxobject2category 
            where oxobject2category.oxobjectid=(
                select if(oxparentid is null or oxparentid='',oxid,oxparentid) 
                from ".getViewName('oxarticles')." oxarticles where oxid=?
            ) order by oxtime asc
        )";
        $values = array_change_key_case($this->getDb()->getCol($sSql,[$sArticleOxid]));

        $tmp = [];
        foreach($values as $value)
        {
            $tmp[] = $this->_convertValue($sTable, "oxid", $value);
        }

        $key = $sTable."__oxid";
        $oData->$key = $tmp;
    }
    protected function generateManufacturer(&$oData, string $sArticleOxid)
    {
        //manufacturer
        $sTable = "oxmanufacturers";
        $sSql="select oxid, oxtitle
        from ".getViewName($sTable)." $sTable 
        where oxid = (
            select oxmanufacturerid from ".getViewName('oxarticles')." oxarticles where if(oxparentid is null or oxparentid='',oxid,oxparentid) = ? limit 0,1
        )";
        $aRowData = array_change_key_case($this->getDb()->getRow($sSql,[$sArticleOxid]));
        foreach($aRowData as $key=>$value)
        {
            $value = $this->_convertValue($sTable, $key, $value);
            $key = $sTable."__".$key;
            $oData->$key = $value;
        }
    }
    protected function generateAttributes(&$oData, string $sArticleOxid)
    {
        $sep = trim($this->getConfig()->getConfigParam('rs-solr_attribute_seperator'));

        //attributes
        $sTable = "oxattribute";
        $sSql="select 
        oxattribute.oxid,
        oxattribute.oxtitle, 
        oxattribute.oxpos, 
        oxobject2attribute.oxvalue
        from ".getViewName($sTable)." $sTable
        join oxobject2attribute on oxattribute.oxid=oxobject2attribute.oxattrid
        where oxobject2attribute.oxobjectid=?
        order by oxattribute.oxpos";
        $aRowsData = $this->getDb()->getAll($sSql,[$sArticleOxid]);
        if(count($aRowsData))
        {
            foreach($aRowsData as $aRowData)
            {
                $aRowData = array_change_key_case($aRowData);
                $tmp = [];

                if($sep!="")
                {
                    $values = array_map('trim',explode($sep,($aRowData['oxvalue']??"")));

                    foreach($values as $v)
                    {
                        $tmp[] = $this->_convertValue("", "", $v, "string");
                    }
                }
                else
                {
                    $tmp[] = $this->_convertValue("", "", $aRowData['oxvalue'], "string");
                }

                $key = $sTable."__".$aRowData['oxid'];
                $oData->$key = $tmp;
            }
        }
    }
}