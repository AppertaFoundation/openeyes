function updateCviAlertState($section) {
    // ensure jquery wrapped for backwards compatibility
    $section = $($section);
    if ($section.find('.cvi_alert_dismissed').val() !== "1") {
        let $cviAlert = $('.cvi-alert');
        if (parseInt($cviAlert.data('hascvi')) === 1) {
            return;
        }

        const threshold = parseInt($cviAlert.data('threshold'));
        const showAlert = ['.right-eye', '.left-eye'].every(function(eyeClass) {
            const values = $section.find(eyeClass + ' .va-selector')
                .map(function() { return parseInt($(this).val()); })
                .toArray();

            if (!values.length) {
                return false;
            }
            return values.every(function(val) { return val < threshold; });
        });

        if (showAlert) {
            $cviAlert.slideDown(500);
        } else {
            $cviAlert.slideUp(500);
        }
    }
}

$(document).ready(function () {
  function visualAcuityChange(target, near) {
    var suffix = 'VisualAcuity';
    if(near === 'near'){
      suffix = 'NearVisualAcuity';
    }
    var target_element = $(target).closest('.element[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]');
    var el = $('.event-content').find('ul.sub-elements-list li[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]');
    if (el.length) {
      el.addClass('clicked');
      addElement(el, true, true, false, {unit_id: $(target).val()});
    } else {
      swapElement(target_element, OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix, {
          'record_mode': $(target).data('recordMode'),
          unit_id: $(target).val()
      });
    }

  }

  function changeVisualAcuityMode(target) {
    let elementTypeClass = $(target).data('elementTypeClass');
    let targetElement = $(target).closest('.element[data-element-type-class="'+ elementTypeClass + '"]');
    swapElement(targetElement, elementTypeClass, {
        'record_mode': $(target).data('recordMode'),
        'eye_id': $(target).data('eyeId'),
        'unit_id': $(targetElement).find('.visualacuity_unit_selector').val()
    });
  }

  ($('.va_readings,.near-va-readings').each(function(){
    if($(this).find('tbody tr').length){
        $(this).siblings('.noReadings').hide();
    }
  }));

  $(this).delegate(".va-change-complexity", 'click', function() {
    changeVisualAcuityMode(this);
  });

  $(this).delegate('#nearvisualacuity_unit_change', 'change', function() {
    visualAcuityChange(this, 'near');
  });

  $(this).delegate('#visualacuity_unit_change', 'change', function() {
    visualAcuityChange(this, '');
  });

	$('.va-info-icon').closest('tr').each(function () {
		OphCiExamination_VisualAcuity_ReadingTooltip($(this));
	});

  $(this).delegate(
    '.visualAcuityReading .removeReading',
    'click', function(e) {
      var activeForm = $(this).closest('.active-form');

      var $section =  $(this).parents('section');

      $(this).closest('tr').remove();

      updateCviAlertState($section);

      if ($('.va_readings tbody', activeForm).children('tr').length === 0) {
        $('.noReadings', activeForm).show();
      }
      e.preventDefault();
    });

  $(this).delegate(
    '.nearvisualAcuityReading .removeReading',
    'click', function(e) {
      var activeForm = $(this).closest('.active-form');

      $(this).closest('tr').remove();
      if ($('.near-va-readings tbody', activeForm).children('tr').length === 0) {
        $('.noReadings', activeForm).show();
      }
      e.preventDefault();
    });

  $(this).delegate('.addNearReading', 'click', function(e) {
    var side = $(this).closest('.side').attr('data-side');
    if($(this).hasClass('addNearReading')){
      OphCiExamination_NearVisualAcuity_addReading(side);
    } else {
      OphCiExamination_VisualAcuity_addReading(side);
    }
    e.preventDefault();
  });

  /**
   * If one of the noReading checkboxes is checked, the add button will be hidden.
   */
  for (let element of ['NearVisualAcuity', 'VisualAcuity']){
    for (let side of ['right', 'left']){
      $(this).delegate('#OEModule_OphCiExamination_models_Element_OphCiExamination_'+element+'_'+side+'_unable_to_assess,' +
        '#OEModule_OphCiExamination_models_Element_OphCiExamination_'+element+'_'+side+'_eye_missing', 'click', function () {

        if ($('#OEModule_OphCiExamination_models_Element_OphCiExamination_'+element+'_'+side+'_unable_to_assess')[0].checked ||
          $('#OEModule_OphCiExamination_models_Element_OphCiExamination_'+element+'_'+side+'_eye_missing')[0].checked){
          $('#'+ 'add-' + element + '-reading-btn-'+side).hide();
        } else {
            $('#'+ 'add-' + element + '-reading-btn-'+side).show();
        }
      });
    }
  }

  /* Visual Acuity readings event binding */

  $('#event-content').on('click', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .js-remove-element', function() {
    $('.cvi-alert').slideUp(500);
  });

  $('#event-content').on('change', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .va-selector', function() {
    updateCviAlertState($(this).closest('section'));
  });


  // Dismiss alert box
  $('#event-content').on('click', '.dismiss_cva_alert', function(){
    var $section = $('section.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity');

    if( $('.ophciexamination.column.event.view').length ) {
      // viewing
      $.get( baseUrl + '/OphCiExamination/default/dismissCVIalert', { "element_id": $section.find('.element_id').val() }, function( result ) {
        var response = $.parseJSON(result);
        if(response.success === 'true'){
          $('.cvi-alert').slideUp(500).remove();
        }
      });
    } else {
      // editing
      $section.find('.cvi_alert_dismissed').val("1");
      $('.cvi-alert').slideUp(500);
    }
  });

});

/**
 * Visual Acuity
 */

function OphCiExamination_VisualAcuity_ReadingTooltip(row) {
  var iconHover = row.find('.va-info-icon:last');

  iconHover.hover(function() {
    var sel = $(this).closest('tr').find('input.va-selector');
    var val = sel.val();
    var tooltip_text = '';
    if (val) {
      var conversions = $(this).parents('.js-reading-record').find('.js-has-tooltip').data('tooltip');
      var approx = false;
      for (var i = 0; i < conversions.length; i++) {
        tooltip_text += conversions[i].name + ": " + conversions[i].value;
        if (conversions[i].approx) {
          approx = true;
          tooltip_text += '*';
        }
        tooltip_text += "<br />";
      }
      if (approx) {
        tooltip_text += "<i>* Approximate</i>";
      }
    }
    else {
      tooltip_text = 'Please select a VA value';
    }
    $(this).data('tooltip-content', tooltip_text);

  }, function(e) {
    $('body > div:last').remove();
  });
}

function OphCiExamination_VisualAcuity_getNextKey(suffix) {
  var keys;
  if(suffix === 'VisualAcuity'){
    keys = $('.visualAcuityReading').map(function(index, el) {
      return parseInt($(el).attr('data-key'));
    }).get();
  } else {
    keys = $('.nearvisualAcuityReading').map(function(index, el) {
      return parseInt($(el).attr('data-key'));
    }).get();
  }

  if(keys.length) {
    return Math.max.apply(null, keys) + 1;
  } else {
    return 0;
  }
}

function OphCiExamination_NearVisualAcuity_addReading(side, selected_data){
  var template = $('#nearvisualacuity_reading_template').html();
  OphCiExamination_VisualAcuity_addReading(side,selected_data, template, 'NearVisualAcuity')
}

function OphCiExamination_VisualAcuity_addReading(side, selected_data, template, suffix) {
  if(typeof template === 'undefined'){
    template = $('#visualacuity_reading_template').html();
  }
  if(typeof suffix === 'undefined'){
    suffix = 'VisualAcuity';
  }
  var data = {
    "key" : OphCiExamination_VisualAcuity_getNextKey(suffix),
    "side" : side,
  };
  Object.assign(data, selected_data);
  var form = Mustache.render(template, data);

  $('section[data-element-type-class="'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+'"] .js-element-eye.'+side+'-eye .noReadings').hide().find('input:checkbox').each(function() {
    $(this).attr('checked', false);
  });
  if (suffix === 'VisualAcuity'){
    var table = $('section[data-element-type-class="'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+'"] .js-element-eye[data-side="'+side+'"] table.va_readings');
  } else {
    var table = $('section[data-element-type-class="'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+'"] .js-element-eye[data-side="'+side+'"] table.near-va-readings');
  }

  table.show();
  $('tbody', table).append(form);

  OphCiExamination_VisualAcuity_ReadingTooltip(table.find('tr').last());
}

/**
 * Which method ID to preselect on newly added readings.
 * Returns the next unused ID.
 * @param side
 * @returns integer
 */
function OphCiExamination_VisualAcuity_getNextMethodId(side, suffix) {
  var method_ids = OphCiExamination_VisualAcuity_method_ids;
  $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+' [data-side="' + side + '"] .method_id').each(function() {
    var method_id = $(this).val();
    method_ids = $.grep(method_ids, function(value) {
      return value != method_id;
    });
  });
  return method_ids[0];
}

function OphCiExamination_VisualAcuity_bestForSide(side) {
  var table = $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_VisualAcuity [data-side="' + side + '"] table');
  if (table.is(':visible')) {
    var best = 0;
    table.find('tr .va-selector').each(function() {
      if (parseInt($(this).val()) > best) {
        best = parseInt($(this).val());
      }
    });
    return best;
  }
  return null;
}

function swapElement(element_to_swap, elementTypeClass, params){
    const nva = elementTypeClass.endsWith("NearVisualAcuity");
    const sidebar = $('#episodes-and-events').data('patient-sidebar');
    const $menuLi = sidebar.findMenuItemForElementClass(elementTypeClass);
    let $parentLi;

    if ($menuLi) {
        let $href = $menuLi.find('a');
        $href.removeClass('selected').removeClass('error');
        if (!$href.hasClass('selected')) {
            sidebar.markSidebarItems(sidebar.getSidebarItemsForExistingElements($href));

            const $container = $href.parent();
            $parentLi = $($container);
            if (params === undefined)
            params = {};
            $href.addClass('selected');
        }
    }

    element_to_swap.css('opacity','0.5').find('select, input, button').prop('disabled','disabled');
    const element = $parentLi.clone(true);
    const element_type_id = $(element).data('element-type-id');
    const element_type_class = $(element).data('element-type-class');

    let core_params = {
        id: element_type_id,
        patient_id: OE_patient_id,
        previous_id: 0
    };

    $.extend(params, core_params);
    $.get(baseUrl + "/" + moduleName + "/Default/ElementForm", params, function (data) {
        const new_element = $(data);
        const container = $('.js-active-elements');
        const cel = $(container).find('.' + element_type_class);
        const pel = $(container).parents('.element');
        const sideField = $(cel).find('input.sideField');
        if ($(sideField).length && $(pel).find('.element-fields input.sideField').length) {
            $(sideField).val($(pel).find('.element-fields input.sideField').val());

            if ($(sideField).val() == '1') {
                $(cel).find('.js-element-eye.left').addClass('inactive');
            }
            else if ($(sideField).val() == '2') {
                $(cel).find('.js-element-eye.right').addClass('inactive');
            }
        }

        let reading_val = [];
        let method = [];
        let current_eye_va_reading;

        $.each(['right-eye', 'left-eye'], function(i, eye_side){
            current_eye_va_reading = element_to_swap.find('.'+eye_side+' table.'+(nva ? 'near-va-readings' : 'va_readings'));

            // look for .va_readings values
            if(current_eye_va_reading.find('tr').length > 0){
                reading_val[eye_side] = [];
                method[eye_side] = [];
                $.each(current_eye_va_reading.find('tr'), function(i, row){
                    // get value
                    let visualacuity_unit = $('#visualacuity_unit_change option:selected').text();
                    let conversion_values = $(row).find('td:eq(1) i').data('tooltip');
                    let method_val = $(row).find('td:eq(2) input').val();

                    // get values
                    let reading_val_li;
                    for (var i = 0; i < conversion_values.length; i++) {
                        if(conversion_values[i].name === visualacuity_unit){
                            reading_val_li = conversion_values[i].value;
                        }
                    }

                    let method_li = $('.'+eye_side+' ul[data-id="method"]').find('li[data-id="'+method_val+'"]');

                    // get index and label
                    reading_val[eye_side].push({
                        label: reading_val_li
                    });

                    method[eye_side].push({
                        val_index: method_li.index(),
                    });
                });
            }
        });

        element_to_swap.replaceWith(new_element);

        // select equivalent
        if(Object.keys(reading_val).length > 0 && Object.keys(method).length > 0){
            $.each(Object.keys(reading_val), function(eye_index, eye_side){
                $.each(reading_val[eye_side], function(i, val){
                    let target = $('section[data-element-type-name="'+(nva ? 'Near ' : '')+'Visual Acuity"] .'+eye_side);

                    target.find('ul[data-id="reading_val"] li[data-label="'+val.label+'"]').addClass('selected');
                    target.find('ul[data-id="method"] li:eq('+method[eye_side][i].val_index+')').addClass('selected');
                    target.find('.oe-add-select-search .add-icon-btn').trigger('click');
                });
            });
        }

        element_to_swap.css('opacity','');
    });
}
