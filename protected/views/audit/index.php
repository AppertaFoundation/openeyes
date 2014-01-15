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

<h1 class="badge">Audit logs</h1>

<div class="box content">
	<form method="post" action="/audit/search" id="auditList-filter" class="clearfix">
		<input type="hidden" id="previous_site_id" value="<?php echo @$_POST['site_id']?>" />
		<input type="hidden" id="previous_firm_id" value="<?php echo @$_POST['firm_id']?>" />
		<input type="hidden" id="previous_user" value="<?php echo @$_POST['user']?>" />
		<input type="hidden" id="previous_action" value="<?php echo @$_POST['action']?>" />
		<input type="hidden" id="previous_target_type" value="<?php echo @$_POST['target_type']?>" />
		<input type="hidden" id="previous_event_type_id" value="<?php echo @$_POST['event_type_id']?>" />
		<input type="hidden" id="previous_date_from" value="<?php echo @$_POST['date_from']?>" />
		<input type="hidden" id="previous_date_to" value="<?php echo @$_POST['date_to']?>" />
		<input type="hidden" id="previous_hos_num" value="<?php echo @$_POST['hos_num']?>" />
		<?php echo $this->renderPartial('_filters');?>
		<div id="searchResults"></div>
		<div id="search-loading-msg" class="large-12 column hidden">
			<div class="alert-box">
				<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" class="spinner" /> <strong>Searching, please wait...</strong>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(function() {

		var loadingMsg = $('#search-loading-msg');

		handleButton($('#auditList-filter button[type="submit"]'),function(e) {
			loadingMsg.show();
			$('#searchResults').empty();

			// $('#searchResults').html('<div id="auditList" class="grid-view"><ul id="auditList"><li class="header"><span>Searching...</span></li></ul></div>');

			$('#page').val(1);

			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('audit/search'); ?>',
				'type': 'POST',
				'data': $('#auditList-filter').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(data) {
					$('#previous_site_id').val($('#site_id').val());
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
		$('#auditList-filter button[type="submit"]').click();

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