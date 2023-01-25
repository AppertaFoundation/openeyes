<?php
$selected_template_id = $selected_template_id ?? null;
?>
<div id="js-template-prefill-selection-popup" class="oe-popup-wrap" style="display: none">
  <div class="oe-popup">
    <div class="remove-i-btn js-template-prefill-popup-close"></div>
    <div class="title">OpNote - pre-fill data from template</div>
    <div class="oe-popup-content undefined">
      <div class="flex-t">
        <h4 class='js-template-prefill-none-found' style='display: none'>No templates found for selected surgeon or procedure(s)</h4>
        <div class="cols-6">
          <h4>Pre-filled for procedure(s)</h4>
          <ul class="dot-list large-text js-template-prefill-selection-procedures">
            <?php foreach ($procedures as $procedure) {
                echo '<li>' . CHtml::encode($procedure->term) . '</li>';
            } ?>
          </ul>
        </div>
        <div class="cols-6">
          <h4>Your pre-fill templates</h4>
          <ul class="btn-list js-template-prefill-selection-choices">
            <?php foreach ($filtered_templates as $template) {
                $class = $template->event_template_id === $selected_template_id ? ' class="selected"' : '';
                echo '<li data-template-id="' . $template->event_template_id . '"' . $class . '>' . CHtml::encode($template->name) . '</li>';
            } ?>
          </ul>
          <hr class="divider">
          <button type="button" class="cols-full js-template-prefill-selection-action">Clear pre-filled template</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  const currentTemplateId = (new URL(window.location)).searchParams.get('template_id') ?? '';

  function setPopupButtonText() {
    let selected = $('#js-template-prefill-selection-popup .js-template-prefill-selection-choices li.selected');

    if (selected.length === 0 || selected.attr('data-template-id') !== currentTemplateId) {
      $('#js-template-prefill-selection-popup .js-template-prefill-selection-action').text('Select template');
    } else {
      $('#js-template-prefill-selection-popup .js-template-prefill-selection-action').text('Clear pre-filled template');
    }
  }

  function setButtonAndPopupState() {
    let templates_list = $('#js-template-prefill-selection-popup .js-template-prefill-selection-choices');

    let selected = templates_list.find('li.selected');
    let selected_name = selected.length !== 0 ? selected.text() : 'Click to choose a template';

    setPopupButtonText();

    let new_button_text = selected_name;

    if (templates_list.find('li').length > 1) {
      new_button_text = `${selected_name} - Click to change`;
    }

    $('#js-template-prefill-popup-open').contents().filter(function() { return this.nodeType === Node.TEXT_NODE; }).replaceWith(new_button_text);

    templates_list.find('li').off('click').on('click', function() {
      templates_list.find('li.selected').removeClass('selected');

      $(this).addClass('selected');

      setPopupButtonText();
    });
  }

  function changeSurgeonOrProcedures() {
    $.ajax(
      '/OphTrOperationnote/Default/findTemplatesFor',
      {
        data: {
          surgeon_id: $('#Element_OphTrOperationnote_Surgeon_surgeon_id').val(),
          procedures: $('.js-procedure').map(function() { return $(this).val(); }).get(),
        },
        success: function(data) {
          let procedures_list = $('#js-template-prefill-selection-popup .js-template-prefill-selection-procedures');
          let templates_list = $('#js-template-prefill-selection-popup .js-template-prefill-selection-choices');

          procedures_list.children().remove();
          templates_list.children().remove();

          if (data === null) {
            $('.js-template-prefill-none-found').show();

            procedures_list.parent().hide();
            templates_list.parent().hide();
          } else {
            for (let term of data.procedures) {
              procedures_list.append(`<li>${term}</li>`);
            }

            for (let id in data.templates) {
              const template = $(`<li data-template-id="${id}">${data.templates[id]}</li>`);

              if (id == currentTemplateId) {
                template.addClass('selected');
              }

              templates_list.append(template);
            }

            procedures_list.parent().show();
            templates_list.parent().show();

            $('.js-template-prefill-none-found').hide();
          }

          setButtonAndPopupState();
        }
      }
    );
  }

  setButtonAndPopupState();

  $('#js-template-prefill-popup-open').on('click', function() {
    $('#js-template-prefill-selection-popup').show();
  });

  $('#js-template-prefill-selection-popup .js-template-prefill-popup-close').on('click', function() {
    $('#js-template-prefill-selection-popup').hide();
  });

  $('#js-template-prefill-selection-popup .js-template-prefill-selection-action').on('click', function() {
    const newTemplateId = $('#js-template-prefill-selection-popup .js-template-prefill-selection-choices li.selected').attr('data-template-id');

    loadOrClearTemplate(newTemplateId);
  });

  $('#Element_OphTrOperationnote_Surgeon_surgeon_id').on('change', changeSurgeonOrProcedures);

  let oldAddFunction = window.callbackAddProcedure;
  let oldRemoveFunction = window.callbackRemoveProcedure;

  let newAddFunction = async function (procedure_id) {
    changeSurgeonOrProcedures();

    oldAddFunction(procedure_id);
  };

  let newRemoveFunction = function (procedure_id) {
    changeSurgeonOrProcedures();

    oldRemoveFunction(procedure_id);
  };

  window.callbackAddProcedure = newAddFunction;
  window.callbackRemoveProcedure = newRemoveFunction;
});
</script>
