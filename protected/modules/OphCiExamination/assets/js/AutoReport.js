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
        containerSelector: '.js-element-eye.column.js-element-eye'
    };

    AutoReportHandler.prototype.initialise = function()
    {
        var self = this;

        this.drawing.registerForNotifications(this, 'drawingNotifications');
        this.side = this.drawing.eye;
        this.$container = $(this.drawing.canvas).parents(this.options.containerSelector);
        this.$edReportField = this.$container.find("input[id$='" + this.options.side + "_ed_report']");
        this.$edReportDisplay = this.$container.find("span[id$='" + this.options.side + "_ed_report_display']");

        // When the container is removed, remove all the diagnoses it had
        this.$container.on('remove', function() { self.removeAll(); });

        this.updateReport();
    };

    AutoReportHandler.prototype.updateReport = function()
    {
        var markup = this.$edReportField.val().replace(/\n/g,'<br />');
        markup = markup.replace('^<br />', ''); // remove first <br>

        // if there is a non-empty comment field,
        // "No abnormality" text should be removed

        var id = this.$edReportField.attr("id");
        var $textarea = $("#"+id.replace(/_ed_report$/, "_description"));
        if($textarea.length < 1) {
            $textarea = $("#"+id.replace(/_ed_report$/, "_comments"));
        }

        if(markup === "No abnormality" && $textarea.val() !== '') {
            markup = '';
            this.$edReportField.val(markup);
        }

        this.$edReportDisplay.html(markup);

        var diagnoses = this.drawing.diagnosis();
        var sidedDiagnoses = Array();
        for (var i = 0; i < diagnoses.length; i++) {
            sidedDiagnoses.push([diagnoses[i], this.side]);
        }
        OpenEyes.OphCiExamination.Diagnosis.setForSource(sidedDiagnoses, this.$container);
    };

    AutoReportHandler.prototype.removeAll = function()
    {
        OpenEyes.OphCiExamination.Diagnosis.setForSource([], this.$container);
        // Unassign the container to prevent zombie events from modifying the report further
        this.$container = $();
    };

    AutoReportHandler.prototype.drawingNotifications = function(msgArray)
    {
        clearTimeout(this.updateTimer);
        this.updateTimer = setTimeout(function() {
            if(!this.$container.length) {
                return;
            }
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
