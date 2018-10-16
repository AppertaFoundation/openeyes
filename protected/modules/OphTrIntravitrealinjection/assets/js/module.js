// listener to handle setting the injection depth for different lens status
function OphTrIntravitrealinjection_antSegListener(_drawing) {
	var self = this;

	self.drawing = _drawing;
	self._default_distance = null;

	var side = 'right';
	if (self.drawing.eye) {
		side = 'left';
	}
	self.side = side;
	self._injectionDoodles = {};
	self._unsynced = new Array();
	// state flag to track whether we are updating the doodle or not
	self._updating = false;
	self.init();
}

OphTrIntravitrealinjection_antSegListener.prototype.init = function()
{
	var self = this;

	self.setDefaultDistance();
	self.drawing.registerForNotifications(self, 'callback', ['doodleAdded', 'doodleDeleted', 'parameterChanged']);

	$('#Element_OphTrIntravitrealinjection_AnteriorSegment_' + self.side + '_lens_status_id').bind('change', function() {
		self.setDefaultDistance();
	});
}

// get the default distance from the lens status
OphTrIntravitrealinjection_antSegListener.prototype.setDefaultDistance = function() {
	var self = this;

	var selVal = $('#Element_OphTrIntravitrealinjection_AnteriorSegment_' + self.side + '_lens_status_id').val();
	if (selVal) {
		$('#Element_OphTrIntravitrealinjection_AnteriorSegment_' + self.side + '_lens_status_id').find('option').each(function() {
			if ($(this).val() == selVal) {
				self._default_distance = $(this).attr('data-default-distance');
				return false;
			}
		});
		self.updateDistances();
	}
	else {
		self._default_distance = null;
	}
}

// update individual injection distance
OphTrIntravitrealinjection_antSegListener.prototype.updateDoodleDistance = function(doodle, distance)
{
	validityArray = doodle.validateParameter('distance', distance.toString());
	if (validityArray.valid) {
		doodle.setParameterWithAnimation('distance', validityArray.value);
	}
	else { console.log('SYNC ERROR: invalid distance from lens status for doodle'); }
}

// iterate through all registered injection site doodles, and update those that have not been manually altered
OphTrIntravitrealinjection_antSegListener.prototype.updateDistances = function()
{
	var self = this;
	for (var id in self._injectionDoodles) {
		var obj = self._injectionDoodles[id];
		skip = false;
		for (var j = 0; j <= self._unsynced.length; j++) {
			if (self._unsynced[j] == id) {
				skip = true;
				break;
			}
		}
		// it's not synced
		if (skip) {
			continue;
		}
		self.updateDoodleDistance(obj, self._default_distance);
	}
}

// listener callback function for eyedraw
OphTrIntravitrealinjection_antSegListener.prototype.callback = function(_messageArray) {
	var self = this;


	if (_messageArray.eventName == "doodleAdded" && _messageArray.object.className == 'InjectionSite') {
		// set the distance to the default value from the lens status
		self._injectionDoodles[_messageArray.object.id] = _messageArray.object;
		if (self._default_distance) {
			self.updateDoodleDistance(_messageArray.object, self._default_distance);
		}
	}
	// we get parameter change noticed for changes initiated by our object, so we don't want to unsync those sites
	else if (_messageArray.eventName == "parameterChanged"
		&& _messageArray.object.doodle.className == "InjectionSite"
		&& _messageArray.object.parameter == "distance") {

		// when editing/after validation, initial doodles are not added, so need to verify the doodle is known to our listener
		var id = _messageArray.object.doodle.id;
		if (!self._injectionDoodles[id]) {
			self._injectionDoodles[id] = _messageArray.object.doodle;
		}

		if (_messageArray.object.value != _messageArray.object.oldvalue
			&& _messageArray.object.value != self._default_distance) {
			// unsync this injection from future changes to lens status
			for (var i = 0; i <= self._unsynced.length; i++) {
				if (self._unsynced[i] == _messageArray.object.doodle.id) {
					return;
				}
			}
			self._unsynced.push(_messageArray.object.doodle.id);
		}
	}

};

function OphTrIntravitrealinjection_setInjectionNumber(side) {
	var el = $('#Element_OphTrIntravitrealinjection_Treatment_' + side + '_drug_id');
	var drug_id = el.val();
	var count = 0;
	if (drug_id) {
		// work out the number of injections (previous plus one)
		count++;

		// get the previous count of this drug for this side
		el.find('option').each(function() {
			if ($(this).val() == drug_id) {
				if ($(this).data('original-count')) {
					count = $(this).data('original-count');
				}
				else {
					count += $(this).data('previous');
				}
				return false;
			}
		});
	}

	$('#Element_OphTrIntravitrealinjection_Treatment_'+side+'_number').val(count);

	// update the history tooltip when the drug is selected/changed
	$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_number').find('.number-history-item').addClass('hidden');
	if (drug_id) {
		$('#' + side + '_number_history_icon').removeClass('hidden');
		$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_history_' + drug_id).removeClass('hidden');
	}
	else {
		$('#' + side + '_number_history_icon').addClass('hidden');
	}
}

function OphTrIntravitrealinjection_hide(side, el) {
	hideSplitElementSide('Element_OphTrIntravitrealinjection_Anaesthetic', side);
	hideSplitElementSide('Element_OphTrIntravitrealinjection_AnteriorSegment', side);
	hideSplitElementSide('Element_OphTrIntravitrealinjection_PostInjectionExamination', side);
	hideSplitElementSide('Element_OphTrIntravitrealinjection_Complications', side);
	hideSplitElementSide('Element_OphTrIntravitrealinjection_Treatment', side);
}

function OphTrIntravitrealinjection_show(side) {
	showSplitElementSide('Element_OphTrIntravitrealinjection_Anaesthetic', side);
	showSplitElementSide('Element_OphTrIntravitrealinjection_AnteriorSegment', side);
	showSplitElementSide('Element_OphTrIntravitrealinjection_PostInjectionExamination', side);
	showSplitElementSide('Element_OphTrIntravitrealinjection_Complications', side);
	showSplitElementSide('Element_OphTrIntravitrealinjection_Treatment', side);
}

// check whether the description field should be shown for the complications on the given side
function OphTrIntravitrealinjection_otherComplicationsCheck(side) {
	var show = false;
	$('#Element_OphTrIntravitrealinjection_Complications_'+side+'_complications').find('input').each(function(e) {
		if ($(this).data('description_required')) {
			show = true;
			return false;
		}
	});
	var el = $('#div_Element_OphTrIntravitrealinjection_Complications_'+side+'_oth_descrip');
	var input = el.find('textarea');

	if (show) {
		el.show();
		if (el.data('store-value') && el.data('store-value').length) {
			input.val(el.data('store-value'));
		}
	}
	else {
		$('#div_Element_OphTrIntravitrealinjection_Complications_'+side+'_oth_descrip').hide();
		if (input.val()) {
			el.data('store-value', input.val());
			input.val('');
		}

	}
}

$(document).ready(function() {

  $(this).on('click','#et_print', function (e) {
    e.preventDefault();
    printEvent(null);
  });

  $(this).on('click','#et_cancel',function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
		}
		e.preventDefault();
	});

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

	$('select').bind('change').change(function() {
		var selVal = $(this).val();
		var allergic = false;
		var allergyName = '';
		if (selVal) {
			$(this).find('option').each(function() {
				if (selVal == $(this).val()) {
					if ($(this).data('allergic') == '1') {
						allergyName = $(this).data('allergy');
						allergic = true;
					}
					return false;
				}
			});
		}
		if (allergic) {
			$(this).closest('.wrapper').prepend('<i class="oe-i warning pad-right js-allergy-warning js-has-tooltip" data-tooltip-content="Allergic to ' + allergyName + '"></i>');
		}
		else {
			$(this).closest('.wrapper').find('.js-allergy-warning').remove();
		}
	})

	// live checking of the drug selection for treatment to determine if the other elements should be shown or not
	$('.Element_OphTrIntravitrealinjection_Treatment').delegate('#Element_OphTrIntravitrealinjection_Treatment_right_drug_id, ' +
			'#Element_OphTrIntravitrealinjection_Treatment_left_drug_id', 'change', function() {
		var side = getSplitElementSide($(this));

		OphTrIntravitrealinjection_setInjectionNumber(side);
	});


	// history tool tip
	$(".Element_OphTrIntravitrealinjection_Treatment").find('.number-history').each(function(){
		var quick = $(this);
		var iconHover = $(this).parent().find('.number-history-icon');

		iconHover.hover(function(e){
			var infoWrap = $('<div class="quicklook"></div>');
			infoWrap.appendTo('body');
			infoWrap.html(quick.html());

			var offsetPos = $(this).offset();
			var top = offsetPos.top;
			var left = offsetPos.left + 25;

			top = top - (infoWrap.height()/2) + 8;

			if (left + infoWrap.width() > 1150) left = left - infoWrap.width() - 40;
			infoWrap.css({'position': 'absolute', 'top': top + "px", 'left': left + "px"});
			infoWrap.fadeIn('fast');
		},function(e){
			$('body > div:last').remove();
		});
	});

	// deal with the ioplowering show/hide
	$('.Element_OphTrIntravitrealinjection_Treatment').delegate(
			'#Element_OphTrIntravitrealinjection_Treatment_right_pre_ioplowering_required, ' +
			'#Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_required', 'change', function() {
		var side = getSplitElementSide($(this));
		if ($(this).attr('checked')) {
			$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_pre_ioploweringdrugs').removeClass('hidden');
			$('#Element_OphTrIntravitrealinjection_Treatment_'+side+'_pre_ioploweringdrugs').removeAttr('disabled');
		} else {
			$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_pre_ioploweringdrugs').addClass('hidden');
			$('#Element_OphTrIntravitrealinjection_Treatment_'+side+'_pre_ioploweringdrugs').attr('disabled', 'disabled');
		}
	});

	$('.Element_OphTrIntravitrealinjection_Treatment').delegate(
			'#Element_OphTrIntravitrealinjection_Treatment_right_post_ioplowering_required, ' +
			'#Element_OphTrIntravitrealinjection_Treatment_left_post_ioplowering_required', 'change', function() {
		var side = getSplitElementSide($(this));
		if ($(this).attr('checked')) {
			$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_post_ioploweringdrugs').removeClass('hidden');
			$('#Element_OphTrIntravitrealinjection_Treatment_'+side+'_post_ioploweringdrugs').removeAttr('disabled');
		} else {
			$('#div_Element_OphTrIntravitrealinjection_Treatment_'+side+'_post_ioploweringdrugs').addClass('hidden');
			$('#Element_OphTrIntravitrealinjection_Treatment_'+side+'_post_ioploweringdrugs').attr('disabled', 'disabled');
		}
	});

	// extend the removal behaviour for treatment element to affect the dependent elements
	$(this).undelegate('#event-content .remove-side', 'click').delegate('#event-content .remove-side', 'click', function(e) {
		e.preventDefault();
		side = getSplitElementSide($(this));

		var other_side = 'left';
		if (side == 'left') {
			other_side = 'right';
		}
		OphTrIntravitrealinjection_hide(side,  this);
		OphTrIntravitrealinjection_show(other_side);
	});

	// extend the adding behaviour for treatment drug to affect dependent elements
	$(this).undelegate('#event-content .js-element-eye .inactive-form a', 'click').delegate('#event-content .js-element-eye .inactive-form a', 'click', function(e) {
		e.preventDefault();
		side = getSplitElementSide($(this));
		OphTrIntravitrealinjection_show(side);
	});

	$('.Element_OphTrIntravitrealinjection_Complications').delegate('select.MultiSelectList', 'MultiSelectChanged', function(e) {
		var side = getSplitElementSide($(this));
		OphTrIntravitrealinjection_otherComplicationsCheck(side);
	});

});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		// handle event
	}
}
