var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function(exports) {
    'use strict';

    /**
     * Simple class to replace element content with patient record information
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
        // TODO: consider moving outside the patient controller.
        'requestUrl': '/patient/previousElements',
        'noResultsText': 'no previous records available'
    };

    /**
     * Check the element is defined correctly to allow the data to be loaded.
     */
    InlinePreviousElement.prototype.validate = function() {
        // TODO: verify the template
        if (!this.$element.data('element-type-id')) {
            console.log('element-type-id is a required data property!!');
        }
        if (!OE_patient_id) {
            console.log('OE_patient_id must be set.');
        }
    };

    /**
     * Load the data required for the element and replace the contents by passing the
     * results to the provided template.
     */
    InlinePreviousElement.prototype.process = function() {
        if (this.$element.data('no-results-text') === undefined) {
            this.$element.data('no-results-text', this.options['noResultsText']);
        }
        var args = {
            element_type_id: this.$element.data('element-type-id'),
            patient_id: OE_patient_id
        };
        if (this.$element.data('limit') !== undefined) {
            args['limit'] = this.$element.data('limit')
        }
        $.ajax({
            url: this.options.requestUrl,
            data: args,
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
}(this));

$(document).ready(function() {
    // simple encapsulation for use on load and after mutations.
    var runLoader = function(element) {
        if ($(element).data('inline-previous-loader') === undefined) {
            $(element).data('inline-previous-loader', new InlinePreviousElement($(element)));
        }
    }

    $('.inline-previous-element').each(function() {
        runLoader($(this));
    });

    // After a change we simple run through the relevant elements to
    // apply the loader where necessary
    var observer = new MutationObserver(function(mutations) {
        $('.inline-previous-element').each(function() {
            runLoader($(this));
        });
    });

    observer.observe(document.documentElement, {'childList': true, 'subtree': true});
});