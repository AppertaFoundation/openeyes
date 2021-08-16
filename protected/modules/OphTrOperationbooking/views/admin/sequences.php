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
<?php
$pagination = $sequences['pagination'];
$sequences = $sequences['data'];

if ($this->checkAccess('admin')) {
    $theatres = OphTrOperationbooking_Operation_Theatre::model()->active()->findAll();
} else {
    $theatres = OphTrOperationbooking_Operation_Theatre::getTheatresForCurrentInstitution();
}
?>
<div class="box admin">
    <h2>Filters</h2>
    <form id="admin_sequences_filters" class="panel">
        <table class="standard">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-2">
                <col class="cols-1">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr>
                <td><?=\CHtml::dropDownList('firm_id', @$_GET['firm_id'], Firm::model()->getListWithSpecialtiesAndEmergency(), array('empty' => '- ' . Firm::contextLabel() . ' -', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('theatre_id', @$_GET['theatre_id'], CHtml::listData($theatres, 'id', 'name'), array('empty' => '- Theatre -', 'class'=>'cols-full'))?></td>
                <td>From</td>
                <td>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'date_from',
                        'id' => 'date_from',
                        // additional javascript options for the date picker plugin
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => @$_GET['date_from'],
                    ))?>
                </td>
                <td>To</td>
                <td>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'date_to',
                        'id' => 'date_to',
                        // additional javascript options for the date picker plugin
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => @$_GET['date_to'],
                    ))?>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="standard">
            <tbody>
            <tr>
                <td><?=\CHtml::dropDownList('interval_id', @$_GET['interval_id'], CHtml::listData(OphTrOperationbooking_Operation_Sequence_Interval::model()->findAll(array()), 'id', 'name'), array('empty' => '- Interval -', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('weekday', @$_GET['weekday'], array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'), array('empty' => '- Weekday ', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('consultant', @$_GET['consultant'], array(1 => 'Yes', 0 => 'No'), array('empty' => '- Consultant -', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('paediatric', @$_GET['paediatric'], array(1 => 'Yes', 0 => 'No'), array('empty' => '- Paediatric -', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('anaesthetist', @$_GET['anaesthetist'], array(1 => 'Yes', 0 => 'No'), array('empty' => '- Anaesthetist -', 'class'=>'cols-full'))?></td>
                <td><?=\CHtml::dropDownList('general_anaesthetic', @$_GET['general_anaesthetic'], array(1 => 'Yes', 0 => 'No'), array('empty' => '- General anaesthetic -', 'class'=>'cols-full'))?></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo CHtml::button(
                        'Filter',
                        [
                            'class' => 'button large header-tab',
                            'name' => 'filter',
                            'type' => 'submit',
                            'id' => 'et_filter'
                        ]
                    ); ?>
                    <?php echo CHtml::button(
                        'Reset',
                        [
                            'class' => 'button large header-tab',
                            'name' => 'reset',
                            'type' => 'submit',
                            'id' => 'et_reset'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>

    <h2>Sequences</h2>
    <form id="admin_sequences">
        <input type="hidden" id="select_all" value="0" />

        <?php if ($pagination->getCurrentPage() !== $pagination->getPageCount()) {?>
            <div class="alert-box checkall_message" style="display: none;">
                <span class="column_checkall_message">
                    All <?php echo count($sequences)?> sequences on this page are selected. <a href="#" id="select_all_items">Select all <?php echo $pagination->getItemCount();?> sequences that match the current search criteria</a>
                </span>
            </div>
        <?php }?>
        <?php if (count($sequences) < 1) {?>
            <div class="alert-box alert with-icon no_results">
                <span class="column_no_results">
                    No items matched your search criteria.
                </span>
            </div>
        <?php }?>

        <table class="standard">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkall" class="sequences" /></th>
                    <th><?=\CHtml::link(Firm::contextLabel(), $this->getUri(array('sortby' => 'firm')))?></th>
                    <th><?=\CHtml::link('Theatre', $this->getUri(array('sortby' => 'theatre')))?></th>
                    <th><?=\CHtml::link('Dates', $this->getUri(array('sortby' => 'dates')))?></th>
                    <th><?=\CHtml::link('Time', $this->getUri(array('sortby' => 'time')))?></th>
                    <th><?=\CHtml::link('Interval', $this->getUri(array('sortby' => 'interval')))?></th>
                    <th><?=\CHtml::link('Weekday', $this->getUri(array('sortby' => 'weekday')))?></th>
                    <th>Attributes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($sequences as $i => $sequence) {?>
                    <tr class="clickable" data-id="<?php echo $sequence->id?>" data-uri="OphTrOperationbooking/admin/editSequence/<?php echo $sequence->id?>">
                        <td><input type="checkbox" name="sequence[]" value="<?php echo $sequence->id?>" class="sequences" /></td>
                        <td><?php echo $sequence->firm ? $sequence->firm->nameAndSubspecialtyCode : 'Emergency'?></td>
                        <td><?php echo $sequence->theatre->name?></td>
                        <td><?php echo $sequence->dates?></td>
                        <td><?php echo $sequence->start_time?> - <?php echo $sequence->end_time?><br/>adm: <?php echo $sequence->default_admission_time?></td>
                        <td><?php echo $sequence->interval->name?></td>
                        <td><?php echo $sequence->weekdayText?></td>
                        <td>
                            <span class="<?php echo $sequence->consultant ? 'set' : 'notset'?>">CON</span>
                            <span class="<?php echo $sequence->paediatric ? 'set' : 'notset'?>">PAED</span>
                            <span class="<?php echo $sequence->anaesthetist ? 'set' : 'notset'?>">ANA</span>
                            <span class="<?php echo $sequence->general_anaesthetic ? 'set' : 'notset'?>">GA</span>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="8">
                        <?php echo EventAction::button('Add', 'add_sequence', null, array('class' => 'small'))->toHtml()?>
                        <?php echo EventAction::button('Delete', 'delete_sequence', null, array('class' => 'small'))->toHtml()?>
                        &nbsp;&nbsp;&nbsp;
                        <?php echo EventAction::button('Generate sessions', 'generate_sessions', null, array('class' => 'small'))->toHtml()?>
                        <?php echo $this->renderPartial('//admin/_pagination', array(
                            'pagination' => $pagination,
                        ))?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>

    <?php echo $this->renderPartial('//base/_messages')?>

    <div class="alert-box hide" id="update_inline">
        <a href="#">Update selected sequences</a>
    </div>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'inline_edit',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('style' => 'display: none;', 'class' => 'panel'),
    ))?>
        <div class="data-group">
            <div class="cols-2 column">
                <label for=""><?php echo Firm::contextLabel() ?>:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_firm_id', '', Firm::model()->getListWithSpecialties(), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label for="">Theatre:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_theatre_id', '', CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->active()->findAll(), 'id', 'name'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label for="">Start date:</label>
            </div>
            <div class="cols-2 column end">
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'inline_start_date',
                        'id' => 'inline_start_date',
                        // additional javascript options for the date picker plugin
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => '',
                ))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label for="">End date:</label>
            </div>
            <div class="cols-2 column end">
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'inline_end_date',
                        'id' => 'inline_end_date',
                        // additional javascript options for the date picker plugin
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => '',
                ))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label for="">Start time:</label>
            </div>
            <div class="cols-2 column end">
                <?=\CHtml::textField('inline_start_time', '', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 10))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>End time:</label>
            </div>
            <div class="cols-2 column end">
                <?=\CHtml::textField('inline_end_time', '', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 10))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Interval:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_interval_id', '', CHtml::listData(OphTrOperationbooking_Operation_Sequence_Interval::model()->findAll(array()), 'id', 'name'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Weekday:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_weekday', '', array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Consultant:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_consultant', '', array(1 => 'Yes', 0 => 'No'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Paediatric:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_paediatric', '', array(1 => 'Yes', 0 => 'No'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Anaesthetist:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_anaesthetist', '', array(1 => 'Yes', 0 => 'No'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>General anaesthetic:</label>
            </div>
            <div class="cols-5 column end">
                <?=\CHtml::dropDownList('inline_general_anaesthetic', '', array(1 => 'Yes', 0 => 'No'), array('empty' => '- Don\'t change -'))?>
                <span class="error"></span>
            </div>
        </div>
        <div class="data-group">
            <div class="cols-2 column">
                <label>Week selection:</label>
            </div>
            <div class="cols-5 column end">
                <div class="data-group">
                    <?=\CHtml::dropDownList('inline_update_weeks', '', array(0 => 'Don\'t change', 1 => 'Change'))?>
                    <span class="inline_weeks" style="display: none;">
                        &nbsp;&nbsp;
                    </span>
                </div>
                <fieldset class="data-group">
                    <input type="hidden" name="inline_week1" value="0" />
                    <label class="inline"><input type="checkbox" name="inline_week1" value="1" /> 1st</label>
                    <input type="hidden" name="inline_week2" value="0" />
                    <label class="inline"><input type="checkbox" name="inline_week2" value="1" /> 2nd</label>
                    <input type="hidden" name="inline_week3" value="0" />
                    <label class="inline"><input type="checkbox" name="inline_week3" value="1" /> 3rd</label>
                    <input type="hidden" name="inline_week4" value="0" />
                    <label class="inline"><input type="checkbox" name="inline_week4" value="1" /> 4th</label>
                    <input type="hidden" name="inline_week5" value="0" />
                    <label class="inline"><input type="checkbox" name="inline_week5" value="1" /> 5th</label>
                </fieldset>
            </div>
        </div>
  <div class="cols-10 large-offset-2 column">
        <?php echo EventAction::button('Update', 'update_inline', array('colour' => 'green'))->toHtml()?>
    <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
  </div>
    <?php $this->endWidget()?>
</div>

<div id="confirm_delete_sequences" title="Confirm delete sequence" style="display: none;">
    <div id="delete_sequences">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the sequences from the system.<br/>This action cannot be undone.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="medication_id" value="" />
            <button type="submit" class="warning btn_remove_sequences">Remove sequence(s)</button>
            <button type="submit" class="secondary btn_cancel_remove_sequences">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
</div>
<script type="text/javascript">
    handleButton($('#et_filter'),function(e) {
        e.preventDefault();

        var filterParams = $('#admin_sequences_filters').serialize();
        var urlParams = $(document).getUrlParams();

        for (var key in urlParams) {
            if (inArray(key,["page","sortby","order"])) {
                filterParams += "&"+key+"="+urlParams[key];
            }
        }

        window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences?'+filterParams;
    });

    handleButton($('#et_reset'),function(e) {
        e.preventDefault();

        var filterFields = $('#admin_sequences_filters').serialize().match(/([a-z_]+)=/g);
        params = $(document).getUrlParams();
        for (var i in filterFields) {
            delete params[filterFields[i].replace(/=/,'')];
        }
        delete params['filter'];

        params = $.param(params);

        if (params != '?=') {
            window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences?'+params+"&reset=1";
        } else {
            window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences?reset=1';
        }
    });

    $('#inline_update_weeks').change(function() {
        if ($(this).val() == 1) {
            $('.inline_weeks').show();
        } else {
            $('.inline_weeks').hide();
        }
    });

    $('#update_inline').click(function(e) {
        e.preventDefault();
        $('#inline_edit').toggle('fast');
    });

    $('input[name="sequence[]"]').click(function() {
        if ($('input[name="sequence[]"]:checked').length >0) {
            $('#update_inline').show();
        } else {
            $('#update_inline').hide();
        }
    });

    $('#checkall').unbind('click').click(function() {
        $('input.'+$(this).attr('class')).attr('checked',$(this).is(':checked') ? 'checked' : false);
        if ($(this).is(':checked')) {
            $('#update_inline').show();
            $('.checkall_message').show();
        } else {
            $('#update_inline').hide();
            $('#select_all').val(0);
            $('.checkall_message').hide();
            $('span.column_checkall_message').html("All <?php echo $pagination->getPageSize();?> sequences on this page are selected. <a href=\"#\" id=\"select_all_items\">Select all <?php echo $pagination->getItemCount();?> sequences that match the current search criteria</a>");
        }
    });

    handleButton($('#et_update_inline'),function(e) {
        e.preventDefault();

        $('span.error').html('');

        if ($('#select_all').val() == 0) {
            var data = $('#admin_sequences').serialize()+"&"+$('#inline_edit').serialize();
        } else {
            var data = $('#admin_sequences_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
        }

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': baseUrl+'/OphTrOperationbooking/admin/sequenceInlineEdit',
            'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(errors) {
                var count = 0;
                for (var field in errors) {
                    $('#inline_'+field).next('span.error').html(errors[field]);
                    count += 1;
                }
                if (count >0) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "There were problems with the entries you made, please correct the errors and try again."
                    }).open();
                    enableButtons();
                } else {
                    window.location.reload();
                }
            }
        });
    });

    $('#select_all_items').live('click',function(e) {
        e.preventDefault();
        $('#select_all').val(1);
        $('span.column_checkall_message').html("All <?php echo $pagination->getItemCount();?> sequences that match the current search criteria are selected. <a href=\"#\" id=\"clear_selection\">Clear selection</a>");
    });

    $('#clear_selection').live('click',function(e) {
        e.preventDefault();
        $('#select_all').val(0);
        $('#checkall').removeAttr('checked');
        $('input[type="checkbox"][name="sequence[]"]').removeAttr('checked');
        $('.checkall_message').hide();
        $('span.column_checkall_message').html("All <?php echo $pagination->getPageSize();?> sequences on this page are selected. <a href=\"#\" id=\"select_all_items\">Select all <?php echo $pagination->getItemCount();?> sequences that match the current search criteria</a>");
        $('#update_inline').hide();
    });

    handleButton($('#et_delete_sequence'),function(e) {
        e.preventDefault();

        if ($('#select_all').val() == 0 && $('input[type="checkbox"][name="sequence[]"]:checked').length <1) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select the sequence(s) you wish to delete."
            }).open();
            enableButtons();
            return;
        }

        if ($('#select_all').val() == 0) {
            var data = $('#admin_sequences').serialize()+"&"+$('#inline_edit').serialize();
        } else {
            var data = $('#admin_sequences_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSequences',
            'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    enableButtons();

                    if ($('input[type="checkbox"][name="sequence[]"]:checked').length == 1) {
                        $('#confirm_delete_sequences').attr('title','Confirm delete sequence');
                        $('#delete_sequences').children('div').children('strong').html("WARNING: This will remove the sequence from the system.<br/><br/>This action cannot be undone.");
                        $('.btn_remove_sequences').children('span').text('Remove sequence');
                    } else {
                        $('#confirm_delete_sequences').attr('title','Confirm delete sequences');
                        $('#delete_sequences').children('div').children('strong').html("WARNING: This will remove the sequences from the system.<br/><br/>This action cannot be undone.");
                        $('.btn_remove_sequences').children('span').text('Remove sequences');
                    }

                    $('#confirm_delete_sequences').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected sequences have sessions with active bookings and so cannot be deleted."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    $('.btn_cancel_remove_sequences').click(function(e) {
        e.preventDefault();
        $('#confirm_delete_sequences').dialog('close');
    });

    handleButton($('.btn_remove_sequences'),function(e) {
        e.preventDefault();

        if ($('#select_all').val() == 0) {
            var data = $('#admin_sequences').serialize()+"&"+$('#inline_edit').serialize();
        } else {
            var data = $('#admin_sequences_filters').serialize()+"&"+$('#inline_edit').serialize()+"&use_filters=1";
        }

        // verify again as a precaution against race conditions
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSequences',
            'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    $.ajax({
                        'type': 'POST',
                        'url': baseUrl+'/OphTrOperationbooking/admin/deleteSequences',
                        'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                        'success': function(resp) {
                            if (resp == "1") {
                                window.location.reload();
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error deleting the sequences, please try again or contact support for assistance",
                                    onClose: function() {
                                        enableButtons();
                                        $('#confirm_delete_sequences').dialog('close');
                                    }
                                }).open();
                            }
                        }
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected sequences now have sessions with active bookings and so cannot be deleted.",
                        onClose: function() {
                            enableButtons();
                            $('#confirm_delete_sequences').dialog('close');
                        }
                    }).open();
                }
            }
        });
    });

    handleButton($('#et_generate_sessions'),function(e) {
        e.preventDefault();

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/viewSequences',
            'data': "generateSessions=1&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    window.location.reload();
                } else {
                    alert("Something went wrong generating the sessions, please try again or contact support for assistance.");
                }
            }
        });
    });
</script>
