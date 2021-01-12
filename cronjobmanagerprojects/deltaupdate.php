<?php

/**
 * the cronjobmanager include this file and pass following array to this script
 * $_CRON_PARAMETER = array
 *
 * it contain all parameter that configured in the cronjob and also all parameter that
 * pass from the last call of the cronjob
 *
 * also the cronjobmanager pass following variable. You can switch it to true, if your job is done
 * $_CRON_FINISHED = boolean
 */

/* include shop */
require_once __DIR__."/../../../../bootstrap.php";
$_CRON_FINISHED = true;

$oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
$iOffset = (int) trim($oConfig->getConfigParam('rs-solr_update_offset'));
if($iOffset <= 0) $iOffset = 10;

echo "execute delta-update".PHP_EOL;

$oSolrImport = \rs\solr\Core\solr_connector::getImport();
$bContinue = $oSolrImport->update($iOffset);



if($bContinue)
    $_CRON_FINISHED = false;
