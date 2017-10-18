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
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.css')?>">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('css/dashboard.css')?>">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/themes/base/minified/jquery.ui.datepicker.min.css')?>">

        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery/jquery.min.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.core.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.datepicker.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.js')?>"></script>
        <?php
        if(Yii::app()->controller->action->id === 'oescape'){ ?>
            <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/highstock.js')?>"></script>
        <?php } else { ?>
            <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/highcharts.js')?>"></script>
        <?php } ?>

        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/exporting.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/offline-exporting.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/highcharts-more.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/no-data-to-display.js')?>"></script>


        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="format-detection" content="telephone=no">
        <?php
        if(Yii::app()->controller->id === 'whiteboard'){ ?>
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
