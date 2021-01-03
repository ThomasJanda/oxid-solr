<?php

namespace rs\solr\Application\Controller;

include '../../../../../bootstrap.php';
$autosuggest = oxNew(AutoSuggestController::class);
$autosuggest->search();

class AutoSuggestController
{
    public function search()
    {
        echo (json_encode($this->_getProductResult(),JSON_PRETTY_PRINT));
    }

    protected function _getProductResult()
    {
        $oConfig  = \OxidEsales\Eshop\Core\Registry::getConfig();
        $term     = (string) $oConfig->getRequestParameter('term');
        
        $oSearch  = oxNew(\OxidEsales\Eshop\Application\Model\Search::class);
        $oArtList = $oSearch->getSearchArticlesSuggest($term);
        $currency = $oConfig->getActShopCurrencyObject()->sign;
        $aResult  = [];

        if ($oArtList != null) {

            $iMax = (int) $oConfig->getConfigParam('rs-solr_suggest_count_articles');
            if($iMax <= 0) $iMax=10;

            $x=0;
            foreach ($oArtList as $oArticle) {
                $aResult[] = [
                    'type'   => 'oxarticles',
                    'oxid'   => $oArticle->getId(),
                    'title'  => $oArticle->oxarticles__oxtitle->value,
                    'artnum' => $oArticle->oxarticles__oxartnum->value,
                    'price'  => \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($oArticle->getPrice()->getPrice()) . $currency,
                    'link'   => $oArticle->getMainLink() . '?searchparam=' . $term,
                    'image'  => $oArticle->getThumbnailUrl()
                ];
                $x++;
                if($x>=$iMax) break;
            }


            $iMax = (int) $oConfig->getConfigParam('rs-solr_suggest_count_other');
            if($iMax <= 0) $iMax=5;
            
            //max 5 categories/manufacturers
            $aFacets = $oArtList->getSolrFacets();
            
            if($oConfig->getConfigParam('rs-solr_suggest_display_categories') && isset($aFacets['oxcategories__oxid']))
            {
                $aList = [];
                foreach($aFacets['oxcategories__oxid'] as $oxid => $data)
                {
                    $aList[$oxid] = $data['count'];
                }
                arsort($aList, SORT_NUMERIC);

                $x=0;
                $tmp=[];
                /** @var \OxidEsales\Eshop\Application\Model\Category $o **/
                $o = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                foreach($aList as $oxid => $count)
                {
                    if($o->load($oxid))
                    {
                        $tmp[] = [
                            'oxid'  => $o->getId(),
                            'title' => $o->getTitle(),
                            'link'  => $o->getLink() . '?searchparam=' . $term,
                            'image' => $o->getIconUrl()
                        ];
                
                        $x++;
                        if($x>=$iMax) break;
                    }
                }
                if(count($tmp)>0)
                {
                    $aResult[] = [
                        'type'  => 'oxcategory',
                        'items' => $tmp
                    ];
                }
            }

            if($oConfig->getConfigParam('rs-solr_suggest_display_categories_main') && isset($aFacets['oxcategories_main__oxid']))
            {
                $aList = [];
                foreach($aFacets['oxcategories_main__oxid'] as $oxid => $data)
                {
                    $aList[$oxid] = $data['count'];
                }
                arsort($aList, SORT_NUMERIC);

                $x=0;
                $tmp=[];
                /** @var \OxidEsales\Eshop\Application\Model\Category $o **/
                $o = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                foreach($aList as $oxid => $count)
                {
                    if($o->load($oxid))
                    {
                        $tmp[] = [
                            'oxid'  => $o->getId(),
                            'title' => $o->getTitle(),
                            'link'  => $o->getLink() . '?searchparam=' . $term,
                            'image' => $o->getIconUrl()
                        ];

                        $x++;
                        if($x>=$iMax) break;
                    }
                }
                if(count($tmp)>0)
                {
                    $aResult[] = [
                        'type'  => 'oxcategory_main',
                        'items' => $tmp
                    ];
                }
            }
            
            if($oConfig->getConfigParam('rs-solr_suggest_display_manufacturers') && isset($aFacets['oxmanufacturers__oxid']))
            {
                $aList = [];
                foreach($aFacets['oxmanufacturers__oxid'] as $oxid => $data)
                {
                    $aList[$oxid] = $data['count'];
                }
                arsort($aList, SORT_NUMERIC);
                
                $x=0;
                $tmp=[];
                /** @var \OxidEsales\Eshop\Application\Model\Manufacturer $o **/
                $o = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
                foreach($aList as $oxid => $count)
                {
                    if($o->load($oxid))
                    {
                        $tmp[] = [
                            'oxid'  => $o->getId(),
                            'title' => $o->getTitle(),
                            'link'  => $o->getLink() . '?searchparam=' . $term,
                            'image' => $o->getIconUrl()
                        ];
                
                        $x++;
                        if($x>=$iMax) break;
                    }
                }
                if(count($tmp)>0)
                {
                    $aResult[] = [
                        'type'  => 'oxmanufactuer',
                        'items' => $tmp
                    ];
                }
            }
        }
        return $aResult;
    }
}