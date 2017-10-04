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

<div class="box content">

    <div class="row">
        <div class="large-12 column">
            <h2>Generate</h2>
        </div>
    </div>

    <div class="search-filters theatre-diaries">
        <form method="post" action="/NodExport/Generate" id="nod-export-filter" class="clearfix">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
            <div class="row">
                <div class="large-12 column">
                    <div class="panel">
                        <div class="row">

                            <div class="large-10 column">
                                <div class="search-filters-extra audit-filters clearfix">
                                    <fieldset class="inline highlight">
                                        <label class="inline" for="date_from">From:</label>
                                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                                'name' => 'date_from',
                                                'id' => 'date_from',
                                                'options' => array(
                                                        'showAnim' => 'fold',
                                                                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                                            ),
                                                            'value' => Yii::app()->request->getParam('date_from'),
                                                            'htmlOptions' => array(
                                                                    'class' => 'small fixed-width',
                                                            ),
                                                    ))?>
                                        <label class="inline" for="date_to">To:</label>
                                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                                'name' => 'date_to',
                                                'id' => 'date_to',
                                                'options' => array(
                                                        'showAnim' => 'fold',
                                                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                                ),
                                                'value' => Yii::app()->request->getParam('date_to'),
                                                'htmlOptions' => array(
                                                        'class' => 'small fixed-width',
                                                ),
                                        ))?>

                                    </fieldset>
                                </div>
                            </div>
                            <div class="large-2 column text-right">
                                <img class="loader hidden" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" alt="loading..." style="margin-right:10px" />
                                <button type="submit" class="secondary long">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="box content">

    <div class="row">
        <div class="large-12 column">
            <h2>Instructions</h2>
        </div>
    </div>
   
    <div class="row">
        <div class="large-12 column">
            <div class="panel">
                <div class="row">
                    <div class="large-10 column">
                        Introduction<br>
                        This exporter will generate a .zip file in the National Ophthalmology Dataset (NOD) format. The .zip can then be submitted via <a href="http://www.nodaudit.org.uk">www.nodaudit.org.uk</a> for inclusion in the national audit

                        <br><br><br>

                        To run an export:<br><br>
                        <ul>
                            <li>Select a From and To date for the data that you would like to include in the export. Data for all operations occurring within this period will be included
                                <ul><li>If you wish to include data for all time, then From and To dates can be left blank</li></ul>
                            </li>
                            <li>Click the 'Generate' button</li>
                            <li>Wait for the report to be generated - This may take some time for systems with large amounts of data</li>
                            <li>Your browser will download a .zip file containing the export</li>
                        </ul>

                        <br>
                        To submit data to NOD for audit:<br><br>
                        The .zip file downloaded above must be uploaded via the <a href="http://www.nodaudit.org.uk">www.nodaudit.org.uk</a> website. You will first need to register on the site as a contributing centre, then follow instructions on that website for uploading your submission. When prompted to supply a file for upload, give the .zip downloaded above
                    </div>
                    <div class="large-2 column"></div>

                </div>
            </div>
        </div>
    </div>
</div>
