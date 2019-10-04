<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="search-filters">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'optomfeedbackmanager-filter',
        'htmlOptions' => array(
            'class' => 'data-group',
        ),
        'enableAjaxValidation' => false,
    )) ?>
    <div class="cols-12 column">
        <div class="panel box js-toggle-container">
            <h3></h3>

            <div class=" js-toggle-body">


                <h3>Filter by Date</h3>
                <div class="flex-layout">
                    <fieldset>
                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'name' => 'date_from',
                            'id' => 'date_from',
                            'options' => array(
                                'showAnim' => 'fold',
                                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                            ),
                            'value' => array_key_exists('date_from', $list_filter) ? $list_filter['date_from'] : '',
                            'htmlOptions' => array(
                                'class' => 'cols-5',
                                'placeholder' => 'from'
                            ),
                        )) ?>
                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'name' => 'date_to',
                            'id' => 'date_to',
                            'options' => array(
                                'showAnim' => 'fold',
                                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                            ),
                            'value' => array_key_exists('date_to', $list_filter) ? $list_filter['date_to'] : '',
                            'htmlOptions' => array(
                                'class' => 'cols-5',
                                'placeholder' => 'to'
                            ),
                        )) ?>
                    </fieldset>
                </div>
                <h3>Invoice Status:</h3>
                <?php
                echo
                CHtml::dropDownList('status_id', (array_key_exists('status_id', $list_filter) ? $list_filter['status_id'] : null),
                    CHtml::listData(\OEModule\OphCiExamination\models\InvoiceStatus::model()->findAll(array('condition' => 'active = :active', 'params' => array(':active' => 1))), 'id', 'name'),
                    array('class' => 'filter-field cols-full', 'empty' => 'All')
                );
                ?>
                <h3>Optometrist Name:</h3>
                <?php
                echo CHtml::textField('optometrist', (array_key_exists('optometrist', $list_filter) ? $list_filter['optometrist'] : null),
                    array(
                        'id' => 'optometrist',
                        'name' => 'optometrist',
                        'class' => 'cols-full'
                    )
                );
                ?>
                <h3>Patient number:</h3>
                <?php echo CHtml::textField('patient_number', (array_key_exists('patient_number', $list_filter) ? $list_filter['patient_number'] : null),
                    array(
                        'id' => 'patient_number',
                        'name' => 'patient_number',
                        'class' => 'cols-full',
                    ));
?>

                <h3>Optometrist GOC code:
                    <h3>
                        <?php echo CHtml::textField('goc_number', (array_key_exists('goc_number', $list_filter) ? $list_filter['goc_number'] : null),
                            array(
                                'id' => 'goc_number',
                                'name' => 'goc_number',
                                'class' => 'cols-full'
                            )
                        );
?>
                        <?php if ($this->isListFiltered()) { ?>
                            <h3></h3>
                            <button id="reset_button" class="warning" type="submit">Reset</button>
                        <?php } ?>
                        <h3></h3>
                        <button id="search_button" class="secondary" type="submit">Search</button>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>
</div>
<?php $this->endWidget() ?>
<script type="text/javascript">
    $('#reset_button').on('click', function (e) {
        e.preventDefault();
        $('#date_from').val('');
        $('#date_to').val('');
        $('#status_id').val('');
        $('#patient_number').val('');
        $('#optometrist').val('');
        $('#goc_number').val('');
        $('#optomfeedbackmanager-filter').submit();
    });

    $('#optomfeedbackmanager-filter').on('change', '.filter-field', function () {
        $('#search_button').removeAttr('disabled');
    });

    $(document).ready(function () {
        $('.datepicker').datepicker({'showAnim': 'fold', 'dateFormat': 'd M yy'});
    });
</script>