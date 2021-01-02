<?php

namespace rs\solr\Core;

class SortingValidator
{
    /**
     * @param string $sortBy
     * @param string $sortOrder
     * @return bool
     */
    public function isValid($sortBy, $sortOrder)
    {
        $isValid = false;
        if (
            $sortBy
            && $sortOrder
            && in_array(strtolower($sortOrder), $this->getSortingOrders())
            && in_array($sortBy, \rs\solr\Core\solr_connector::OxidSortColumns())
        ) {
            $isValid = true;
        }

        return $isValid;
    }
    
    /**
     * @return array
     */
    public function getSortingOrders()
    {
        $o = new \OxidEsales\EshopCommunity\Core\SortingValidator();
        return $o->getSortingOrders();
    }
}