
$(document).ready(function() {
	var show_disable_manual_warning = false;
	var sdmw = $('#show_disable_manual_warning').val();

	autosize($('.autosize'));

	$("#Element_OphInBiometry_Measurement_k1_right, #Element_OphInBiometry_Measurement_k2_right, #Element_OphInBiometry_Measurement_k2_axis_right").on("keyup", function() {
		calculateDeltaValues('right');
	});

	$("#Element_OphInBiometry_Measurement_k1_left, #Element_OphInBiometry_Measurement_k2_left, #Element_OphInBiometry_Measurement_k2_axis_left").on("keyup", function() {
		calculateDeltaValues('left');
	});

	$("#Element_OphInBiometry_Calculation_target_refraction_right").on("keyup", function() {
		var tarref = $("#Element_OphInBiometry_Calculation_target_refraction_right" ).val();
		if(tarref < 0){
			if (tarref.length > 2) {
				updateClosest ('right');
			}
		}else {
			if (tarref.length > 1) {
				updateClosest ('right');
			}
		}
	});

	$("#Element_OphInBiometry_Calculation_target_refraction_left").on("keyup", function() {
		var tarref = $("#Element_OphInBiometry_Calculation_target_refraction_left" ).val();
		if(tarref < 0){
			if (tarref.length > 2) {
				updateClosest ('left');
			}
		}else {
			if (tarref.length > 1) {
				updateClosest ('left');
			}
		}
	});

	if(sdmw ==1 ){
		$("#event-content").hide();
		$("#et_save").hide();
		show_disable_manual_warning = true;
	}

	if(show_disable_manual_warning) {
		var warning_message = 'No new biometry reports are available for this patient. Please generate a new event on your linked device first (e.g., IOL Master).';
		var dialog_msg = '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all dialog" id="dialog-msg" tabindex="-1" role="dialog" aria-labelledby="ui-id-1" style="outline: 0px; height: auto; width: 600px; position: fixed; top: 50%; left: 50%; margin-top: -110px; margin-left: -200px;">' +
			'<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">' +
			'<span id="ui-id-1" class="ui-dialog-title">No New Biometry Reports</span>' +
			'</div><div id="site-and-firm-dialog" class="ui-dialog-content ui-widget-content" scrolltop="0" scrollleft="0" style="display: block; width: auto; min-height: 0px; height: auto;">' +
			'<div class="alert-box alert with-icon" id="disabled-manual-dialog-message"> <strong>WARNING: ' + warning_message + ' </strong></div>' +
			'<div style = "margin-top:20px; float:right">' +
			'<input class="secondary small" id="prescription-yes" type="submit" name="yt0" style = "margin-right:10px" value="OK" onclick="goBack()">' +
			'</div>';

		var blackout_box = '<div id="blackout-box" style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:black;opacity:0.6;">';

		$(dialog_msg).prependTo("body");
		$(blackout_box).prependTo("body");
		$('div#blackout_box').css.opacity = 0.6;
		$("input#prescription-no").focus();
		$("input#prescription-yes").keyup(function (e) {
			hide_dialog();
		});
	}


	$('input[id^="iolrefrad-"], tr[id^="iolreftr-"]').click(function(event) {
		var id = event.target.id;
	//	alert ('radio  -  ' + id);
		id = id.split("-").pop();
		var d = id.split('__');
		var s = d[0].split('_');
		$("#Element_OphInBiometry_Selection_iol_power_"+s[0]).val($("#iolval-"+d[0]+"__"+d[1]).val());
		$("#Element_OphInBiometry_Selection_predicted_refraction_"+s[0]).val($("#refval-"+d[0]+"__"+d[1]).val());
		updateIolRefRow('left');
		updateIolRefRow('right');
	});

	$(this).on('click','#et_cancel',function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
		}
		e.preventDefault();
	});

	$(this).on('click','#et_canceldelete',function(e) {
		if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/delete/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
		}
		e.preventDefault();
	});

	$(this).on('click','#et_print',function(e) {
		printIFrameUrl(OE_print_url, null);
		enableButtons();
		e.preventDefault();
	});

	// extend the removal behaviour to affect the dependent elements
	$(this).delegate('#event-content .js-element-eye .active-form .remove-side', 'click', function(e) {
		side = getSplitElementSide($(this));
		var other_side = 'left';
		if (side === 'left') {
			other_side = 'right';
		}
		OphInBiometry_hide(side,	this);
		OphInBiometry_show(other_side);
	});

	// extend the adding behaviour to affect dependent elements
	$(this).delegate('#event-content .inactive-form a', 'click', function(e) {
		side = getSplitElementSide($(this));
		OphInBiometry_show(side);
	});

	function OphInBiometry_hide(side, el) {
		hideSplitElementSide('Element_OphInBiometry_BiometryData', side);
		hideSplitElementSide('Element_OphInBiometry_Calculation', side);
		hideSplitElementSide('Element_OphInBiometry_Selection', side);
	}

	function OphInBiometry_show(side) {
		showSplitElementSide('Element_OphInBiometry_BiometryData', side);
		showSplitElementSide('Element_OphInBiometry_Calculation', side);
		showSplitElementSide('Element_OphInBiometry_Selection', side);

		$('section.Element_OphInBiometry_BiometryData').find('div[data-side="' + side + '"]').find('.active-form').show();
		$('section.Element_OphInBiometry_BiometryData').find('div[data-side="' + side + '"]').find('.inactive-form').hide();

		$('section.Element_OphInBiometry_Calculation').find('div[data-side="' + side + '"]').find('.active-form').show();
		$('section.Element_OphInBiometry_Calculation').find('div[data-side="' + side + '"]').find('.inactive-form').hide();
	}

	$('select.populate_textarea').unbind('change').change(function() {
		if ($(this).val() != '') {
			var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
			var el = $('#'+cLass+'_'+$(this).attr('id'));
			var currentText = el.text();
			var newText = $(this).children('option:selected').text();

			if (currentText.length == 0) {
				el.text(ucfirst(newText));
			} else {
				el.text(currentText+', '+newText);
			}
		}
	});

	function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

	$('#Element_OphInBiometry_Selection_lens_id_left').on('change', () => onChangeLensType('left'));
	$('#Element_OphInBiometry_Selection_lens_id_right').on('change', () => onChangeLensType('right'));

	$('.js-lens-manual-override-dropdown').on('change', function() {
		let option_selected = $(this).find("option:selected").data('constant');
		$(this).closest('tbody').find('.js-lens-constant').text(option_selected);
	});

	function onChangeLensType(side){
		clearIolSelection(side);
		updateClosest (side);
	}

	function clearIolSelection(side){
		$('input[id^=iolrefrad-' + side + ']:checked').prop('checked', false);
		$('#Element_OphInBiometry_Selection_iol_power_' + side).val('');
		$('#Element_OphInBiometry_Selection_predicted_refraction_' + side).val('0');
		$('tr[id*=iolreftr-' + side + ']').removeClass('selected-row');
	}

	$('#Element_OphInBiometry_BiometryData_axial_length_left').die('change').live('change',function() {
		update('left');
	});

	$('#Element_OphInBiometry_BiometryData_axial_length_right').die('change').live('change',function() {
		update('right');
	});

	$('#Element_OphInBiometry_BiometryData_r1_left').die('change').live('change',function() {
		update('left');
	});

	$('#Element_OphInBiometry_BiometryData_r1_right').die('change').live('change',function() {
		update('right');
	});


	$('#Element_OphInBiometry_BiometryData_r2_left').die('change').live('change',function() {
		update('left');
	});

	$('#Element_OphInBiometry_BiometryData_r2_right').die('change').live('change',function() {
		update('right');
	});

	$('#Element_OphInBiometry_Calculation_target_refraction_left').die('change').live('change',function() {
		update('left');
	});

	$('#Element_OphInBiometry_Calculation_target_refraction_right').die('change').live('change',function() {
		update('right');
	});

	$('#Element_OphInBiometry_Calculation_formula_id_left').die('change').live('change',function() {
		update('left');
	});

	$('#Element_OphInBiometry_Calculation_formula_id_right').die('change').live('change',function() {
		update('right');
	});

	$('#Element_OphInBiometry_Selection_lens_id_left').die('change').live('change',function() {
		update('left');
		updateIolRefTable('left');
	});

	$('#Element_OphInBiometry_Selection_lens_id_right').die('change').live('change',function() {
		update('right');
		updateIolRefTable('right');
	});

	$('#Element_OphInBiometry_Selection_formula_id_left').die('change').live('change',function() {
		updateIolRefTable('left');
	});
	$('#Element_OphInBiometry_Selection_formula_id_right').die('change').live('change',function() {
		updateIolRefTable('right');
	});


	renderCalculatedValues('left');
	renderCalculatedValues('right');

	updateIolRefRow('left');
	updateIolRefRow('right');
});

function calculateDeltaValues(side){
	$('#Element_OphInBiometry_Measurement_delta_k_'+side+', #input_Element_OphInBiometry_Measurement_delta_k_'+side).val($('#Element_OphInBiometry_Measurement_k2_'+side).val() - $('#Element_OphInBiometry_Measurement_k1_'+side).val());
	$('#Element_OphInBiometry_Measurement_delta_k_axis_'+side+', #input_Element_OphInBiometry_Measurement_delta_k_axis_'+side).val($('#Element_OphInBiometry_Measurement_k2_axis_'+side).val());
}

function update(side)
{
	clearChoice(side);
	renderCalculatedValues(side);
}

function clearChoice(side) {
	$('span.iol_power_'+side).text('');
	$('Element_OphInBiometry_Selection[iol_power_'+side+']').val('');
	$('span.predicted_refraction_'+side).text('');
	$('Element_OphInBiometry_Selection[predicted_refraction_'+side+']').val('');
}

function renderCalculatedValues(side)
{
	updateBiometryData(side);

	if(isView()){
		updateIolData($('#lens_'+side).html(),side);
	}

	if(isCreate()) {
		updateIolData($('#Element_OphInBiometry_Selection_lens_id_' + side + ' option:selected').text(),side);
		updateSuggestedPowerTable(side);
	}
}

function updateBiometryData(side)
{
	var n = 1.3375;

	var em = new EyeMeasurements(side);

	var k1Value = (n-1) * 1000 / em.r1;
	var k2Value = (n-1) * 1000 / em.r2;

	if (k1Value) {
		$('#Element_OphInBiometry_BiometryData_r1_axis_' + side).prev('span').text(k1Value.toFixed(2) + " D @");
	}

	if (k2Value) {
		$('#Element_OphInBiometry_BiometryData_r2_axis_' + side).prev('span').text(k2Value.toFixed(2) + " D @");
	}

	if (em.r1 && em.r2) {
		var rse_mm = (em.r1 + em.r2) / 2;
		$('.rse_mm_' + side).text(rse_mm.toFixed(2) + ' mm');
		$('.rse_d_' + side).text(((n-1) * 1000/rse_mm).toFixed(2) + ' D');
	} else {
		$('.rse_mm_' + side).text('');
		$('.rse_d_' + side).text('');
	}

	if (k1Value && k2Value) {
		var cyl = k1Value - k2Value;
		var axis = 0;

		if (cyl > 0) {
			axis = em.ra2;
		} else if (cyl <0) {
			axis = em.ra1;
		}

		$('.cyl_' + side).text(cyl.toFixed(2) + ' mm @' + axis + '°');
	} else {
		$('.cyl_' + side).text('');
	}
}

function updateIolRefRow(side) {

	$('#Element_OphInBiometry_Selection_lens_id_' + side + ' option').each(function () {
		var lensid = $(this).attr('value');
		$('#Element_OphInBiometry_Selection_formula_id_' + side + ' option').each(function () {
			var formulaid = $(this).attr('value');

			if (!isNaN(parseInt(lensid)) && !isNaN(parseInt(formulaid))) {

				var trstr = '#' + side + '_' + lensid + '_' + formulaid + ' tr';
				$(trstr).each(function (i, el) {

					var elem = $(el);
					var rowstr = '#iolreftr-' + side + '_' + lensid + '_' + formulaid + '__';
					var rowsrad = '#iolrefrad-' + side + '_' + lensid + '_' + formulaid + '__';
					var iolvalstr = '#iolval-' + side + '_' + lensid + '_' + formulaid + '__';
					var refvalstr = '#refval-' + side + '_' + lensid + '_' + formulaid + '__';

					$(rowstr + i).click(function () {

						$("#Element_OphInBiometry_Selection_iol_power_"+side).val($(iolvalstr+i).val());
						$("#Element_OphInBiometry_Selection_predicted_refraction_"+side).val($(refvalstr+i).val());
						$("#iol_power_"+side).text($(iolvalstr+i).val());
						$("#predicted_refraction_"+side).text($(refvalstr+i).val());

						for (j = 0; j < $(trstr).length; j++) {
							if (i == j) {
								//alert('clicked'+ rowstr + j);
								$(rowstr + j).addClass("selected-row");
								//$('#iolreftr-left_6_1__' + j).css("background-color", "#FFFFE0");
								$(rowsrad + j).attr('checked', true);
							}
							else
							{
								$(rowstr + j).removeClass("selected-row");
								$(rowsrad + j).attr('checked', false);
							}
						}
					});
				});
			}

		});
	});
}

function updateIolRefTable(side) {
	var l_id = ($('#Element_OphInBiometry_Selection_lens_id_' + side + ' option:selected').val());
	var f_id = ($('#Element_OphInBiometry_Selection_formula_id_' + side + ' option:selected').val());
	$('table[id^="'+side+'_"]').hide();
	$('span[id^="emmetropia_'+side+'_"]').hide();
	$('span[id^="aconstant_'+side+'_"]').hide();

	if (!isNaN(parseInt(l_id)) && !isNaN(parseInt(f_id))) {
		var swtb = side + '_' + l_id + '_' + f_id;
		var swsn = 'emmetropia_'+side + '_' + l_id + '_' + f_id;
		var asn = 'aconstant_'+side + '_' + l_id + '_' + f_id;
		$('#' + swtb ).show();
		$('#' + swsn).show();
		$('#' + asn).show();
	}
}

function updateClosest (side) {
		var tarref = $("#Element_OphInBiometry_Calculation_target_refraction_"+side ).val();
		var l_id = ($('#Element_OphInBiometry_Selection_lens_id_' + side + ' option:selected').val());
		var f_id = ($('#Element_OphInBiometry_Selection_formula_id_' + side + ' option:selected').val());

		if (!isNaN(parseInt(l_id)) && !isNaN(parseInt(f_id))) {
			var swtb = side + '_' + l_id + '_' + f_id;
			var trstr = '#' + side + '_' + l_id + '_' + f_id + ' tr';
			var ref = new Array();
			$(trstr).each(function (i, el) {
				var swref = '#refval-' + side + '_' + l_id + '_' + f_id+'__'+i;
				var refval = $(swref).val();
				if(!isNaN(refval))
				{
					ref.push(refval);
				}
				var oldclosest = '#iolreftr-'+side+'_'+l_id + '_' + f_id+'__'+i;
				$(oldclosest).find('span').removeClass("highlighter");
			});
			var closest = closestNew(ref,tarref );
			var trstr1 = '#' + side + '_' + l_id + '_' + f_id+ ' tr';
			$(trstr1).each(function(i,el) {
				var swref1 = '#refval-' + side + '_' + l_id + '_' + f_id+'__'+i;
				var refval1 = $(swref1).val();
				if(parseFloat(closest) == parseFloat(refval1) )
				{
					var newclosest = '#iolreftr-'+side+'_'+l_id + '_' + f_id+'__'+i;
					$(newclosest).find('span').addClass("highlighter");
				}
			});
		}
}

function closestNew(theArray, closestTo) {
	var closest = null;
	$.each(theArray, function () {
		if (closest == null || Math.abs(this - closestTo) < Math.abs(closest - closestTo)) {
			closest = this;
		}
	});
	return closest;
}

function updateIolData(index,side) {
	var acon = document.getElementById('acon_'+side);
	var sf = document.getElementById('sf_'+side);
	var type = document.getElementById('type_'+side);
	var position = document.getElementById('position_'+side);
	var comments = document.getElementById('comments_'+side);
	if (typeof(OphInBioemtry_lens_types[index]) != 'undefined') {
		if (acon) {
			acon.innerHTML = OphInBioemtry_lens_types[index].acon.toFixed(2);
		}

		if(sf) {
			if(OphInBioemtry_lens_types[index].sf) {
				sf.innerHTML = OphInBioemtry_lens_types[index].sf.toFixed(2);
			} else {
				sf.innerHTML = 'Unknown';
			}
		}
		if(type) type.innerHTML = OphInBioemtry_lens_types[index].model + " " + OphInBioemtry_lens_types[index].description;
		if(position) position.innerHTML = OphInBioemtry_lens_types[index].position;
		if(comments) comments.innerHTML = OphInBioemtry_lens_types[index].comments;
	}
    /*
    else {
		acon.innerHTML = '';
		sf.innerHTML = '';
		type.innerHTML = '';
		position.innerHTML = '';
		comments.innerHTML = '';
	}
     */

	updateIolRefTable('left');
	updateIolRefTable('right');
}

function updateSuggestedPowerTable(side)
{
	executeFormula($('#Element_OphInBiometry_Calculation_formula_id_'+side+' option:selected').text(),side);
}

function executeFormula(formula,side)
{
	var formulae = [];
	formulae['SRK/T'] = 'SRKT';
	formulae['Holladay 1'] = 'Holladay1';
	formulae['T2'] = 'T2';

	fillTableUsingFormula(formulae[formula],side);
}

function fillTableUsingFormula(formulaName, side)
{
	clearTable(side);
	// Get values
	var e = new EyeMeasurements(side);
	var iol = new IolConstants(side);
	var formulaClass = this[formulaName];
	if (formulaClass) {
		var formula = new formulaClass(e,iol);

		// Calculate lens power for target refraction
		var powerIOL = formula.suggestedPower();
		if (powerIOL) {

			// Round to nearest 0.5
			var roundIOL = Math.round(powerIOL * 2) / 2;

			// Produce results for range of refraction around this one
			var startPower = roundIOL + 1;
			for (var i = 0; i < 5; i++) {
				var power = startPower - (0.5 * i);
				var refraction = formula.powerFor(power);
				addRow(power.toFixed(1),enforceSign(refraction.toFixed(2)), i == 2,side);
			}
		}
		else {
			//console.log('Unable to calculate power');
		}
	}
}

function enforceSign(value)
{
	return value > 0 ? "+" + value : value;
}

// Delete all rows
function clearTable(side) {
	// Get reference to table
	var table = document.getElementById('iol-table_'+side);

	if (table) {
		// Get number of rows
		var numberOfRows = table.tBodies[0].rows.length;

		// Delete them
		for (var i = 0; i < numberOfRows; i++) {
			table.deleteRow(1);
		}
	}
}

function addRow(power, refraction, _bold, side) {

	// Get reference to table
	var table = document.getElementById('iol-table_'+side);

	// Index of next row is equal to number of rows
	var nextRowIndex = table.tBodies[0].rows.length;

	// Add new row
	var newRow = table.tBodies[0].insertRow(nextRowIndex);

	// IOL
	var cell0 = newRow.insertCell(0);
	var node = document.createElement('button');
	node.setAttribute('onclick', 'iolSelected(' + power + ',' + refraction + ',"' + side +'")');
	node.innerHTML = power;
	cell0.appendChild(node);

	// Refraction
	var cell1 = newRow.insertCell(1);
	node = document.createElement('p');
	node.innerHTML = refraction;
	cell1.appendChild(node);
}

function iolSelected(power, refraction, side) {
	event.preventDefault();
	clearChoice(side);

	$('span.iol_power_'+side).text(power);
	$('input[name="Element_OphInBiometry_Selection[iol_power_'+side+']"]').val(power);
	$('span.predicted_refraction_'+side).text(refraction);
	$('input[name="Element_OphInBiometry_Selection[predicted_refraction_'+side+']"]').val(refraction);

	setTimeout(function() {
		var inserted = $('section.Element_OphInBiometry_Selection');
		var offTop = inserted.offset().top - 90;
		var speed = (Math.abs($(window).scrollTop() - offTop)) * 1.5;
		$('body').animate({
			scrollTop : offTop
		}, speed, null, function() {
			$('.element-title', inserted).effect('pulsate', {
				times : 2
			}, 600);
		});
	}, 100);
}

function EyeMeasurements(side)
{
	if(isView()) {
		this.al=parseFloat($('#al_'+side).html());
		this.r1=parseFloat($('#r1_'+side).html());
		this.r2=parseFloat($('#r2_'+side).html());
		this.tr=parseFloat($('#tr_'+side).html());
	}

	if(isCreate()){
		this.al=parseFloat($('#Element_OphInBiometry_BiometryData_axial_length_'+side).val());
		this.r1=parseFloat($('#Element_OphInBiometry_BiometryData_r1_'+side).val());
		this.ra1=parseFloat($('#Element_OphInBiometry_BiometryData_r1_axis_'+side).val());
		this.r2=parseFloat($('#Element_OphInBiometry_BiometryData_r2_'+side).val());
		this.ra2=parseFloat($('#Element_OphInBiometry_BiometryData_r2_axis_'+side).val());
		this.tr=parseFloat($('#Element_OphInBiometry_Calculation_target_refraction_'+side).val());
	}
}

function isCreate()
{

	return( $('#al_left').length==0 && $('#al_right').length==0);
}

function isView()
{
	return ($('#al_left').length!=0 || $('#al_right').length!=0);
}

function IolConstants(side)
{
	if(isCreate()) {
	//this.acon=parseFloat(document.getElementById('acon_'+side).innerHTML);
	//this.sf=parseFloat(document.getElementById('sf_'+side).innerHTML);
	}
}

function Holladay1 (eyeMeasurements, iolConstants) {

	var r = (eyeMeasurements.r1 + eyeMeasurements.r2) / 2;
	var AL = eyeMeasurements.al;
	var RefTgt = eyeMeasurements.tr;

	var SF = iolConstants.sf;

	var Alm = AL + 0.2;
	var Rag = r < 7.0 ? 7.0 : r;
	var AG = (12.5 * AL / 23.45 > 13.5) ? 13.5 : 12.5 * AL / 23.45;
	var BF7 = (Rag * Rag - (AG * AG / 4.0));
	var BF8 = Math.sqrt(BF7);
	var ACD = 0.56 + Rag - BF8;
	const na = 1.336;
	const nc_1 = 1.0 / 3.0;

	this.suggestedPower = function() {
		var numerator = (1000.0 * na * (na * r - nc_1 * Alm - 0.001 * RefTgt * (12.0 * (na * r - nc_1 * Alm) + Alm * r)));
		var denominator = ((Alm - ACD - SF) * (na * r - nc_1 * (ACD + SF) - 0.001 * RefTgt * (12.0 * (na * r - nc_1 * (ACD + SF)) + (ACD + SF) * r)));
		return numerator / denominator;	};

	this.powerFor = function(lensPower) {
		var Numerator = (1000.0 * na * (na * r - (nc_1) * Alm) - lensPower * (Alm - ACD - SF) * (na * r - (nc_1) * (ACD + SF)));
		var Denominator = (na * (12.0 * (na * r - (nc_1) * Alm) + Alm * r) - 0.001 * lensPower * (Alm - ACD - SF) * (12.0 * (na * r - (nc_1) * (ACD + SF)) + (ACD + SF) * r));
		return Numerator / Denominator;
	};
}

function hide_dialog()
{
	$('#blackout-box').hide();
	$('#dialog-msg').hide();

}

function goBack()
{
	window.history.back();
}


function SRKT (eyeMeasurements, iolConstants)
{
	// Refractive index of cornea with fudge factor for converting radius of curvature to dioptric power
	var n = 1.3375;
	// Refractive index of the cornea
	var nc = 1.333;
	// Refractive index of aqueous and vitreous
	var na = 1.336;
	// Vertex distance
	var vd = 12.0;

	var averageRadius = (eyeMeasurements.r1 + eyeMeasurements.r2) / 2;
	var dioptresCornea = (n - 1) * 1000 / averageRadius;

	var diffRI = nc - 1;

	var retinalThickness = 0.65696 - 0.02029 * eyeMeasurements.al;
	var opticalAxialLength = eyeMeasurements.al + retinalThickness;

	var aconstant;
	if (iolConstants.acon > 100) {
		aconstant = iolConstants.acon * 0.62467 - 68 - 0.74709;
		//calculationComments += "A-constant correction applied</br>";
	} else {
		aconstant = iolConstants.acon;
	}

	// Difference between natural lens and IOL to cornea
	var diff = aconstant - 3.3357;

	// Axial length correction for high myopes
	var axialLength;
	if (eyeMeasurements.al > 24.2) {
		// Value of 1.716 (as in original SRK/T paper) gives identical results to IOLMaster. Using 1.715 as in erratum gives slightly different results
		axialLength = -3.446 + 1.716 * eyeMeasurements.al - 0.0237 * eyeMeasurements.al * eyeMeasurements.al;
		//axialLength = -3.446 + 1.715 * eyeMeasurements.al - 0.0237 * eyeMeasurements.al * eyeMeasurements.al;
		//calculationComments += "Axial length correction applied</br>";
	} else {
		axialLength = eyeMeasurements.al;
	}

	// Corneal width
	var cornealWidth = -5.40948 + 0.58412 * axialLength + 0.098 * dioptresCornea;

	// Corneal dome height (check for negative result here before taking square root)
	if (averageRadius * averageRadius - cornealWidth * cornealWidth / 4 > 0) {
		var cornealDomeHeight = averageRadius - Math.sqrt(averageRadius * averageRadius - cornealWidth * cornealWidth / 4);
	} else {
		//calculationComments += "Negative square root for corneal dome height</br>";
		var cornealDomeHeight = averageRadius;
	}
	if (cornealDomeHeight > 5.5) {
		cornealDomeHeight = 5.5;
		//calculationComments += "Corneal dome height capped at 5.5</br>";
	}

	// Post-op anterior chamber depth
	var postopACDepth = cornealDomeHeight + diff;

	this.suggestedPower = function() {
		var numerator = 1000 * na * (na * averageRadius - diffRI * opticalAxialLength - 0.001 * eyeMeasurements.tr * (vd * (na * averageRadius - diffRI * opticalAxialLength) + opticalAxialLength * averageRadius));
		var denominator = (opticalAxialLength - postopACDepth) * (na * averageRadius - diffRI * postopACDepth - 0.001 * eyeMeasurements.tr * (vd * (na * averageRadius - diffRI * postopACDepth) + postopACDepth * averageRadius));

		return numerator/denominator;
	}

	this.powerFor = function(lensPower) {
		var numerator = 1000 * na * (na * averageRadius - diffRI * opticalAxialLength) - lensPower * (opticalAxialLength - postopACDepth) * (na * averageRadius - diffRI * postopACDepth);
		var denominator = (na * (vd * (na * averageRadius - diffRI * opticalAxialLength) + opticalAxialLength * averageRadius) - 0.001 * lensPower * (opticalAxialLength - postopACDepth) * (vd * (na * averageRadius - diffRI * postopACDepth) + postopACDepth * averageRadius));

		return numerator/denominator;
	}
}

function SRKT2 (eyeMeasurements, iolConstants)
{
	var e = eyeMeasurements;
	var _axialLength = eyeMeasurements.al;
	var _radius1 = eyeMeasurements.r1;
	var _radius2 = eyeMeasurements.r2;
	var _aConstant = iolConstants.acon;

	// Fixed parameters here (could come from parameter file)
	var cornealRI = 1.333;	//Refractive index of the cornea as set in IOL Master

	// Calculate average radius of curvature
	var averageRadius = (_radius1 + _radius2) / 2;

	var dioptresCornea = 337.5 / averageRadius;

	// Difference in refrative indices
	var diffRI = cornealRI - 1;

	// Define result
	var returnPower = false;

	// Calculate IOL power for a given refraction
	var na = 1.336; // ***TODO***  What is this?
	var vertexDistance = 12;
	var retinalThickness = 0.65696 - 0.02029 * _axialLength;
	var opticalAxialLength = _axialLength + retinalThickness;

	// 'A' constant correction
	if (_aConstant > 100) {
		var aConstantSRK = _aConstant * 0.62467 - 68 - 0.74709;
	}
	else {
		var aConstantSRK = _aConstant
	}

	// Difference between natural lens and IOL to cornea
	var diff = aConstantSRK - 3.3357;

	// Axial length correction for high myopes
	var axialLength;
	if (_axialLength > 24.2) {
		axialLength = -3.446 + 1.716 * _axialLength - 0.0237 * _axialLength * _axialLength;
	}
	else {
		axialLength = _axialLength;
	}

	// Corneal width
	var cornealWidth = -5.40948 + 0.58412 * axialLength + 0.098 * dioptresCornea;

	// Corneal dome height
	var cornealDomeHeight = averageRadius - Math.sqrt(averageRadius * averageRadius - cornealWidth * cornealWidth / 4);
	if (cornealDomeHeight > 5.5) cornealDomeHeight = 5.5;

	// Post-op anterior chamber depth
	var postopACDepth = cornealDomeHeight + diff;

	this.suggestedPower = function() {
		var top = 1000 * na * (na * averageRadius - diffRI * opticalAxialLength - 0.001 * e.tr * (vertexDistance * (na * averageRadius - diffRI * opticalAxialLength) + opticalAxialLength * averageRadius));
		var bottom = (opticalAxialLength - postopACDepth) * (na * averageRadius - diffRI * postopACDepth - 0.001 * e.tr * (vertexDistance * (na * averageRadius - diffRI * postopACDepth) + postopACDepth * averageRadius));
		returnPower = top / bottom;
		return returnPower;
	}

	this.powerFor = function(lensPower) {
		var top = 1000 * na * (na * averageRadius - diffRI * opticalAxialLength) - lensPower * (opticalAxialLength - postopACDepth) * (na * averageRadius - diffRI * postopACDepth);
		var bottom = (na * (vertexDistance * (na * averageRadius - diffRI * opticalAxialLength) + opticalAxialLength * averageRadius) - 0.001 * lensPower * (opticalAxialLength - postopACDepth) * (vertexDistance * (na * averageRadius - diffRI * postopACDepth) + postopACDepth * averageRadius));
		returnPower = top / bottom;
		return returnPower;
	}
}
