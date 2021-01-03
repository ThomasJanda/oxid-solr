<?php

namespace rs\solr\Application\Model;


class rssolr_import extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    protected $_sClassName = 'rssolr_import';

    public function __construct()
    {
        parent::__construct();
        $this->init('rssolr_import');
    }
}
