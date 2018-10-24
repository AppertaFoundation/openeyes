

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function SocialHistoryController(options) {
      this.options = $.extend(true, {}, SocialHistoryController._defaultOptions, options);
      this.$tableSelector = $( '#'+this.options.modelName+'_entry_table');
      this.$popupSelector = $('#add-to-social-history');

      this.initialiseTriggers();

    }

    SocialHistoryController._defaultOptions = {
      modelName: 'OEModule_OphCiExamination_models_SocialHistory'
    };

    
    SocialHistoryController.prototype.initialiseTriggers = function () {

      $('#OEModule_OphCiExamination_models_SocialHistory_occupation_id').on('change', function() {
        if ($('#OEModule_OphCiExamination_models_SocialHistory_occupation_id option:selected').attr('value') == 7/*Other*/) {
          $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').show();
        } else {
          $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').hide();
          $('#OEModule_OphCiExamination_models_SocialHistory_type_of_job').val('');
        }
      });

      var select_lists = ['occupation', 'alcohol', 'smoking_status', 'accommodation'];
      for (i in select_lists){
        $('#add-to-social-history ul.'+select_lists[i]+' li').on('click', function (e) {
          $(this).siblings('.selected').removeClass('selected');
        })
      }

    };

    SocialHistoryController.prototype.addEntry = function (selectedItems) {

      // Remove all driving statuses in case they have been deselected
      $('#' + this.options.modelName + '_driving_statuses')
        .closest('.multi-select-list')
        .find('.multi-select-remove')
        .click();

      // reset textField input for driving statuses
      $('#textField_driving_statuses').html('');

      for (var i in selectedItems) {
        var item = selectedItems[i];
        var itemSetId = item.itemSet.options.id;
        var $field = this.$tableSelector.find('#' + this.options.modelName + '_' + itemSetId);
        var $textField = this.$tableSelector.find('#textField' + '_' + itemSetId);
        var field_option = $field.find('option[value='+ item.id +']').text().trim();

        $field.val(item.id);
        $field.change();

        if (itemSetId === "driving_statuses") {
          // insert first driving status
          if ($textField.html() == '')
            $textField.html(field_option);
          // append the rest of the driving statuses to the first one
          else
            $textField.append(', ' + field_option);
        } else if (itemSetId === "alcohol_intake") {
          $textField.html(item.id);
        } else {
          // for the rest of the elements, show the info in the textField
          $textField.html($field.find(":selected").text());
        }
      }

      this.$popupSelector.find('.selected').removeClass('selected');

    };

  exports.SocialHistoryController = SocialHistoryController;
})(OpenEyes.OphCiExamination);