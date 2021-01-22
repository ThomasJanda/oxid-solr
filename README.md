# Oxid solr

## Description

Implement/replace search, inclusive suggest list, category listing and manufacturer listing 
with a solr instance. 

Used third party projects: 

    https://lucene.apache.org/solr/
    https://github.com/solariumphp/solarium
    http://ionden.com/a/plugins/ion.rangeSlider/demo.html

This extension is required and have to install first

    https://github.com/ThomasJanda/oxid-formedit
    https://github.com/ThomasJanda/oxid-cronjobmanager

This extension was created for Oxid 6.2, Wave theme.


## Install

1. Install solr server on your web space. Contact your hosting provider how you have to do this and how you can configure/get access to it.

2. Creates a "core" within the solr server (in this case, you create a core called "oxid")

    sudo su - solr -c "/opt/solr/bin/solr create -c oxid"
    /opt/solr/bin/solr config -c oxid -p 8983 -action set-user-property -property update.autoCreateFields -value false

3. Install extension in your shop

        composer config repositories.rs/solr git https://github.com/ThomasJanda/oxid-solr/
        composer require rs/solr:dev-master --update-no-dev --ignore-platform-reqs
        vendor/bin/oe-console oe:module:install-configuration source/modules/rs/solr

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
        
        CREATE TABLE `rssolr_update_articles` (
         `oxid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
         `rscreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
         PRIMARY KEY (`oxid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        CREATE TABLE `rssolr_import` (
         `oxid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
         `rsstart` datetime DEFAULT NULL,
         `rsend` datetime DEFAULT NULL,
         PRIMARY KEY (`oxid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        CREATE TABLE `rssolr_requests` (
         `oxid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
         `rscreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
         `rsparam_q` varchar(5000) NOT NULL,
         `rsparam_fq` varchar(250) NOT NULL,
         `rsparam_sort` varchar(250) NOT NULL,
         `rsresult_count` int(11) NOT NULL,
         `rsresult_error` varchar(1000) NOT NULL,
         `rscached` int(11) NOT NULL DEFAULT '0',
         `rsview` varchar(250) NOT NULL,
         PRIMARY KEY (`oxid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        CREATE TRIGGER rssolr_update_articles__oxarticles__insert AFTER INSERT ON oxarticles
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxid);
        
        CREATE TRIGGER rssolr_update_articles__oxarticles__update AFTER UPDATE ON oxarticles
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxid);
        
        CREATE TRIGGER rssolr_update_articles__oxarticles__delete AFTER DELETE ON oxarticles
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (OLD.oxid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2category__insert AFTER INSERT ON oxobject2category
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2category__update AFTER UPDATE ON oxobject2category
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2category__delete AFTER DELETE ON oxobject2category
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (OLD.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2attribute__insert AFTER INSERT ON oxobject2attribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2attribute__update AFTER UPDATE ON oxobject2attribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (NEW.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxobject2attribute__delete AFTER DELETE ON oxobject2attribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) values (OLD.oxobjectid);
        
        CREATE TRIGGER rssolr_update_articles__oxmanufacturers__insert AFTER INSERT ON oxmanufacturers
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxarticles.oxid from oxarticles where oxarticles.oxmanufacturersid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxmanufacturers__update AFTER UPDATE ON oxmanufacturers
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxarticles.oxid from oxarticles where oxarticles.oxmanufacturersid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxmanufacturers__delete AFTER DELETE ON oxmanufacturers
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxarticles.oxid from oxarticles where oxarticles.oxmanufacturersid=OLD.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxcategories__insert AFTER INSERT ON oxcategories
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2category.oxobjectid from oxobject2category where oxobject2category.oxcatnid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxcategories__update AFTER UPDATE ON oxcategories
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2category.oxobjectid from oxobject2category where oxobject2category.oxcatnid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxcategories__delete AFTER DELETE ON oxcategories
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2category.oxobjectid from oxobject2category where oxobject2category.oxcatnid=OLD.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxattribute__insert AFTER INSERT ON oxattribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2attribute.oxobjectid from oxobject2attribute where oxobject2attribute.oxattrid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxattribute__update AFTER UPDATE ON oxattribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2attribute.oxobjectid from oxobject2attribute where oxobject2attribute.oxattrid=NEW.oxid;
        
        CREATE TRIGGER rssolr_update_articles__oxattribute__delete AFTER DELETE ON oxattribute
        FOR EACH ROW replace into rssolr_update_articles (oxid) select oxobject2attribute.oxobjectid from oxobject2attribute where oxobject2attribute.oxattrid=OLD.oxid;


5. Enable extension in the oxid admin area, Extensions => Modules. Setup the credentials to the solr server.

6. Setup the solr instance with all necessary information. Create the columns/fields in solr.

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Setup".

7. Import data to solr (this has to be made from time to time to keep the solr instance actual)

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Delete all".

    In the oxid admin area, go to "reisacher software" => "Solr" => "Import". Press the button "Import".
