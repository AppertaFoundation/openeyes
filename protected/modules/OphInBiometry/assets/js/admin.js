/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(function() {
	/*
	* 2017-03-17: Maybe unused code part, because this file was not included to admin view
	*
	$('.lensAdmin #et_cancel').die('click').live('click',function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphInBiometry/admin/lensTypes';
	});

	$('#et_add_lens_type').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphInBiometry/admin/addLensType';
	});

	$('#et_delete_lens_type').unbind('click').click(function(e) {
		e.preventDefault();

		if ($('input[name="lens_types[]"]:checked').length == 0) {
			alert("Please select one or more lens types to delete.");
			return;
		}

		var lens_types = {'lens_type_id':[]};

		$('input[name="lens_types[]"]:checked').map(function() {
			lens_types['lens_type_id'].push($(this).val());
		});

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphInBiometry/admin/deleteLensTypes',
			'data': $.param(lens_types) + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
			'success': function(html) {
				window.location.reload();
			}
		});
	});
	*/
	$('#OphInBiometry_LensType_Lens_name').bind('focusout blur',function(){
		$('#OphInBiometry_LensType_Lens_display_name').val( $(this).val());
        
		if($('#OphInBiometry_LensType_Lens_description').val() == ""){
			$('#OphInBiometry_LensType_Lens_description').val('(Created by IOL Master input)');
		}
    });
});
