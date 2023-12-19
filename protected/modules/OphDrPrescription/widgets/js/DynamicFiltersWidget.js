class DynamicFilters {
    constructor(options) {
        this._defaultOptions = {
            adder_dialog_item_set: [],
            filter_result_selector: '#dynamic-param-list',
            template_selector: '#dynamic-advanced-signatory-filter',
            prefix: 'dynamic_filters'
        };

        this.options = this.mergeOptions(this._defaultOptions, options);

        this.table = document.querySelector(this.options.filter_result_selector);
        this.template = document.querySelector(this.options.template_selector).textContent;


        this.init();
        this.addEventListeners();
    }

    init() {
        new OpenEyes.UI.AdderDialog({
            itemSets: this.options.adder_dialog_item_set,
            openButton: $('#add-to-advanced-search-filters'),
            parentContainer: 'body',
            id: 'add-parameter-dialog',
            onReturn: (dialog, selectedValues) => {
                const tbody = this.table.querySelector('tbody');
                let tr = Mustache.render(this.template, {
                    "parameter_name": this.options.prefix,
                    "key": OpenEyes.Util.getNextDataKey($(`${this.options.filter_result_selector} tbody tr`), 'key'),
                    "label": selectedValues[0].label,
                    "value_label": selectedValues[1].label.toUpperCase(),
                    "operation_label": selectedValues?.[2]?.label
                });

                tbody.insertAdjacentHTML('beforeend', tr);
                this.toggleInitialText();
            }
        });
    }

    mergeOptions(defaultOptions, options) {
        return { ...defaultOptions, ...options };
    }

    toggleInitialText() {
        const has_filters = this.table.querySelectorAll('tbody tr').length
        const initial = document.getElementById('dynamic-criteria-initial');
        if (initial) {
            initial.style.display = has_filters ? 'none' : 'block';
        }
    }

    addEventListeners() {
        OpenEyes.UI.DOM.addEventListener( this.table, 'click', 'i.remove-circle', (event) => {
            const icon = event.target;
            icon.closest('tr').remove();

            this.toggleInitialText();
        });

        const institution_dropdown = document.getElementById('filters_institution_id');
        if (institution_dropdown) {
            OpenEyes.UI.DOM.addEventListener(institution_dropdown, 'change', null, function (e) {
                const form = institution_dropdown.closest('form');
                const hidden = OpenEyes.UI.DOM.createElement('input', {
                    "type": "hidden",
                    "name": "clear_filters",
                    "value": 1
                });
                form.appendChild(hidden);
                form.submit();
            });
        }
    }
}
