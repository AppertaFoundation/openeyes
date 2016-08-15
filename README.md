# OphCoCvi

An OpenEyes event module to generate electronic CVI (Certificate of Visual Impairment)

## Configuration

Add the module to the application configuration:

    'OphCoCvi' => array('class' => '\OEModule\OphCoCvi\OphCoCviModule')

Install the following packages:

    sudo apt-get install libreoffice-core libreoffice-common libreoffice-writer php5-mcrypt

Run composer update (need to get the QR codes)

Add the following section with proper data into your config/local/common.php params section:
    'portal' => array(
                'uri' => 'http://api.localhost:8000',
                'endpoints' => array(
                    'auth' => '/oauth/access',
                    'examinations' => '/examinations/searches',
                    'signatures' => '/signatures/searches'
                ),
                'credentials' => array(
                    'username' => 'user@example.com',
                    'password' => 'apipass',
                    'grant_type' => 'password',
                    'client_id' => 'f3d259ddd3ed8ff3843839b',
                    'client_secret' => '4c7f6f8fa93d59c45502c0ae8c4a95b',
                ),
            ),

## Status

This module is in initial development and not intended for any use outside of core development. Feel free to take a look as code is developed though.

