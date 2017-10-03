var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.MedicalLidsController = (function () {
  function MedicalLidsController(_drawing, options)
  {
    this.drawing = _drawing;
    this.options = $.extend(true, {}, MedicalLidsController._defaultOptions, options);
    this.initialise();
  }

  MedicalLidsController.prototype.initialise = function()
  {
    this.drawing.registerForNotifications(this, 'drawingNotifications');
    this.$edReportField = $('#OEModule_OphCiExamination_models_MedicalLids_' + this.options.side + '_ed_report');
    this.$edReportDisplay = $('#OEModule_OphCiExamination_models_MedicalLids_'+this.options.side+'_ed_report_display');
    this.updateReport();
  };

  MedicalLidsController.prototype.updateReport = function()
  {
    this.$edReportDisplay.html(this.$edReportField.val().replace(/\n/g,'<br />'));
  };
  
  MedicalLidsController.prototype.drawingNotifications = function(msgArray)
  {
    this.updateReport();
  };

  return MedicalLidsController;
})();

function medicalLidsListener(_drawing)
{
  var canvas = $(_drawing.canvas);
  var controller = canvas.data('controller');
  if (!controller) {
    controller = new OpenEyes.OphCiExamination.MedicalLidsController(
      _drawing,
      {side: (_drawing.eye === 1 ? 'left' : 'right')}
    );
    canvas.data('controller', controller);
  }

}
