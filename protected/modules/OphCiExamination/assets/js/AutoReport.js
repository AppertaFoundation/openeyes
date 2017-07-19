var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.AutoReportHandler = (function () {
    function AutoReportHandler(_drawing, options)
    {
        this.drawing = _drawing;
        this.options = $.extend(true, {}, AutoReportHandler._defaultOptions, options);
        this.initialise();
    }

    AutoReportHandler._defaultOptions = {
        containerSelector: '.element-eye.column.side'
    };

    AutoReportHandler.prototype.initialise = function()
    {
        this.drawing.registerForNotifications(this, 'drawingNotifications');
        this.side = this.drawing.eye;
        this.$container = $(this.drawing.canvas).parents(this.options.containerSelector);
        this.$edReportField = this.$container.find("input[id$='" + this.options.side + "_ed_report']");
        this.$edReportDisplay = this.$container.find("span[id$='" + this.options.side + "_ed_report_display']");

        this.updateReport();
    };

    AutoReportHandler.prototype.updateReport = function()
    {
        var markup = this.$edReportField.val().replace(/\n/g,'<br />');
        markup = markup.replace('^<br />', ''); // remove first <br>
        this.$edReportDisplay.html(markup);

        var diagnoses = this.drawing.diagnosis();
        var sidedDiagnoses = Array();
        for (var i = 0; i < diagnoses.length; i++) {
            sidedDiagnoses.push([diagnoses[i], this.side])
        }
        OpenEyes.OphCiExamination.Diagnosis.setForSource(sidedDiagnoses, this.$container);
    };

    AutoReportHandler.prototype.drawingNotifications = function(msgArray)
    {
        clearTimeout(this.updateTimer);
        this.updateTimer = setTimeout(function() {
            this.updateReport();
        }.bind(this), 300);
    };

    return AutoReportHandler;
})();


function autoReportListener(_drawing)
{
    var canvas = $(_drawing.canvas);
    var autoreport = canvas.data('autoreport');
    if (!autoreport) {
        autoreport = new OpenEyes.OphCiExamination.AutoReportHandler(
            _drawing,
            {side: (_drawing.eye === 1 ? 'left' : 'right')}
        );
        canvas.data('autoreport', autoreport);
    }
}
