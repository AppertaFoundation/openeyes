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
<h1 class="badge">Transport</h1>

<div class="box content">
  <div class="cols-12 column">
            <h2>TCIs for today onwards.</h2>

            <div class="box generic transport">

                <div class="data-group">
                    <div class="cols-6 column date-filter">
                        <form id="transport_form" method="post" action="<?php echo Yii::app()->createUrl('/OphTrOperationbooking/transport/index')?>">
                            <div class="data-group">
                                <label for="transport_date_from" class="inline align">
                                    From:
                                </label>
                                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                    'name' => 'transport_date_from',
                                    'id' => 'transport_date_from',
                                    'options' => array(
                                        'showAnim' => 'fold',
                                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                    ),
                                    'value' => @$_GET['date_from'],
                                    'htmlOptions' => array('class' => 'inline fixed-width'),
                                ))?>
                                <label for="transport_date_to" class="inline align">
                                    To:
                                </label>
                                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                    'name' => 'transport_date_to',
                                    'id' => 'transport_date_to',
                                    'options' => array(
                                        'showAnim' => 'fold',
                                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                    ),
                                    'value' => @$_GET['date_to'],
                                    'htmlOptions' => array('class' => 'inline fixed-width'),
                                ))?>
                                <button type="submit" class="small btn_transport_filter">
                                    Filter
                                </button>
                                <button type="submit" class="small btn_transport_viewall">
                                    View all
                                </button>
                                <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="loader" style="display: none;" />
                            </div>
                            <div class="data-group">
                                <fieldset class="inline">
                                    <legend>
                                        Include:
                                    </legend>
                                    <label class="inline">
                                        <input type="checkbox" name="include_bookings" id="include_bookings" class="filter" value="1"<?php if (@$_GET['include_bookings']) {?> checked="checked"<?php }?> />
                                        Bookings
                                    </label>
                                    <label class="inline">
                                        <input type="checkbox" name="include_reschedules" id="include_reschedules" class="filter" value="1"<?php if (@$_GET['include_reschedules']) {?> checked="checked"<?php }?> />
                                        Reschedules
                                    </label>
                                    <label class="inline">
                                        <input type="checkbox" name="include_cancellations" id="include_cancellations" class="filter" value="1"<?php if (@$_GET['include_cancellations']) {?> checked="checked"<?php }?> />
                                        Cancellations
                                    </label>
                                    <label class="inline">
                                        <input type="checkbox" name="include_completed" id="include_completed" class="filter" value="1"<?php if (@$_GET['include_completed']) {?> checked="checked"<?php }?> />
                                        Completed
                                    </label>
                                </fieldset>
                            </div>
                        </form>
                    </div>
                    <div class="cols-6 column end text-right">
                        <?php echo $this->renderPartial('/transport/_buttons')?>
                    </div>
                </div>

                <div id="searchResults">
                    <form id="csvform" method="post" class="csvform" action="<?php echo Yii::app()->createUrl('/OphTrOperationbooking/transport/downloadcsv')?>">
                        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>" />
                        <input type="hidden" name="date_from" value="<?=\CHtml::encode(@$_GET['date_from'])?>" />
                        <input type="hidden" name="date_to" value="<?=\CHtml::encode(@$_GET['date_to'])?>" />
                        <input type="hidden" name="include_bookings" value="<?php echo @$_GET['include_bookings'] ? 1 : 0?>" />
                        <input type="hidden" name="include_reschedules" value="<?php echo @$_GET['include_reschedules'] ? 1 : 0?>" />
                        <input type="hidden" name="include_cancellations" value="<?php echo @$_GET['include_cancellations'] ? 1 : 0?>" />
                        <input type="hidden" name="include_completed" value="<?php echo @$_GET['include_completed'] ? 1 : 0?>" />
                    </form>
                    <div id="transport_data">
                        <?php echo $this->renderPartial('/transport/_list_header')?>
                    </div>
                </div>
                <div class="text-right">
                    <?php echo $this->renderPartial('/transport/_buttons')?>
                </div>
            </div>
        </div>
</div>
