# Oxid solr

## Description

Implement/replace search, inclusive suggest list, categorie listing and manufacturer listing 
trought a solr  instance. 

Used third party projects: 

    https://lucene.apache.org/solr/
    https://github.com/solariumphp/solarium
    http://ionden.com/a/plugins/ion.rangeSlider/demo.html

This extension is required and have to install first

    https://github.com/ThomasJanda/oxid-formedit

This extension was created for Oxid 6.2, Wave theme.


## Install

1. Install solr server on your web space. Contact your hosting provider how you have to do this and how you can configure/get access to it.

        Example for hoster "ProfiHost" you can find a "howto" here https://wissen.profihost.com/wissen/artikel/wie-kann-ich-apache-solr-installieren/

2. Create core within the solr server (in this case, you create a core called "oxid")

        #PATH TO SOLR INSTALLATION FOLDER#/bin/solr create -c oxid
        #PATH TO SOLR INSTALLATION FOLDER#/bin/solr config -c oxid -p 8983 -action set-user-property -property update.autoCreateFields -value false

3. Install module in your shop

        composer config repositories.rs/solr git https://github.com/ThomasJanda/oxid-solr/
        composer require rs/solr:dev-master --update-no-dev --ignore-platform-reqs
        If you get ask, override files, press "n".

4. Execute following within your DB environment

        CREATE TABLE `rssolr_facets_categories` (
         `oxid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
         `rsfacete` char(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
         `rstype` enum('checkbox_list','range_slider','range_slider_numeric','range_slider_currency','selectbox','custom_template') NOT NULL DEFAULT 'checkbox_list',
         `rscustom` varchar(50) DEFAULT NULL,
         `rssort` int(11) NOT NULL DEFAULT 0,
         `f_oxcategories` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
         PRIMARY KEY (`oxid`),
         KEY `f_oxcategories` (`f_oxcategories`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `rssolr_facets_manufacturers` (
         `oxid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
         `rsfacete` char(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
         `rstype` enum('checkbox_list','range_slider','range_slider_numeric','range_slider_currency','selectbox','custom_template') NOT NULL DEFAULT 'checkbox_list',
         `rscustom` varchar(50) DEFAULT NULL,
         `rssort` int(11) NOT NULL DEFAULT 0,
         `f_oxmanufacturer` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
         PRIMARY KEY (`oxid`),
         KEY `f_oxcategories` (`f_oxmanufacturer`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

4. Enable module in the oxid admin area, Extensions => Modules. Setup the credentials to the solr server.

5. Setup the solr instance with all nessecary information. Create the columns/fields in solr.

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Setup".

6. Import data to solr (this has to be made from time to time to keep the solr instance actuall)

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Delete all".

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Import".
