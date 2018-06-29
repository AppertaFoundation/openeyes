

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function SocialHistoryController(options) {
      this.options = $.extend(true, {}, SocialHistoryController._defaultOptions, options);
      this.$tableSelector = $( '#'+this.options.modelName+'_entry_table');
      this.$popupSelector = $('#add-to-social-history');
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
    };

    SocialHistoryController.prototype.addEntry = function() {
      data = {};
      data['occupation_id'] = this.$popupSelector.find('ul.occupation .selected').data('id');
      data['smoking_status_id'] = this.$popupSelector.find('ul.smoking_status .selected').data('id');
      data['accommodation_id'] = this.$popupSelector.find('ul.accommodation .selected').data('id');
      data['alcohol_intake'] = this.$popupSelector.find('ul.alcohol .selected').data('str');

      var driving_statuses =  this.$popupSelector.find('ul.driving_status .selected');
      for(var i = 0; i < driving_statuses.length; i++) {
        var id = $(driving_statuses[i]).data('id');
        $driving_selector = this.$tableSelector.find('#'+this.options.modelName+'_driving_statuses');
        $driving_selector.val(id);
        $driving_selector.change();
      }

      (data['occupation_id'] == null)? '' : this.$tableSelector.find('#'+this.options.modelName+'_occupation_id').val(data['occupation_id']);
      (data['smoking_status_id'] == null)? '': this.$tableSelector.find('#'+this.options.modelName+'_smoking_status_id').val(data['smoking_status_id']);
      (data['accommodation_id'] == null)? '': this.$tableSelector.find('#'+this.options.modelName+'_accommodation_id').val(data['accommodation_id']);
      (data['alcohol_intake'] == null)? '': this.$tableSelector.find('#'+this.options.modelName+'_alcohol_intake').val(data['alcohol_intake']);

      this.$popupSelector.find('.selected').removeClass('selected');

    };

  exports.SocialHistoryController = SocialHistoryController;
})(OpenEyes.OphCiExamination);