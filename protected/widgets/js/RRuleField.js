(function (exports) {

    'use strict';

    function RRuleField(element, options) {
        this.element = element;
        this.options = $.extend(true, {}, RRuleField._defaultOptions, options);
        this.init();
    }

    RRuleField._defaultOptions = {
        'daysOfWeek':   ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'changeClass':  'rrulefield-input'
    };

    var dayCodes    = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

    RRuleField.prototype.init = function()
    {
        this._rrule = RRule.fromString(this.element.val());
        this.render();
    };

    RRuleField.prototype.render = function()
    {
        var self = this;
        // mask the original input field
        $(self.element).hide();
        self.container = $("<div />").insertAfter(self.element);
        // render the days of the week selection
        self.renderDaysOfWeek();

        // update the field every time the rrule field form is changed
        $(self.container).on('change', '.'+self.options.changeClass, function(e) {self.updateField()});
    };

    RRuleField.prototype.isDaySelected = function(day)
    {
        return ($.inArray(parseInt(day), this._rrule.options.byweekday) > -1);
    };

    /**
     * @TODO: template this?
     * @TODO: set the field values based on the current rrule
     */
    RRuleField.prototype.renderDaysOfWeek = function()
    {
        var self = this;

        var fields = "<label>Day(s) of the Week:</label>";
        for (var i in dayCodes) {
            fields += '<label class="inline"><input type="checkbox" name="dayOfWeek[]" value="' + i + '" class="'+ this.options.changeClass +'"';
            if (self.isDaySelected(i)) {
                console.log('yay');
                fields += ' checked';
            }

            fields += '/>' + this.options.daysOfWeek[i] + '</label>';
        }
        $(this.container).append(fields);
    };

    /**
     * This iterates through the form to pull out the selected values, create a new RRule object and use that
     * to populate the original form element.
     */
    RRuleField.prototype.updateField = function()
    {
        var self = this;
        var weekdays = [];
        $(this.container).find('input[name="dayOfWeek\[\]"]').each(function() {
            if ($(this).prop('checked'))
                weekdays.push($(this).val());
        });
        self._rrule = new RRule({
            freq: RRule.DAILY,
            byweekday: weekdays
        });

        $(self.element).val(self._rrule.toString());
    };

    /**
     * Simple attachment function
     *
     * @param element
     * @param options
     * @returns {*}
     */
    function attach(element, options)
    {
        if (options === undefined)
            options = {};
        console.log(element, options);
        // TODO: (if this were ever to become useful) a remove and replace method to refresh it
        if (!element.data('OpenEyes.UI.Widgets.RRuleField')) {
            element.data('OpenEyes.UI.Widgets.RRuleField', new RRuleField(element, options));
        }
        return element;
    }

    exports.RRuleField = attach;

}(this.OpenEyes.UI.Widgets));