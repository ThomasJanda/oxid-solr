<?php

namespace rs\solr\Application\Model;


class rssolr_requests extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    protected $_aSkipSaveFields = ['rscreated'];
    protected $_sClassName = 'rssolr_requests';

    public function __construct()
    {
        parent::__construct();
        $this->init('rssolr_requests');
    }
}
