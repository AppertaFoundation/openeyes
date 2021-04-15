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
    <div class="title wordcaps">Audit</div>
    <div>
        <!-- no header buttons -->
    </div>
</div>
<div class="oe-full-content subgrid oe-audit">
    <form method="post" action="/audit/search" id="auditList-filter" class="clearfix">
        <input type="hidden" id="previous_site_id" value="<?= \Yii::app()->request->getPost('site_id') ?>" />
        <input type="hidden" id="previous_institution_id" value="<?= \Yii::app()->request->getPost('institution_id') ?>" />
        <input type="hidden" id="previous_firm_id" value="<?= \Yii::app()->request->getPost('firm_id') ?>" />
        <input type="hidden" id="previous_user_id" value="<?= \Yii::app()->request->getPost('user') ?>" />
        <input type="hidden" id="previous_action" value="<?= \Yii::app()->request->getPost('action') ?>" />
        <input type="hidden" id="previous_target_type" value="<?= \Yii::app()->request->getPost('target_type') ?>" />
        <input type="hidden" id="previous_event_type_id" value="<?= \Yii::app()->request->getPost('event_type_id') ?>" />
        <input type="hidden" id="previous_date_from" value="<?= \Yii::app()->request->getPost('date_from') ?>" />
        <input type="hidden" id="previous_date_to" value="<?= \Yii::app()->request->getPost('date_to') ?>" />
        <input type="hidden" id="previous_patient_identifier_value" value="<?= \Yii::app()->request->getPost('patient_identifier_value') ?>" />
        <?= $this->renderPartial('_filters');?>

        <div id="search-loading-msg" class="large-12 column hidden">
            <div class="alert-box">
                <img src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" class="spinner" /> <strong>Searching, please wait...</strong>
            </div>
        </div>
    </form>
    <main id="searchResults" class="oe-full-main audit-main"></main>
</div>

<script type="text/javascript">
    $(function() {

        var loadingMsg = $('#search-loading-msg');

        handleButton($('#auditList-filter button[type="submit"]'),function(e) {
            loadingMsg.show();
            $('#searchResults').empty();

            $('#page').val(1);

            $.ajax({
                'url': '<?= Yii::app()->createUrl('audit/search'); ?>',
                'type': 'POST',
                'data': $('#auditList-filter').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                'success': function(data) {
                    $('#previous_site_id').val($('#site_id').val());
                    $('#previous_institution_id').val($('#institution_id').val());
                    $('#previous_firm_id').val($('#firm_id').val());
                    $('#previous_user').val($('#user').val());
                    $('#previous_action').val($('#action').val());
                    $('#previous_target_type').val($('#target_type').val());
                    $('#previous_event_type_id').val($('#event_type_id').val());
                    $('#previous_date_from').val($('#date_from').val());
                    $('#previous_date_to').val($('#date_to').val());

                    var s = data.split('<!-------------------------->');

                    $('#searchResults').html(s[0]);
                    $('.pagination').html(s[1]).show();

                    enableButtons();
                },
                'complete': function() {
                    loadingMsg.hide();
                }
            });

            e.preventDefault();
        });
    });

    $(document).ready(function() {

        $('#auto_update_toggle').click(function() {
            if ($(this).text().match(/update on/)) {
                $(this).text('Auto update off');
                auditLog.run = false;
            } else {
                $(this).text('Auto update on');
                auditLog.run = true;
                auditLog.refresh();
            }
            return false;
        });
    });

    $('#date_from').bind('change',function() {
        $('#date_to').datepicker('option','minDate',$('#date_from').datepicker('getDate'));
    });

    $('#date_to').bind('change',function() {
        $('#date_from').datepicker('option','maxDate',$('#date_to').datepicker('getDate'));
    });
</script>