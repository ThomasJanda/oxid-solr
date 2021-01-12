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
        $sStartDatetime = $oRequest->getRequestParameter("startdatetime",null);
        if($sStartDatetime===null)
            $sStartDatetime = date('Y-m-d H:i:s');
        $sEndDatetime = date('Y-m-d H:i:s');
        $sImportOxid = $oRequest->getRequestParameter("importoxid",null);
        if($sImportOxid===null || $sImportOxid==="")
        {
            $sImportOxid=uniqid("");

            $aData = [];
            $aData['rssolr_import__oxid']=$sImportOxid;
            $aData['rssolr_import__rsstart']=date('Y-m-d H:i:s');

            //create start
            $o = oxNew(\rs\solr\Application\Model\rssolr_import::class);
            $o->assign($aData);
            $o->save();
        }

        $iOffset = (int) trim($this->getConfig()->getConfigParam('rs-solr_import_offset'));
        if($iOffset <= 0)
        {
            $iOffset = 50;
        }


        $oSolrImport = \rs\solr\Core\solr_connector::getImport();
        /*
        if($iStart===0)
            $oSolrImport->deleteAll();
        */

        $bContinue = $oSolrImport->import($iStart, $iOffset);

        if($bContinue===false && $sImportOxid!="")
        {
            //add end
            $o = oxNew(\rs\solr\Application\Model\rssolr_import::class);
            if($o->load($sImportOxid))
            {
                $aData = [];
                $aData['rssolr_import__rsend']=date('Y-m-d H:i:s');
                $o->assign($aData);
                $o->save();
            }
        }

        $this->addTplParam("startDatetime", $sStartDatetime);
        $this->addTplParam("endDatetime", $sEndDatetime);
        $this->addTplParam("importOxid", $sImportOxid);
        $this->addTplParam("continueInsert", $bContinue);
        $this->addTplParam("offset", $iStart + $iOffset);
    }



    public function update()
    {
        /** @var \OxidEsales\Eshop\Core\Request $oRequest */
        $oRequest = oxNew(\OxidEsales\Eshop\Core\Request::class);

        $sStartDatetime = $oRequest->getRequestParameter("startdatetime",null);
        if($sStartDatetime===null)
            $sStartDatetime = date('Y-m-d H:i:s');
        $sEndDatetime = date('Y-m-d H:i:s');
        $sImportOxid = $oRequest->getRequestParameter("updateoxid",null);
        if($sImportOxid===null || $sImportOxid==="")
        {
            $sImportOxid=uniqid("");

            $aData = [];
            $aData['rssolr_import__oxid']=$sImportOxid;
            $aData['rssolr_import__rsstart']=date('Y-m-d H:i:s');

            //create start
            $o = oxNew(\rs\solr\Application\Model\rssolr_import::class);
            $o->assign($aData);
            $o->save();
        }

        $iOffset = (int) trim($this->getConfig()->getConfigParam('rs-solr_update_offset'));
        if($iOffset <= 0)
        {
            $iOffset = 10;
        }

        $oSolrImport = \rs\solr\Core\solr_connector::getImport();
        $bContinue = $oSolrImport->update($iOffset);

        if($bContinue===false && $sImportOxid!="")
        {
            //add end
            $o = oxNew(\rs\solr\Application\Model\rssolr_import::class);
            if($o->load($sImportOxid))
            {
                $aData = [];
                $aData['rssolr_import__rsend']=date('Y-m-d H:i:s');
                $o->assign($aData);
                $o->save();
            }
        }

        $this->addTplParam("startDatetime", $sStartDatetime);
        $this->addTplParam("endDatetime", $sEndDatetime);
        $this->addTplParam("updateOxid", $sImportOxid);
        $this->addTplParam("continueUpdate", $bContinue);
    }

}