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

    var dayCodes = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];
    var dayMap = [RRule.MO, RRule.TU, RRule.WE, RRule.TH, RRule.FR, RRule.SA, RRule.SU];

    RRuleField.prototype.init = function()
    {
        try {
            this._rrule = RRule.fromString(this.element.val());
        }
        catch (e) {
            // simple error handling here. In theory should never arise, but this will at
            // least prevent the widget from masking any issues by still rendering the form
            console.log(this.element.val() + ' is not a valid for rrule');
            return;
        }
        this.render();
    };

    /**
     * Render the form fields for this widget
     */
    RRuleField.prototype.render = function()
    {
        var self = this;
        // mask the original input field
        $(self.element).hide();
        // create a container in which we can store all our input fields for defining the RRule
        self.container = $("<div />").insertAfter(self.element);

        self.renderDescription(self.container);
        // render the days of the week selection
        self.renderDaysOfWeek(self.container);
        self.renderWeekSelection(self.container);

        self.updateField();
        // update the field every time the rrule field form is changed
        $(self.container).on('change', '.'+self.options.changeClass, function(e) {self.updateField()});
    };

    /**
     * Check current rule configuration for whether the given day should be selected in the form
     *
     * @param day
     * @returns {boolean}
     */
    RRuleField.prototype.isDaySelected = function(day)
    {
        if (this._rrule.options.byweekday)
            return ($.inArray(parseInt(day), this._rrule.options.byweekday) > -1);
        if (this._rrule.options.bynweekday) {
            for (var i in this._rrule.options.bynweekday) {
                if (day == this._rrule.options.bynweekday[i][0]) {
                    return true;
                }
            }
        }
        return false;
    };

    /**
     * Only returns true if not all weeks are selected, and the specific week should be.
     *
     * @param week
     * @returns {boolean}
     */
    RRuleField.prototype.isWeekSelected = function(week)
    {
        if (this._rrule.options.byweekday) {
            // if the rrule library has set a byweekday option
            // this implies all weeks selected
            return false;
        }
        else if (this._rrule.options.bynweekday) {
            for (var i in this._rrule.options.bynweekday) {
                if (week == this._rrule.options.bynweekday[i][1])
                    return true;
            }
        }
        return false;
    };

    /**
     * Display the rrule description as the form is updated.
     * 
     * @param container
     */
    RRuleField.prototype.renderDescription = function(container)
    {
        $(container).append('<div class="rrule-description" style="font-style: italic; font-size:0.7em;"></div><hr style="margin: 5px;"/>');
    };

    /**
     * Create the form component for selecting the days of the week the rule applies to
     *
     * @TODO: templating?
     */
    RRuleField.prototype.renderDaysOfWeek = function(container)
    {
        var self = this;

        var fields = "<h4>Day(s) of the Week:</h4>";
        for (var i in dayCodes) {
            fields += '<label class="inline"><input type="checkbox" name="dayOfWeek[]" value="' + i + '" class="'+ this.options.changeClass +'"';
            if (self.isDaySelected(i)) {
                fields += ' checked';
            }

            fields += '/>' + self.options.daysOfWeek[i] + '</label>';
        }

        $(container).append(fields);
    };

    /**
     * Create form component for selecting which weeks of the month the rule applies to
     *
     * @param container
     */
    RRuleField.prototype.renderWeekSelection = function(container)
    {
        var self = this;

        var weekSelection = $('<div style="padding-top: 20px;" />');
        container.append(weekSelection);

        // flag to determine set the everyweek box checked
        var everyWeek = true;

        // the individual weeks checkboxes span
        var weekFields = '<span class="week-number-wrapper">or<br />';
        for (var i = 1; i <=5; i++) {
            weekFields += '<label class="inline"><input type="checkbox" name="week-number[]" value="' + i + '" class="week-number ' + this.options.changeClass + '" ';
            if (self.isWeekSelected(i)) {
                everyWeek = false;
                weekFields += ' checked';
            }
            weekFields += '/>Week ' + i + '</label>';
        }
        weekFields += '</span>';

        // defining this afterward so we can apply the checked property correctly.
        var everyWeekField = '<h4>Weeks of the Month:</h4><label class="inline"><input type="checkbox" name="every-week" class="every-week '+ this.options.changeClass +'";';
        if (everyWeek)
            everyWeekField += ' checked';
        everyWeekField += '/>Every Week</label>';
        // the hint field is there to explain why you can't uncheck the every week box alone.
        everyWeekField += '<span style="display:none; font-size: 0.6em;" class="info hint">select a week number to uncheck this</span><br />';

        weekSelection.append(everyWeekField + weekFields);

        weekSelection.on('change', 'input', function(e) {
            var showHint = false;
            if ($(e.target).hasClass('every-week')) {
                if ($(e.target).prop('checked')) {
                    weekSelection.find('.week-number:checked').prop('checked', false);
                }
                else {
                    if (weekSelection.find('.week-number:checked').length == 0) {
                        $(e.target).prop('checked', true);
                        showHint = true;
                    }
                }
            }
            else {
                if (weekSelection.find('.week-number:checked').length == 0) {
                    weekSelection.find('.every-week').prop('checked', true);
                }
                else {
                    weekSelection.find('.every-week').prop('checked', false);
                }
            }
            if (showHint) {
                weekSelection.find('.hint').fadeIn();
            }
            else {
                weekSelection.find('.hint').fadeOut();
            }
        });
    };

    /**
     * This iterates through the form to pull out the selected values, create a new RRule object and use that
     * to populate the original form element.
     */
    RRuleField.prototype.updateField = function()
    {
        var self = this;
        var weekdays = [];
        $(self.container).find('input[name="dayOfWeek\[\]"]').each(function() {
            if ($(this).prop('checked'))
                weekdays.push(dayMap[parseInt($(this).val())]);
        });

        var rruleOptions = {};
        if (!weekdays.length) {
            weekdays = dayMap;
        }

        if ($(self.container).find('input[name="every-week"]').prop('checked')) {
            if (weekdays.length == dayMap.length) {
                rruleOptions.freq = RRule.DAILY;
            }
            else {
                rruleOptions.freq = RRule.WEEKLY;
                rruleOptions.byweekday = weekdays;
            }

        }
        else {
            rruleOptions.freq = RRule.MONTHLY;
            rruleOptions.byweekday = [];
            for (var i in weekdays) {
                $(self.container).find('.week-number:checked').each(function() {
                    rruleOptions.byweekday.push(weekdays[i].nth(parseInt($(this).val())));
                });
            }
        }
        self._rrule = new RRule(rruleOptions);

        var description = self._rrule.toText();
        description = description[0].toUpperCase() + description.slice(1);

        $(self.container).find('.rrule-description').text(description);
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
        // TODO: (if this were ever to become useful) a remove and replace method to refresh it
        if (!element.data('OpenEyes.UI.Widgets.RRuleField')) {
            element.data('OpenEyes.UI.Widgets.RRuleField', new RRuleField(element, options));
        }
        return element;
    }

    exports.RRuleField = attach;

}(this.OpenEyes.UI.Widgets));