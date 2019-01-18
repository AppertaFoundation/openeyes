$(document).ready(function () {
  function visualAcuityChange(target, near) {
    var suffix = 'VisualAcuity';
    if(near === 'near'){
      suffix = 'NearVisualAcuity';
    }
    removeElement($(target).closest('.element[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]'), true);
    var el = $('.event-content').find('ul.sub-elements-list li[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]');
    if (el.length) {
      el.addClass('clicked');
      addElement(el, true, true, false, {unit_id: $(target).val()});
    } else {
      // use a different selector
      var sidebar = $('#episodes-and-events').data('patient-sidebar');
      if (sidebar) {
        sidebar.addElementByTypeClass(OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix, {unit_id: $(target).val()});
      } else {
        console.log('Cannot find sidebar to manipulate elements for VA change');
      }
    }
  }

  ($('.va_readings,.near-va-readings').each(function(){
    if($(this).find('tbody tr').length){
        $(this).siblings('.noReadings').hide();
    }
  }));

  $(this).delegate('#nearvisualacuity_unit_change', 'change', function() {
    visualAcuityChange(this, 'near');
  });

  $(this).delegate('#visualacuity_unit_change', 'change', function() {
    visualAcuityChange(this, '');
  });

  $(this).delegate(
    '.visualAcuityReading .removeReading',
    'click', function(e) {
      var activeForm = $(this).closest('.active-form');

      var $section =  $(this).parents('section');
      var $cviAlert = $('.cvi-alert');
      var threshold = parseInt($cviAlert.data('threshold'));

      $(this).closest('tr').remove();

      if( $section.find('.cvi_alert_dismissed').val() !== "1"){
        var show_alert = false;
        $section.find('.va-selector').each(function(){
          var val = parseInt($(this).val());
          if (val < threshold) {
            show_alert = true;
          } else {
            show_alert = false;
          }
          return;
        });
        if (show_alert) {
          $cviAlert.slideDown(500);
        } else {
          $cviAlert.slideUp(500);
        }
      }

      if ($('.va_readings tbody', activeForm).children('tr').length === 0) {
        $('.noReadings', activeForm).show();
      }
      else {
        // VA can affect DR
        var side = getSplitElementSide($(this));
        OphCiExamination_DRGrading_update(side);
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
      else {
        // VA can affect DR
        var side = getSplitElementSide($(this));
        OphCiExamination_DRGrading_update(side);
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
    // VA can affect DR
    OphCiExamination_DRGrading_update(side);
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
          $('#'+side+'-add-'+element+'-reading').hide();
        } else {
          $('#'+side+'-add-'+element+'-reading').show();
        }
      });
    }
  }

  /* Visual Acuity readings event binding */

  $('#event-content').on('click', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .js-remove-element', function() {
    $('.cvi-alert').slideUp(500);
  });

  $('#event-content').on('change', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .va-selector', function(){
    var $section =  $(this).closest('section');
    var $cviAlert = $('.cvi-alert');
    var threshold = parseInt($cviAlert.data('threshold'));

    if( $section.find('.cvi_alert_dismissed').val() !== "1"){
      var show_alert = null;
      $section.find('.va-selector').each(function(){
        var val = parseInt($(this).val());
        if (val < threshold) {
          show_alert = (show_alert === null) ? true : show_alert;
        } else {
          show_alert = false;
        }
        return;
      });

      if (show_alert) {
        $cviAlert.slideDown(500);
      } else {
        $cviAlert.slideUp(500);
      }
    }
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
