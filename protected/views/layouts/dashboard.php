<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<!doctype html>
<html lang="en">
    <head>
        <script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
        <link href="<?= Yii::app()->assetManager->createUrl('fonts/Roboto/roboto.css')?>" rel="stylesheet">
        <link href="<?= Yii::app()->assetManager->createUrl('fonts/material-design/material-icons.css')?>" rel="stylesheet">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/themes/base/minified/jquery.ui.datepicker.min.css')?>">

        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery/jquery.min.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.core.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.datepicker.js')?>"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
        <?php

        // Override scaling based ondevice type. currently there is no way to identify the exact device,
        // so we can only approximate using the User Agent, which can only tell us if it is an iPhone, iPad, Android, Windows, etc.
        // The values below target the standard (i.e, cheapest) current iPad and the iPhone 12 (standard version, not max)
        // 0.5 is our current default value, which supports older devices of 600px width (e.g., cheap samsung galaxy tablets)
        // For other devices and widths, see: https://www.mydevice.io/#compare-devices.
        // To calculate scaling, divide the CSS width by 1200 (which is our minimum supported width for OpenEyes)
                $ua = $_SERVER['HTTP_USER_AGENT'];
                $initial_scale = '0.5';

        if (str_contains($ua, 'iPad')) {
            $initial_scale = '0.675';
        } elseif (str_contains($ua, 'iPhone')) {
            $initial_scale = '0.325';
        } else {
            $initial_scale = "0.5";
        }
        ?>

        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=<?= $initial_scale ?>">

        <meta name="format-detection" content="telephone=no">
        <?php
        if (Yii::app()->controller->id === 'whiteboard') { ?>
            <title>Whiteboard</title>
        <?php } else { ?>
            <title>OpenEyes Analytics</title>
        <?php } ?>

        <!-- Tile icon for Win8 (144x144 + tile color) -->
        <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
        <meta name="msapplication-TileColor" content="#3372DF">

        <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
        <!--
        <link rel="canonical" href="http://www.example.com/">
        -->


    </head>
    <body>
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
            <header class="mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
                <?php $this->renderPartial($this->getHeaderTemplate());?>
            </header>
                <?php echo $content; ?>
        </div>
        <div id="dialog-container"></div>
    </body>
</html>
