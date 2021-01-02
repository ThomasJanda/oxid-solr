<?php
$sMetadataVersion = '2.0';

$aModule = array(
    'id'          => 'rs-solr',
    'title'       => '*RS Solr',
    'description' => 'Implement solr connector',
    'thumbnail'   => '',
    'version'     => '0.0.5',
    'author'      => '',
    'url'         => '',
    'email'       => '',
    'extend'      => array(
        \OxidEsales\Eshop\Application\Controller\ArticleListController::class => \rs\solr\Application\Controller\ArticleListController::class,
        \OxidEsales\Eshop\Application\Controller\ManufacturerListController::class => \rs\solr\Application\Controller\ManufacturerListController::class,
        \OxidEsales\Eshop\Application\Controller\SearchController::class => \rs\solr\Application\Controller\SearchController::class,
        \OxidEsales\Eshop\Application\Model\Search::class => \rs\solr\Application\Model\Search::class,
        \OxidEsales\Eshop\Application\Model\ArticleList::class => \rs\solr\Application\Model\ArticleList::class,
        \OxidEsales\Eshop\Application\Controller\FrontendController::class => \rs\solr\Application\Controller\FrontendController::class,
        \OxidEsales\Eshop\Core\ViewConfig::class => rs\solr\Core\ViewConfig::class,
    ),
    'controllers' => array(
        'rs_solr_test' => \rs\solr\Application\Controller\Admin\rs_solr_test::class,
        'rs_solr_import' => \rs\solr\Application\Controller\Admin\rs_solr_import::class,
        'rs_solr_autosuggest' => \rs\solr\Application\Controller\AutoSuggestController::class,
    ),
    'templates'   => array(
        'rs_solr_test.tpl'   => 'rs/solr/views/admin/tpl/rs_solr_test.tpl',
        'rs_solr_import.tpl'   => 'rs/solr/views/admin/tpl/rs_solr_import.tpl',
        'rs/solr/views/tpl/page/search/search__search_header.tpl' => 'rs/solr/views/tpl/page/search/search__search_header.tpl',
        'rs/solr/views/tpl/page/search/search__search_results.tpl' => 'rs/solr/views/tpl/page/search/search__search_results.tpl',
        'rs/solr/views/tpl/layout/page__layout_header.tpl' => 'rs/solr/views/tpl/layout/page__layout_header.tpl',
        'rs/solr/views/tpl/widget/locator/attributes__widget_locator_attributes.tpl' => 'rs/solr/views/tpl/widget/locator/attributes__widget_locator_attributes.tpl',
        
        'rs/solr/views/tpl/widget/locator/attributes/checkbox_list.tpl' => 'rs/solr/views/tpl/widget/locator/attributes/checkbox_list.tpl',
        'rs/solr/views/tpl/widget/locator/attributes/range_slider.tpl' => 'rs/solr/views/tpl/widget/locator/attributes/range_slider.tpl',
        'rs/solr/views/tpl/widget/locator/attributes/range_slider_currency.tpl' => 'rs/solr/views/tpl/widget/locator/attributes/range_slider_currency.tpl',
        'rs/solr/views/tpl/widget/locator/attributes/range_slider_numeric.tpl' => 'rs/solr/views/tpl/widget/locator/attributes/range_slider_numeric.tpl',
        'rs/solr/views/tpl/widget/locator/attributes/selectbox.tpl' => 'rs/solr/views/tpl/widget/locator/attributes/selectbox.tpl',
        
    ),
    'blocks'      => array(
        array(
            'template' => 'page/search/search.tpl',
            'block'    => 'search_header',
            'file'     => '/views/blocks/page/search/search__search_header.tpl',
        ),
        array(
            'template' => 'page/search/search.tpl',
            'block'    => 'search_results',
            'file'     => '/views/blocks/page/search/search__search_results.tpl',
        ),
        array(
            'template' => 'widget/locator/attributes.tpl',
            'block'    => 'widget_locator_attributes',
            'file'     => '/views/blocks/widget/locator/attributes__widget_locator_attributes.tpl',
        ),
        array(
            'template' => 'layout/page.tpl', 
            'block' => 'layout_header', 
            'file' => '/views/blocks/layout/page__layout_header.tpl'
        )
    ),
    'settings'    => array(
        array(
            'group' => 'rs-solr_server',
            'name'  => 'rs-solr_server_host',
            'type'  => 'str',
            'value' => 'localhost',
        ),
        array(
            'group' => 'rs-solr_server',
            'name'  => 'rs-solr_server_port',
            'type'  => 'str',
            'value' => '8983',
        ),
        array(
            'group' => 'rs-solr_server',
            'name'  => 'rs-solr_server_path',
            'type'  => 'str',
            'value' => '/',
        ),
        array(
            'group' => 'rs-solr_server',
            'name'  => 'rs-solr_server_core',
            'type'  => 'str',
            'value' => 'oxid',
        ),
        

        array(
            'group' => 'rs-solr_suggest',
            'name'  => 'rs-solr_suggest_display_categories',
            'type'  => 'bool',
            'value' => true,
        ),    
        array(
            'group' => 'rs-solr_suggest',
            'name'  => 'rs-solr_suggest_display_manufacturers',
            'type'  => 'bool',
            'value' => false,
        ),  
        
        array(
            'group' => 'rs-solr_search',
            'name'  => 'rs-solr_search_display_categories',
            'type'  => 'bool',
            'value' => true,
        ),    
        array(
            'group' => 'rs-solr_search',
            'name'  => 'rs-solr_search_display_price',
            'type'  => 'bool',
            'value' => true,
        ),   
        array(
            'group' => 'rs-solr_search',
            'name'  => 'rs-solr_search_display_manufacturers',
            'type'  => 'bool',
            'value' => true,
        ),  
    ),
);