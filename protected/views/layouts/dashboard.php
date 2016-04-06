<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<!doctype html>
<html lang="en">
    <head>
        <script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
        <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
        <link href="<?= Yii::app()->assetManager->createUrl('fonts/material-design/material-icons.css')?>" rel="stylesheet">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.css')?>">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('css/dashboard.css')?>">
        <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/themes/base/minified/jquery.ui.datepicker.min.css')?>">

        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery/jquery.min.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.core.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/ui/jquery.ui.datepicker.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/highcharts.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/exporting.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/offline-exporting.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/highcharts-more.js')?>"></script>
        <script src="<?= Yii::app()->assetManager->createUrl('components/highcharts/modules/no-data-to-display.js')?>"></script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>OpenEyes Analytics</title>

        <!-- Tile icon for Win8 (144x144 + tile color) -->
        <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
        <meta name="msapplication-TileColor" content="#3372DF">

        <link rel="shortcut icon" href="images/favicon.png">

        <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
        <!--
        <link rel="canonical" href="http://www.example.com/">
        -->


    </head>
    <body>
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
            <header class="mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
                <div class="mdl-layout__header-row">
                    <span class="mdl-layout-title">
                        <img src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/OpenEyes_logo_transparent.png')?>" alt="OpenEyes logo" />
                        Cataract Personal Audit: <?= $this->user->getFullNameAndTitle() ?>
                    </span>
                    <div class="mdl-layout-spacer"></div>
                    <form id="search-form">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" id="from-date" name="from">
                            <label class="mdl-textfield__label" for="from-date">From...</label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input class="mdl-textfield__input" type="text" id="to-date" name="to">
                            <label class="mdl-textfield__label" for="to-date">To...</label>
                        </div>
                        <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" name="action">Submit
                            <i class="material-icons right">send</i>
                        </button>
                        <span>
                            <i class="material-icons" id="export">print</i>
                        </span>
                    </form>

                </div>
            </header>
            <!--<div class="drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
                <header class="drawer-header ">
                    <img src="<?= Yii::app()->assetManager->createUrl('img/_structure/openeyes.png')?>" />
                    <span>OpenEyes Analytics</span>
                </header>
                <nav class="mdl-navigation mdl-color--blue-grey-800">
                    <a class="mdl-navigation__link" href=""><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">home</i>Home</a>
                    <a class="mdl-navigation__link mdl-js-button" href="" id="reports"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">sort</i>Reports</a>
                    <ul class="mdl-menu mdl-js-menu" for="reports">
                        <li>
                            <a class="mdl-navigation__link" href=""><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">sort</i>Cataract</a>
                        </li>
                    </ul>
                    <div class="mdl-layout-spacer"></div>
                    <a class="mdl-navigation__link" href=""><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">help_outline</i><span class="visuallyhidden">Help</span></a>
                </nav>
            </div>-->
            <main class="mdl-layout__content mdl-color--grey-100">
                <?php echo $content; ?>
            </main>
        </div>
    </body>
</html>
