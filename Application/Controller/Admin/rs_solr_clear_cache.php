<?php
namespace rs\solr\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Request;

class rs_solr_clear_cache extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    public function render()
    {
        parent::render();
        return "rs_solr_clear_cache.tpl";
    }

    protected function getCacheDirectory()
    {
        return $this->getConfig()->getConfigParam('sCompileDir')."rs-solr";
    }

    protected $_iFileCount = false;
    public function getFileCount()
    {
        if($this->_iFileCount===false)
        {
            $this->_iFileCount=0;

            $sPath = $this->getCacheDirectory();
            if($handle=opendir($sPath))
            {
                while ($sFilename = readdir ($handle)) {

                    if($sFilename!="." && $sFilename!=".." && is_file($sPath."/".$sFilename))
                    {
                        $this->_iFileCount++;
                    }
                }
                closedir($handle);
            }
        }

        return $this->_iFileCount;
    }
    public function hasFileCount()
    {
        $iCount = $this->getFileCount();
        if($iCount>0)
            return true;
        return false;
    }

    public function deleteCacheFiles()
    {
        $sPath = $this->getCacheDirectory();

        $files = glob($sPath.'/*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }
}
