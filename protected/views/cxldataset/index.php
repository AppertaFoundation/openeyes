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

<div class="oe-full-header flex-layout">
    <div class="title wordcaps">CXL Dataset</div>
</div>

<div class="oe-full-content subgrid oe-nod-export">
    <nav class="oe-full-side-panel">
        <h3>Select Date range for CXL Dataset</h3>

        <div class="search-filters theatre-diaries">
            <form method="post" action="/CxlDataset/Generate" id="nod-export-filter" class="clearfix">
                <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
                <div class="cols-12 column">
                    <div class="panel">
                        <div class="data-group">
                            <div class="cols-10 column">
                                <div class="search-filters-extra audit-filters clearfix flex-layout">
                                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                        'name' => 'date_from',
                                        'id' => 'date_from',
                                        'options' => array(
                                            'showAnim' => 'fold',
                                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                        ),
                                                                  'value' => Yii::app()->request->getParam('date_from'),
                                                                  'htmlOptions' => array(
                                                                  'placeholder' => 'From',
                                                                  'class' => 'cols-5',
                                        ),
                                    )) ?>
                                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                        'name' => 'date_to',
                                        'id' => 'date_to',
                                        'options' => array(
                                            'showAnim' => 'fold',
                                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                        ),
                                        'value' => Yii::app()->request->getParam('date_to'),
                                        'htmlOptions' => array(
                                            'placeholder' => 'To',
                                            'class' => 'cols-5',
                                        ),
                                    )) ?>
                                </div>
                            </div>
                            <h4>Or, leave dates blank to collect data for all time.</h4>
                            <div class="row">
                                <img class="loader hidden"
                                     src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif'); ?>"
                                     alt="loading..." style="margin-right:10px"/>
                                <button type="submit" class="green hint cols-full">Generate CXL Dataset (.zip)</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <h4>Please wait for the report to be generated. <br>
            It may take some time for systems with large amounts of data</h4>

    </nav>
</div>
