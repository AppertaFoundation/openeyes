

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

      $('.js-remove-add-comments').on('click', function (e) {
        e.preventDefault();
        $(this).parent().hide();
        var container = $($(this).data('input'));
        container.show();
      });

      $('.js-add-comments').on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        var container = $($(this).data('input'));
        container.val('');
        button.hide();
        container.parent().show();
        container.find('textarea, input').andSelf().focus();
      });

    };

    SocialHistoryController.prototype.addEntry = function() {
      data = {};
      var select_lists = ['occupation', 'smoking_status', 'accommodation', 'alcohol'];
      var postfixes = ['_occupation_id', '_smoking_status_id', '_accommodation_id', '_alcohol_intake'];
      for (i in select_lists) {
        data[select_lists[i]] = this.$popupSelector.find('ul.'+select_lists[i]+' .selected').data('id');
        (data[select_lists[i]] == null)? '' : this.$tableSelector.find('#'+this.options.modelName+postfixes[i]).val(data[select_lists[i]]);
        this.$tableSelector.find('#'+this.options.modelName+postfixes[i]).change();
      }

      var driving_statuses =  this.$popupSelector.find('ul.driving_status .selected');
      for(var i = 0; i < driving_statuses.length; i++) {
        var id = $(driving_statuses[i]).data('id');
        $driving_selector = this.$tableSelector.find('#'+this.options.modelName+'_driving_statuses');
        $driving_selector.val(id);
        $driving_selector.change();
      }

      this.$popupSelector.find('.selected').removeClass('selected');

    };

  exports.SocialHistoryController = SocialHistoryController;
})(OpenEyes.OphCiExamination);