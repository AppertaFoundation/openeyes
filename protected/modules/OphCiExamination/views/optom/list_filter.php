<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="search-filters">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'optomfeedbackmanager-filter',
        'htmlOptions' => array(
            'class' => 'row',
        ),
        'enableAjaxValidation' => false,
    ))?>
    <div class="large-12 column">
        <div class="panel box js-toggle-container">
            <h3></h3>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
                <span class="icon-showhide">
                    Show/hide this section
                </span>
            </a>
            <div class=" js-toggle-body">

                <div class="row" style="margin-bottom:10px;">
                    <div class="column large-4">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="date_from">Date From:</label></div>
                            <div class="column large-8">
                                <input type="text" id="date_from" name="date_from" class="datepicker filter-field"
                                       value="<?= array_key_exists('date_from', $list_filter) ? $list_filter['date_from'] : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="column large-4">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="status_id">Invoice Status:</label></div>
                            <div class="column large-8">
                                <?php
                                echo
                                CHtml::dropDownList('status_id',(array_key_exists('status_id', $list_filter) ? $list_filter['status_id'] : null),
                                    CHtml::listData(\OEModule\OphCiExamination\models\InvoiceStatus::model()->findAll( array('condition' => 'active = :active', 'params' => array(':active' => 1))), 'id', 'name'),
                                    array('class' => 'filter-field', 'empty' => 'All')
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="column large-4">
                        <div class="row">
                            <div class="column large-5 text-right"><label for="optometrist">Optometrist Name:</label></div>
                            <div class="column large-7">
                                <?php
                                echo CHtml::textField('optometrist', (array_key_exists('optometrist', $list_filter) ? $list_filter['optometrist'] : null),
                                    array(
                                        'id'=>'optometrist',
                                        'name'=>'optometrist'
                                    )
                                );
                                ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom:10px;">
                    <div class="column large-4">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="date_to">Date To:</label></div>
                            <div class="column large-8">
                                <input type="text" id="date_to" name="date_to" class="datepicker filter-field"
                                       value="<?= array_key_exists('date_to', $list_filter) ? $list_filter['date_to'] : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="column large-4">
                        <div class="column large-4  text-right"><label for="patient_number">Patient number:</label></div>
                        <div class="column large-8"><?php
                            echo CHtml::textField('patient_number', (array_key_exists('patient_number', $list_filter) ? $list_filter['patient_number'] : null),
                                array(
                                    'id'=>'patient_number',
                                    'name'=>'patient_number'
                                ));
                            ?>
                        </div>
                    </div>

                    <div class="column large-4">

                        <div class="row">
                            <div class="column large-5 text-right"><label for="goc_number">Optometrist GOC code:</label></div>
                            <div class="column large-7">
                                <?php
                                echo CHtml::textField('goc_number',  (array_key_exists('goc_number', $list_filter) ? $list_filter['goc_number'] : null),
                                    array(
                                        'id'=>'goc_number',
                                        'name'=>'goc_number'
                                    )
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="column large-12 text-right">
                        <?php if ($this->isListFiltered()) { ?>
                            <button id="reset_button" class="warning" type="submit">Reset</button>
                        <?php } ?>
                        <button id="search_button" class="secondary" type="submit" >Search</button>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
        <?php $this->endWidget()?>
    </div>
    <script type="text/javascript">
        $('#reset_button').on('click', function(e) {
            e.preventDefault();
            $('#date_from').val('');
            $('#date_to').val('');
            $('#status_id').val('');
            $('#patient_number').val('');
            $('#optometrist').val('');
            $('#goc_number').val('');
            $('#optomfeedbackmanager-filter').submit();
        });

        $('#optomfeedbackmanager-filter').on('change', '.filter-field', function() {
            $('#search_button').removeAttr('disabled');
        });

        $(document).ready(function() {
            $('.datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
        });
    </script>