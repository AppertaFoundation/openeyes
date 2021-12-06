var OpenEyes = OpenEyes || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {
    function formatPeriod(periodNames, period) {
        if (typeof period === 'string') {
            return periodNames.get(period);
        } else {
            return {period: `${period.from} -- ${period.to}`};
        }
    }

    function formatOptional(idMappings, type, items) {
        const itemNames = idMappings.get(type).items;

        if (itemNames.constructor === Map) {
            if (typeof items === 'object') {
                return items.map((id) => itemNames.get(id)).join(', ');
            } else {
                return itemNames.get(items);
            }
        } else {
            if (typeof items === 'object') {
                return items.map((id) => itemNames[id]).join(', ');
            } else {
                return itemNames[items];
            }
        }
    }

    function makeListsTemplateData(listNames, lists) {
        if (typeof lists === 'string') {
            lists = [lists];
        }

        return lists.map(function (list) {
            return {title: listNames.get(list)};
        });
    }

    function makeOptionalFiltersTemplateData(idMappings, optional) {
        if (optional.size === 0) {
            return 'All';
        } else {
            let labels = [];

            for ([type, items] of optional) {
                labels.push(formatOptional(idMappings, type, items));
            }

            return labels.join(', ');
        }
    }

    function WorklistFilterPanel(controller, panelElementSelector, saveFilterPopupSelector,
                                 saveFilterPopupButtonSelector) {
        this.controller = controller;
        this.panel = $(panelElementSelector);

        this.adder = null; // Filled in setupFilterseAdder via setupPanel

        this.saveFilterPopup = saveFilterPopupSelector !== ''
            ? $(saveFilterPopupSelector)
            : null;

        this.saveFilterPopupButton = this.saveFilterPopup && saveFilterPopupButtonSelector !== null
            ? $(saveFilterPopupButtonSelector)
            : null;
    }

    WorklistFilterPanel.prototype.getSitesAndContexts = function () {
        const sites = [];
        const contexts = [];

        this.panel.find('.js-worklists-sites option').each(function () {
            const site = $(this);

            sites.push([site.val(), site.text().trim()]);
        });

        this.panel.find('.js-worklists-contexts option').each(function () {
            const context = $(this);

            contexts.push([context.val(), context.text().trim()]);
        });

        const results = {
            selectedSite: this.panel.find('.js-worklists-sites option:selected').val(),
            sites: new Map(sites),
            contexts: new Map(contexts)
        };

        return results;
    }

    WorklistFilterPanel.prototype.setupPanel = function (idMappings) {
        const view = this;
        const controller = this.controller;

        this.panel.find('.worklist-mode button').click(function () {
            const previous = view.panel.find('.worklist-mode button.selected');
            const next = $(this);

            const previousName = previous.data('subpanel');
            previous.removeClass('selected');
            $(`.js-worklist-mode-panel[data-subpanel="${previousName}"]`).hide();

            const nextName = next.data('subpanel');
            next.addClass('selected');
            $(`.js-worklist-mode-panel[data-subpanel="${nextName}"]`).show();
        });

        this.panel.find('.js-worklists-sites').change(function () {
            controller.site = $(this).find('option:selected').val();
        });

        this.panel.find('.js-worklists-contexts').change(function () {
            controller.context = $(this).find('option:selected').val();
        });

        this.panel.find('.js-apply-filter-btn').click(function () {
            controller.applyFilterWhenAltered();
        });

        this.setupDateControls();
        this.setupListViewControls(idMappings.worklists);
        this.setupFiltersAdder(idMappings);

        this.panel.find('.js-combine-lists-option').change(function () {
            controller.combined = $(this).prop('checked');
        });

        if (this.saveFilterPopupButton) {
            const saveFilterPopup = this.saveFilterPopup;

            this.saveFilterPopupButton.click(function () {
                saveFilterPopup.show();
            });

            saveFilterPopup.find('.js-close-popup-btn').click(function () {
                saveFilterPopup.hide();
            });

            saveFilterPopup.find('.js-save-filter-btn').click(function () {
                controller.saveFilter(saveFilterPopup.find('.js-filter-save-name').val());

                saveFilterPopup.hide();
            });
        }
    }

    WorklistFilterPanel.prototype.setupDateControls = function () {
        const view = this;
        const controller = this.controller;

        this.panel.find('.js-quick-date').change(function () {
            controller.period = view.panel.find('.js-quick-date input:checked').val();
        });

        const fromInput = this.panel.find('.js-filter-date-from');
        const toInput = this.panel.find('.js-filter-date-to');

        pickmeup(fromInput.selector, {
            format: 'Y-m-d',
            hide_on_select: true,
            date: fromInput.val().trim(),
            default_date: false,
        });

        pickmeup(toInput.selector, {
            format: 'Y-m-d',
            hide_on_select: true,
            date: toInput.val().trim(),
            default_date: false,
        });

        const setDateRange = function () {
            controller.period = {
                from: fromInput.val(),
                to: toInput.val(),
            };
        };

        fromInput.off('focusout').on('focusout', setDateRange);
        fromInput.off('pickmeup-change').on('pickmeup-change', setDateRange);

        toInput.off('focusout').on('focusout', setDateRange);
        toInput.off('pickmeup-change').on('pickmeup-change', setDateRange);

        // TODO Sort out the z-index values for the entire panel
        $(fromInput.get(0).__pickmeup.element).css('z-index', 100);
        $(toInput.get(0).__pickmeup.element).css('z-index', 100);
    }

    WorklistFilterPanel.prototype.setupListViewControls = function (idMappings) {
        const controller = this.controller;

        const template = $('#js-worklist-filter-panel-template-worklist-entry').text();
        const into = this.panel.find('.js-worklist-lists-view .js-list-set');
        const allButton = this.panel.find('.js-worklist-lists-view button.js-all-lists-btn');

        allButton.text(idMappings.get('all'));
        allButton.click(function () {
            into.find('input:checked').prop('checked', false);
            allButton.addClass('selected');

            controller.setShownLists('all');
        });

        for ([id, title] of idMappings) {
            if (id !== 'all') {
                into.append(Mustache.render(template, {id: id, title: title}));
            }
        }

        into.change(function () {
            const shownLists = [];

            into.find('input:checked').each(function () {
                shownLists.push($(this).val());
            })

            allButton.removeClass('selected');

            controller.setShownLists(shownLists);
        });
    }

    WorklistFilterPanel.prototype.setupFiltersAdder = function (idMappings) {
        const view = this;
        const controller = this.controller;

        const dialogOptions = [
            {id: 'lists', label: 'Lists'},
            {id: 'sortBy', label: 'Sort by'},
        ];

        const lists = [];

        for ([id, title] of idMappings.worklists.entries()) {
            lists.push({id: id, label: title});
        }

        const listsItems = new OpenEyes.UI.AdderDialog.ItemSet(
            lists,
            {name: 'lists', id: 'js-wfp-lists', multiSelect: true});

        const sortByItems = new OpenEyes.UI.AdderDialog.ItemSet(
            idMappings.sortBy.map(function (label, index) {
                return {id: index, label: label}
            }),
            {name: 'sortBy', id: 'js-wfp-sortBy', multiSelect: false});

        const optionalItems = [];

        for ([name, data] of idMappings.optional) {
            dialogOptions.push({id: name, label: data.header});

            let items = [];

            if (data.items.constructor === Map) {
                for ([id, label] of data.items) {
                    items.push({id: id, label: label});
                }
            } else {
                items = data.items.map(function (label, index) {
                    return {id: index, label: label}
                });
            }

            const newItemSet = new OpenEyes.UI.AdderDialog.ItemSet(
                items,
                {
                    name: name,
                    id: `js-wfp-${name}`,
                    multiSelect: data.multiSelect
                }
            );

            optionalItems.push(newItemSet);
        }

        const dialogItems = new OpenEyes.UI.AdderDialog.ItemSet(
            dialogOptions,
            {id: 'js-wfp-dialog', multiSelect: false});

        this.adder = new OpenEyes.UI.AdderDialog({
            openButton: this.panel.find('.search-filters .js-add-select-btn'),
            itemSets: [dialogItems, listsItems, sortByItems, ...optionalItems],

            filterListId: 'js-wfp-dialog',
            listFilter: true,

            resetSelectionToDefaultOnReturn: false,
            deselectOnReturn: false,

            // What happens in onSelect should be part of the adder itself, or something which inherits from it in the future
            onSelect: function (e) {
                const selected = $(e.target)
                const item = selected.is('span') ? selected.closest('li') : selected;

                const parentId = item.parent().data('id');

                if (parentId === 'js-wfp-dialog') {
                    const filterListId = item.data('id');

                    view.adder.$tr.find('td:not([data-adder-id="js-wfp-dialog"])').hide();

                    view.adder.$tr.find(`td[data-adder-id="js-wfp-${filterListId}"]`).show();
                } else if (parentId === 'js-wfp-lists') {
                    if (item.data('id') === 'all') {
                        $('#js-wfp-lists li:not([data-id="all"])').removeClass('selected');
                    } else {
                        $('#js-wfp-lists li[data-id="all"]').removeClass('selected');
                    }
                }
            },

            onReturn: function (adderDialog, selectedItems) {
                let groupedItems = new Map();
                let lists = [];

                for (item of selectedItems) {
                    const into = item.itemSet.options.name;
                    const multipleAllowed = item.itemSet.options.multiSelect || false;

                    if (into === 'lists') {
                        if (item.id === 'all') {
                            lists = 'all';
                        } else if (lists !== 'all') {
                            lists.push(item.id);
                        }
                    } else if (into === 'sortBy') {
                        controller.sortBy = item.id;
                    } else if (into !== undefined) {
                        if (multipleAllowed) {
                            if (groupedItems.has(into)) {
                                groupedItems.get(into).push(item.id);
                            } else {
                                groupedItems.set(into, [item.id]);
                            }
                        } else {
                            groupedItems.set(into, item.id);
                        }
                    }
                }

                controller.worklists = lists;
                controller.optional = groupedItems;

                return true;
            }
        });

        // Ensure default values for compulsary filters are selected
        this.adder.$tr.find('#js-wfp-lists li[data-id="all"]').addClass('selected');
        this.adder.$tr.find('#js-wfp-sortBy li[data-id="0"]').addClass('selected');

        // This should be part of the adder itself, or something which inherits from it in the future
        this.adder.$tr.find('td:not([data-adder-id="js-wfp-dialog"])').hide();
    }

    /*
     * Entire panel
     */
    WorklistFilterPanel.prototype.updatePanel = function (idMappings, filter) {
        this.setSelectedSite(filter.site);
        this.setSelectedContext(filter.context);

        this.setDatePeriod(idMappings.periods, filter.period);
        this.setFiltersTableRows(idMappings, filter);
        this.setCombinedStatus(filter.combined);
    }

    WorklistFilterPanel.prototype.setSelectedSite = function (site) {
        this.panel.find('.js-worklists-sites option:selected').prop('selected', false);

        const selected = this.panel.find(`.js-worklists-sites option[value=${site}]`);

        selected.prop('selected', true);

        if (this.saveFilterPopup) {
            this.saveFilterPopup.find('.js-filter-site').text(selected.text().trim());
        }
    }

    WorklistFilterPanel.prototype.setSelectedContext = function (context) {
        this.panel.find('.js-worklists-contexts option:selected').prop('selected', false);
        const selected = this.panel.find(`.js-worklists-contexts option[value=${context}]`);

        selected.prop('selected', true);

        if (this.saveFilterPopup) {
            this.saveFilterPopup.find('.js-filter-context').text(selected.text().trim());
        }
    }

    /*
     * Lists tab
     */
    WorklistFilterPanel.prototype.setDatePeriod = function (idMappings, newPeriod) {
        if (typeof newPeriod === 'string') {
            // Relative period
            this.panel.find('.js-quick-date input:checked').prop('checked', false);
            this.panel.find(`.js-quick-date input[value="${newPeriod}"]`).prop('checked', true);
        } else {
            // Absolute period
            this.panel.find('.js-quick-date input:checked').prop('checked', false);
        }

        const range = OpenEyes.WorklistFilter.getDateRange(newPeriod);

        this.panel.find('.js-filter-date-from').val(range.from);
        this.panel.find('.js-filter-date-to').val(range.to);

        if (this.saveFilterPopup) {
            const formatted = formatPeriod(idMappings, newPeriod);
            const datesLabel = typeof formatted.includes === 'string' && formatted.includes !== ''
                ? `${formatted.title} (${formatted.includes})`
                : formatted.title;

            this.saveFilterPopup.find('.js-filter-dates').text(datesLabel);
        }
    }

    WorklistFilterPanel.prototype.setStateForListsTab = function (idMappings, filter) {
        this.setSelectedSite(filter.site);
        this.setSelectedContext(filter.context);

        this.setFiltersTableRows(idMappings, filter);
    }

    WorklistFilterPanel.prototype.setListsRow = function (idMappings, lists) {
        this.adder.$tr.find('#js-wfp-lists li.selected').removeClass('selected');

        let listsLabel = '';

        if (typeof lists === 'string') {
            listsLabel = idMappings.get(lists);

            this.adder.$tr.find(`#js-wfp-lists li[data-id="${lists}"]`).addClass('selected');
        } else {
            const view = this;

            listsLabel = lists.map(function (id) {
                view.adder.$tr.find(`#js-wfp-lists li[data-id="${id}"]`).addClass('selected');

                return idMappings.get(id);
            }).join(', ');
        }

        this.panel.find('.js-lists-value').text(listsLabel);

        if (this.saveFilterPopup) {
            this.saveFilterPopup.find('.js-filter-lists').text(listsLabel);
        }
    }

    WorklistFilterPanel.prototype.setSortByRow = function (idMappings, sortBy) {
        this.panel.find('.js-sort-by-value').text(idMappings[sortBy]);

        this.adder.$tr.find('#js-wfp-sortBy li.selected').removeClass('selected');
        this.adder.$tr.find(`#js-wfp-sortBy li[data-id="${sortBy}"]`).addClass('selected');
    }

    WorklistFilterPanel.prototype.setOptionalFiltersRows = function (idMappings, optional) {
        const view = this;
        const controller = this.controller;

        const table = this.panel.find('.js-filters-table');
        const template = $('#js-worklist-filter-panel-template-removable-filter').text();

        let optionalLabels = [];

        table.find('tr.js-removeable-filter').remove();

        for ([type, items] of optional) {
            const optionalInfo = idMappings.get(type);

            this.adder.$tr.find(`#js-wfp-${type} li.selected`).removeClass('selected');

            let label = formatOptional(idMappings, type, items);

            if (typeof items === 'object') {
                for (id of items) {
                    view.adder.$tr.find(`#js-wfp-${type} li[data-id="${id}"]`).addClass('selected');
                }
            } else {
                view.adder.$tr.find(`#js-wfp-${type} li[data-id="${items}"]`).addClass('selected');
            }

            optionalLabels.push(label);

            const data = {
                type: type,
                name: optionalInfo.header,
                value: label
            };

            table.append(Mustache.render(template, data));
        }

        table.find('tr.js-removeable-filter i.js-remove-filter').click(function () {
            const row = $(this).parents('tr.js-removeable-filter');
            const type = row.data('filter-type');

            view.adder.$tr.find(`#js-wfp-${type} li.selected`).removeClass('selected');

            controller.removeOptionalFilter(type);
            row.remove();
        });

        if (this.saveFilterPopup) {
            const optionalLabel = optionalLabels.length === 0 ? 'All' : optionalLabels.join(', ');

            this.saveFilterPopup.find('.js-filter-optional').text(optionalLabel);
        }
    }

    WorklistFilterPanel.prototype.setFiltersTableRows = function (idMappings, filter) {
        this.setListsRow(idMappings.worklists, filter.worklistsArray);
        this.setSortByRow(idMappings.sortBy, filter.sortBy);
        this.setOptionalFiltersRows(idMappings.optional, filter.optional);
    }

    WorklistFilterPanel.prototype.setCombinedStatus = function (isCombined) {
        this.panel.find('.js-combine-lists-option').prop('checked', isCombined);
    }

    /*
     * Saved (starred) and recent filters tabs
     */
    WorklistFilterPanel.prototype.makeFilterEntryData = function (idMappings, filter, index) {
        let formattedPeriod = formatPeriod(idMappings.periods, filter.period);

        return {
            index: index,
            site: idMappings.sites.get(filter.site),
            context: idMappings.contexts.get(filter.context),
            period: formattedPeriod.title,
            periodIncludes: formattedPeriod.includes,
            optional: makeOptionalFiltersTemplateData(idMappings.optional, filter.optional),
            lists: makeListsTemplateData(idMappings.worklists, filter.worklistsArray),
        }
    }

    WorklistFilterPanel.prototype.setSavedTabList = function (idMappings, savedFilters) {
        const controller = this.controller;
        const parent = this.panel.find('.js-worklist-mode-panel[data-subpanel="starred"]');
        const putAfter = parent.find('h3');
        const template = $('#js-worklist-filter-panel-template-named-filter').text();

        parent.find('div.fav').remove();

        for (index in savedFilters) {
            const data = this.makeFilterEntryData(idMappings, savedFilters[index], index);

            data.name = savedFilters[index].name;

            putAfter.after(Mustache.render(template, data));
        }

        parent.find('div.fav .details').click(function () {
            controller.loadSavedFilter($(this).data('index'));
        });

        parent.find('div.fav .expand-fav').click(function () {
            const icon = $(this).find('.oe-i');

            $(this).parent().find('.js-full-details').toggle();

            icon.toggleClass('expand');
            icon.toggleClass('collapse');
        });

        parent.find('div.fav .remove-fav').click(function () {
            controller.deleteNamed($(this).data('index'));
        });
    }

    WorklistFilterPanel.prototype.setRecentTabList = function (idMappings, recentFilters) {
        const controller = this.controller;
        const parent = this.panel.find('.js-worklist-mode-panel[data-subpanel="recent"]');
        const putAfter = parent.find('h3');
        const template = $('#js-worklist-filter-panel-template-recent-filter').text();

        parent.find('div.fav').empty();

        for (index in recentFilters) {
            const data = this.makeFilterEntryData(idMappings, recentFilters[index], index);

            putAfter.after(Mustache.render(template, data));
        }

        parent.find('div.fav').click(function () {
            controller.loadRecentFilter($(this).data('index'));
        });
    }

    WorklistFilterPanel.prototype.constructor = WorklistFilterPanel;

    exports.WorklistFilterPanel = WorklistFilterPanel;
}(OpenEyes.UI));
