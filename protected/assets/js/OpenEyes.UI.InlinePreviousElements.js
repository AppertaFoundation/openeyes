var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function(exports) {
    'use strict';

    /**
     *
     * @param element
     * @param options
     * @constructor
     */
    function InlinePreviousElement(element, options) {
        this.$element = $(element);
        this.options = $.extend(true, {}, InlinePreviousElement._defaultOptions, options);
        this.validate();
        this.process();
    }

    InlinePreviousElement._defaultOptions = {
        'requestUrl': '/patient/previousElements',
        'noResultsText': 'no previous records available'
    };

    InlinePreviousElement.prototype.validate = function() {
        if (!this.$element.data('element-type-id')) {
            console.log('element-type-id is a required data property!!');
        }
        if (!OE_patient_id) {
            console.log('OE_patient_id must be set.');
        }
    };

    InlinePreviousElement.prototype.process = function() {
        if (this.$element.data('no-results-text') === undefined) {
            this.$element.data('no-results-text', this.options['noResultsText']);
        }
        $.ajax({
            url: this.options.requestUrl,
            data: {element_type_id: this.$element.data('element-type-id'), patient_id: OE_patient_id},
            dataType: 'JSON',
            success: function (data) {
                if (data.length) {
                    var template = $('#' + this.data('template-id')).html();
                    var renderedResults = [];
                    for (var i in data) {
                        renderedResults.push(Mustache.render(template, data[i]));
                    }
                    this.replaceWith(renderedResults.join('<br />'));
                } else {
                    this.replaceWith(this.data('no-results-text'));
                }
            }.bind(this.$element),
            error: function () {
                $(this).html('<b>Could not retrieve historical data</b>');
            }.bind(this.$element)
        });
    };

    exports.InlinePreviousElement = InlinePreviousElement;
}(OpenEyes.UI));

$(document).ready(function() {
    var runLoader = function(element) {
        if ($(element).data('inline-previous-loader') === undefined) {
            $(element).data('inline-previous-loader', new OpenEyes.UI.InlinePreviousElement($(element)));
        }
    }
    $('.inline-previous-element').each(function() {
        runLoader($(this));
    });

    var observer = new MutationObserver(function(mutations) {
        $('.inline-previous-element').each(function() {
            runLoader($(this));
        });
    });

    observer.observe(document.documentElement, {childList: true, subtree: true, attributes: true});
});
