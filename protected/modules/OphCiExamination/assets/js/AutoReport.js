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
        containerSelector: '.ed-widget:first'
    };

    AutoReportHandler.prototype.initialise = function()
    {
        this.drawing.registerForNotifications(this, 'drawingNotifications');
        this.$container = $(this.drawing.canvas).parents(this.options.containerSelector);
        this.$edReportField = this.$container.find("input[id$='" + this.options.side + "_ed_report']");
        this.$edReportDisplay = this.$container.find("span[id$='" + this.options.side + "_ed_report_display']");

        this.updateReport();
    };

    AutoReportHandler.prototype.updateReport = function()
    {
        var markup = this.$edReportField.val().replace(/\n/g,'<br />');
        markup = markup.replace('<br />', ''); // remove first <br>
        this.$edReportDisplay.html(markup);
    };

    AutoReportHandler.prototype.drawingNotifications = function(msgArray)
    {
        this.updateReport();
    };

    return AutoReportHandler;
})();


function autoReportListener(_drawing)
{
    var canvas = $(_drawing.canvas);
    var controller = canvas.data('controller');
    if (!controller) {
        controller = new OpenEyes.OphCiExamination.AutoReportHandler(
            _drawing,
            {side: (_drawing.eye === 1 ? 'left' : 'right')}
        );
        canvas.data('controller', controller);
    }

}
