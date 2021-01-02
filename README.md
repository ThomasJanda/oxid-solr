# Oxid solr

## Description

Implement/replace search, inclusive suggest list, categorie listing and manufacturer listing 
trought a solr  instance. 

    https://lucene.apache.org/solr/
    https://github.com/solariumphp/solarium

This extension was created for Oxid 6.2, Wave theme.


## Install

1. Install solr server on your web space. Contact your hosting provider how you 
have to do this and how you can configure/get access to it.

2. Install module in your shop

        composer config repositories.rs/solr git https://github.com/ThomasJanda/oxid-solr/
        composer require rs/solr:dev-master --update-no-dev --ignore-platform-reqs

6. 
        
6. Enable module in the oxid admin area, Extensions => Modules
7. Changes settings in the module itself



## Manual optimization

Add to .htaccess at the end of the file

        #cpOptimization module start
        <IfModule mod_deflate.c>
            AddOutputFilterByType DEFLATE text/html
            AddOutputFilterByType DEFLATE text/css
            AddOutputFilterByType DEFLATE text/javascript
            AddOutputFilterByType DEFLATE application/javascript
            AddOutputFilterByType DEFLATE application/x-javascript
            AddOutputFilterByType DEFLATE image/svg+xml
            AddOutputFilterByType DEFLATE application/javascript
            AddOutputFilterByType DEFLATE application/rss+xml
            AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
            AddOutputFilterByType DEFLATE application/x-font
            AddOutputFilterByType DEFLATE application/x-font-opentype
            AddOutputFilterByType DEFLATE application/x-font-otf
            AddOutputFilterByType DEFLATE application/x-font-truetype
            AddOutputFilterByType DEFLATE application/x-font-ttf
            AddOutputFilterByType DEFLATE application/x-javascript
            AddOutputFilterByType DEFLATE application/xhtml+xml
            AddOutputFilterByType DEFLATE application/xml
            AddOutputFilterByType DEFLATE font/opentype
            AddOutputFilterByType DEFLATE font/otf
            AddOutputFilterByType DEFLATE font/ttf
            AddOutputFilterByType DEFLATE image/x-icon
            AddOutputFilterByType DEFLATE text/plain
            AddOutputFilterByType DEFLATE text/xml
        </IfModule>
        <IfModule mod_headers.c>
            <FilesMatch "\.(eot|svg|ttf|woff|woff2)$">
                Header set Cache-Control "max-age=15552000‬, public"
            </FilesMatch>
            <FilesMatch "\.(ico|jpg|jpeg|png|gif|swf)$">
                Header set Cache-Control "max-age=15552000, public"
            </FilesMatch>
            <FilesMatch "\.(css|js)$">
                Header set Cache-Control "max-age=15552000, public"
            </FilesMatch>
            Header unset ETag
        </IfModule>
        FileETag None
        ServerSignature Off
        #cpOptimization module end
    







execute on shop root
composer require solarium/solarium

https://solarium.readthedocs.io/en/stable/queries/update-query/the-result-of-an-update-query/

create new solr core:
sudo su - solr -c "/opt/solr/bin/solr create -c oxid -n data_driven_schema_configs"

sudo su - solr -c "/opt/solr/bin/solr create -c oxid"
/opt/solr/bin/solr config -c oxid -p 8983 -action set-user-property -property update.autoCreateFields -value false




//range slider
http://ionden.com/a/plugins/ion.rangeSlider/demo.html



# oxid6.2

cli: 
vendor/bin/oe-console oe:module:install-configuration source/modules/rs/solr





## Install

1. Copy files into following directory

        source/modules/rs/solr

        
2. Add following to composer.json on the shop root

        "autoload": {
            "psr-4": {
                "rs\\solr\\": "./source/modules/rs/solr"
            }
        },
    
3. Refresh autoloader files with composer.

        composer dump-autoload
        
4. Enable module in the oxid admin area, Extensions => Modules