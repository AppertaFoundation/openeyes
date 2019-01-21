var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.DilationController = (function () {

  function DilationController(options, table, adder){
    this.$table = table;
    this.$addList = adder.find('ul');

    this.initialiseTriggers();
  }

  DilationController.prototype.OphCiExamination_Dilation_getNextKey = function() {
    var keys = $('.main-event .edit-Dilation .dilationTreatment').map(function (index, el) {
      return parseInt($(el).attr('data-key'));
    }).get();
    if (keys.length) {
      return Math.max.apply(null, keys) + 1;
    } else {
      return 0;
    }
  };

  DilationController.prototype.OphCiExamination_Dilation_addTreatment = function(element, side) {

    var drug_id = $(element).attr('data-str');
    var data_order = $(element).attr('data-order');
    if (drug_id) {
      var drug_name = $(element).text();
      var template = $('#dilation_treatment_template').html();
      var data = {
        "key": this.OphCiExamination_Dilation_getNextKey(),
        "side": side,
        "drug_name": drug_name,
        "drug_id": drug_id,
        "data_order": data_order,
        "treatment_time": (new Date).toTimeString().substr(0, 5)
      };
      var form = Mustache.render(template, data);
      this.$table.show();
      $(element).closest('.js-element-eye').find('.timeDiv').show();
      $('tbody', this.$table).append(form);
    }
  };

  DilationController.prototype.initialiseTriggers = function(){
    var addList = this.$addList;

    this.$table.delegate('.removeTreatment', 'click', function (e) {
      var wrapper = $(this).closest('.js-element-eye');
      var row = $(this).closest('tr');
      var id = row.find('.drugId').val();
      addList.find('li[data-str=\'' + id + '\']').show();
      row.remove();
      if ($('.dilation_table tbody tr', wrapper).length === 0) {
        $('.dilation_table', wrapper).hide();
        $('.timeDiv', wrapper).hide();
      }
      e.preventDefault();
    });
  };

  $('.dilation_drug').keypress(function (e) {
    if (e.keyCode == 13) {
      var side = $(this).closest('.js-element-eye').attr('data-side');
      OphCiExamination_Dilation_addTreatment(this, side);
    }
  });

  $(this).delegate('.main-event .edit-Dilation .clearDilation', 'click', function (e) {
    $(this).closest('.js-element-eye').find('tr.dilationTreatment a.removeTreatment').click();
    e.preventDefault();
  });

  return DilationController;
})();