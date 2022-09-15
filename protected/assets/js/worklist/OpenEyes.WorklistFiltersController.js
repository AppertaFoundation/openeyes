var OpenEyes = OpenEyes || {};

(function (exports) {
    const sortByOptions = [
        'Time',
        'Clinic (A-Z)', 'Clinic (Z-A)',
        'Wait (longest)', 'Wait (shortest)',
        'Priority',
        'Duration'
    ];

    const periodOptions = [
        ['yesterday', {title: 'Yesterday', includes: ''}],
        ['today', {title: 'Today', includes: ''}],
        ['tomorrow', {title: 'Tomorrow', includes: ''}],
        ['this-week', {title: 'This week', includes: 'Mon - Sun'}],
        ['next-week', {title: 'Next week', includes: ''}],
        ['next-7-days', {title: '+ 7 days', includes: 'includes Today'}],
    ];

    // When items is an array, a zero-based index will be returned as the id in the filter data
    // If a Map is stored in their instead, the key will be used as the id instead, with the value as the label
    const optionalFiltersOptions = [
        ['ageRanges', {
            header: 'Age range',
            multiSelect: false,
            items: [
                '0 - 16',
                '16+'
            ]
        }],

        ['redFlags', {
            header: 'Red flags',
            multiSelect: false,
            items: [
                'With red flags',
                'No red flags',
            ]
        }],

        ['priorityOrRisk', {
            header: 'Priority / Risk',
            multiSelect: true,
            items: [
                'Immediate / High',
                'Very Urgent / High',
                'Urgent / Medium',
                'Standard / Low',
                'Low / Low',
            ]
        }],

        ['pathwayStates', {
            header: 'Pathway state',
            multiSelect: true,
            items: [
                'Scheduled',
                'Arrived',
                'Complete',
            ]
        }],

        ['durations', {
            header: 'Duration',
            multiSelect: true,
            items: [
                '0 - 1 hrs',
                '1 - 2 hrs',
                '3 - 4 hrs',
                '4 - 5 hrs',
                '6 - 7 hrs',
                '7 - 8 hrs',
            ]
        }]
    ];

    const default_options = {
        worklistFilterPanelSelector: '#js-worklists-filter-panel',
        saveFilterPopupSelector: '',
        saveFilterPopupButtonSelector: '',

        // No button selector needed for sort by popup, it's part of the quick filter panel
        quickFilterPanelSelector: '',
        sortByPopupSelector: '',

        maxRecentFilters: 5,

        // Defaults for worklist filters
        selectedSite: 1,
        sites: [],
        contexts: [],
        worklists: [],
        filteredWorklists: [],

        users: [],

        initial_selected_filter_type: null,
        initial_selected_filter_id: null,
        initial_selected_quick_filter: null,
        initial_is_combined: false,

        // Defaults for id -> property mappings for filters
        sortByOptions: sortByOptions,
        periodOptions: periodOptions,
        optionalFiltersOptions: optionalFiltersOptions,

        // Callback functions
        // Since this controller will mainly be utilised from views/worklist/index.php,
        // these callbacks are provided to connect the controller to the existing code
        // for updating the worklists in the main view.
        applyFilter: null, // Called to apply a filter, passing the controller as the argument
        changeShownLists: null, // Called to change which lists are shown in the main view
        removeRow: null, // Called to remove a single worklist patient row (client side only)
    };

    function WorklistFiltersController(options = {}) {
        const controller = this;
        this.options = $.extend(true, {}, default_options, options);

        const panelViewSelector = this.options.worklistFilterPanelSelector;
        this.panelView = new OpenEyes.UI.WorklistFilterPanel(this, panelViewSelector,
            this.options.saveFilterPopupSelector,
            this.options.saveFilterPopupButtonSelector);

        const quickViewSelector = this.options.quickFilterPanelSelector;
        this.quickView = quickViewSelector !== '' ?
            new OpenEyes.UI.WorklistQuickFilterPanel(this, quickViewSelector, this.options.sortByPopupSelector) :
            null;

        this.establishMappings();

        this.filter = new OpenEyes.WorklistFilter();
        this.filter.site = this.options.selectedSite;
        this.filterIsAltered = true;

        this.quickProperties = {
            filter: 'all',
            patientName: '',
        };

        this.savedFilters = [];
        this.recentFilters = [];

        this.shownLists = 'all';
        this.activeFilterIsCombined = this.options.initial_is_combined;

        this.panelView.setupPanel(this.mappings);

        if (this.mappings.sites.size === 0 && this.mappings.contexts.size === 0) {
            const results = this.panelView.getSitesAndContexts();

            this.filter.site = results.selectedSite;
            this.mappings.sites = results.sites;
            this.mappings.contexts = results.contexts;
        }

        this.panelView.updatePanel(this.mappings, this.filter);

        this.retrieveFilters(function() {
            if (controller.options.initial_selected_filter_type !== null && controller.options.initial_selected_filter_id !== null) {
                const id = controller.options.initial_selected_filter_id;

                if (controller.options.initial_selected_filter_type === 'Recent') {
                    const index = controller.recentFilters.findIndex((filter) => parseInt(filter.id, 10) === id);

                    if (index !== -1) {
                        controller.loadRecentFilter(index, true);
                    }
                } else if (controller.options.initial_selected_filter_type === 'Saved') {
                    const index = controller.savedFilters.findIndex((filter) => parseInt(filter.id, 10) === id);

                    if (index !== -1) {
                        controller.loadSavedFilter(index, true);
                    }
                }

                controller.activeFilterIsCombined = controller.filter.combined;
            }

            if (controller.options.initial_selected_quick_filter !== null && controller.quickView !== null) {
                const initial_quick = JSON.parse(controller.options.initial_selected_quick_filter);

                controller.quickView.setQuickSelection(initial_quick.filter);
                controller.quickView.setQuickName(initial_quick.patientName);

                if (initial_quick.sortBy) {
                    controller.quickView.setSortBy(controller.mappings.sortBy, initial_quick.sortBy);
                }
            }
        });
    }

    WorklistFiltersController.prototype.constructor = WorklistFiltersController;

    WorklistFiltersController.prototype.establishMappings = function () {
        this.mappings = {
            sites: new Map(this.options.sites),
            contexts: new Map(this.options.contexts),
            periods: new Map(this.options.periodOptions),
        };

        this.mappings.worklists = new Map();
        this.mappings.filteredWorklists = new Map();
        this.mappings.users = new Map();
        this.mappings.steps = new Map();

        this.mappings.worklists.set('all', `All (${this.options.worklists.length})`);
        this.mappings.filteredWorklists.set('all', `All (${this.options.filteredWorklists.length})`);

        const filteredIds = new Set(this.options.filteredWorklists);

        for (let worklistData of this.options.worklists) {
            this.mappings.worklists.set(worklistData.id, worklistData.title);

            if (filteredIds.has(worklistData.id)) {
                this.mappings.filteredWorklists.set(worklistData.id, worklistData.title);
            }
        }

        for (let userData of this.options.users) {
            this.mappings.users.set(parseInt(userData.id), userData.label);
        }

        for (let stepData of this.options.steps) {
            this.mappings.steps.set(parseInt(stepData.id), stepData.label);
        }

        this.mappings.sortBy = this.options.sortByOptions;

        // Prepend the optional choices with 'Assigned To', 'Steps' and 'Todo' choices
        // Done at this point because users for Assigned To and path steps for Steps and Todo
        // are passed in as separate options
        const assignedTo = ['assignedTo', {
            header: 'Assigned To',
            multiSelect: false,
            items: this.mappings.users
        }];

        const steps = ['steps', {
            header: 'Steps',
            multiSelect: true,
            items: this.mappings.steps
        }];

        const todo = ['todo', {
            header: 'To-do',
            multiSelect: true,
            items: this.mappings.steps
        }];

        const optional = [assignedTo, steps, todo];

        this.mappings.optional = new Map(optional.concat(this.options.optionalFiltersOptions));
    };

    WorklistFiltersController.prototype.updateActiveQuickFilter = function (success) {
        this.setSessionFilter('Quick', JSON.stringify(this.quickProperties), success);
    };

    // Retrieval and storage of filters
    WorklistFiltersController.prototype.retrieveFilters = function (success) {
        const controller = this;

        $('.spinner').show();

        $.ajax({
            url: '/worklist/retrieveFilters',
            type: 'GET',
            success: function(resp) {
                $('.spinner').hide();

                controller.savedFilters = resp.saved.map(OpenEyes.WorklistFilter.fromJSON);
                controller.recentFilters = resp.recent.map(OpenEyes.WorklistFilter.fromJSON);

                controller.maxRecentFilters = resp.max_recents ?
                    resp.max_recents :
                    controller.options.maxRecentFilters;

                controller.panelView.setSavedTabList(controller.mappings, controller.savedFilters);
                controller.panelView.setRecentTabList(controller.mappings, controller.recentFilters);

                success();
            },
            error: function() {
                $('.spinner').hide();

                new OpenEyes.UI.Dialog.Alert({
                    content: "Unable to retrieve the filters.\n\nPlease reload the page to try again or contact support."
                }).open();
            }
        });
    };

    WorklistFiltersController.prototype.storeFilter = function (isRecent, onSuccess) {
        $('.spinner').show();

        $.ajax({
            url: '/worklist/storeFilter',
            type: 'POST',
            data: {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                is_recent: isRecent,
                id: this.filter.id ? this.filter.id : '',
                name: this.filter.name ? this.filter.name : '',
                filter: this.filter.asJSON()
            },
            success: function() {
                $('.spinner').hide();

                if (onSuccess) {
                    onSuccess();
                }
            },
            error: function() {
                $('.spinner').hide();

                new OpenEyes.UI.Dialog.Alert({
                    content: "Unable to save the new filter.\n\nPlease try again or contact support."
                }).open();
            }
        });
    };

    WorklistFiltersController.prototype.applyFilterWhenAltered = function () {
        if (this.filterIsAltered) {
            this.pushRecentFilter(this.applyFilter.bind(this));
        }
    };

    WorklistFiltersController.prototype.applyFilter = function () {
        this.activeFilterIsCombined = this.filter.combined;

        if (this.options.applyFilter) {
            this.options.applyFilter(this);
        }
    };

    // Handling of saved (starred) and recent filters
    WorklistFiltersController.prototype.saveFilter = function (name) {
        this.filter.name = name;
        this.filterIsAltered = false;

        this.savedFilters.push(this.filter.clone());
        this.storeFilter(false);

        this.panelView.setSavedTabList(this.mappings, this.savedFilters);
    };

    WorklistFiltersController.prototype.pushRecentFilter = function (onSuccess) {
        const existing = this.recentFilters.findIndex(this.filter.compare.bind(this.filter));

        if (existing !== -1) {
            this.loadRecentFilter(existing, false);
        } else {
            if (this.recentFilters.length >= this.maxRecentFilters) {
                this.recentFilters.shift();
            }

            this.filterIsAltered = false;

            this.recentFilters.push(this.filter.clone());
            this.storeFilter(true, onSuccess);

            this.panelView.setRecentTabList(this.mappings, this.recentFilters);
        }
    };

    WorklistFiltersController.prototype.loadSavedFilter = function (index, onlyUpdateUI) {
        this.filter = this.savedFilters[index].clone();
        this.filterIsAltered = false;

        if (!onlyUpdateUI) {
            const controller = this;

            this.setSessionFilter('Saved', this.filter.id, function() {
                if (controller.quickProperties.sortBy !== null) {
                    controller.quickView.setSortBy(controller.mappings.sortBy, controller.filter.sortBy);
                    controller.quickSortBy = null; // Remove the quick filter sort by, which will apply the filter too
                } else {
                    controller.applyFilter();
                }
            });
        }

        this.panelView.updatePanel(this.mappings, this.filter);
    };

    WorklistFiltersController.prototype.loadRecentFilter = function (index, onlyUpdateUI) {
        this.filter = this.recentFilters[index].clone();
        this.filterIsAltered = false;

        if (!onlyUpdateUI) {
            const controller = this;

            this.setSessionFilter('Recent', this.filter.id, function() {
                if (controller.quickProperties.sortBy !== null) {
                    controller.quickView.setSortBy(controller.mappings.sortBy, controller.filter.sortBy);
                    controller.quickSortBy = null; // Remove the quick filter sort by, which will apply the filter too
                } else {
                    controller.applyFilter();
                }
            });
        }

        this.panelView.updatePanel(this.mappings, this.filter);
    };

    WorklistFiltersController.prototype.setSessionFilter = function (type, value, success) {
        $.ajax({
            url: '/worklist/setChosenFilter',
            type: 'POST',
            data: {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                filter_type: type,
                filter_value: value
            },
            success: function() {
                success();
            }
        });
    };

    // Panel wide properties (site & context)
    Object.defineProperty(WorklistFiltersController.prototype, 'site', {
        set: function (newSite) {
            this.filter.site = newSite;
            this.filterIsAltered = true;

            this.panelView.setSelectedSite(newSite);
        }
    });

    Object.defineProperty(WorklistFiltersController.prototype, 'context', {
        set: function (newContext) {
            this.filter.context = newContext;
            this.filterIsAltered = true;

            this.panelView.setSelectedContext(newContext);
        }
    });

    // Lists tab properties
    Object.defineProperty(WorklistFiltersController.prototype, 'period', {
        set: function (newPeriod) {
            this.filter.period = newPeriod;
            this.filterIsAltered = true;

            this.panelView.setDatePeriod(this.mappings.periods, newPeriod);
        }
    });

    Object.defineProperty(WorklistFiltersController.prototype, 'worklists', {
        set: function (newWorklists) {
            this.filter.worklistsArray = newWorklists;
            this.filterIsAltered = true;

            this.panelView.setListsRow(this.mappings.worklists, newWorklists);
        }
    });

    Object.defineProperty(WorklistFiltersController.prototype, 'sortBy', {
        set: function (newSortBy) {
            this.filter.sortBy = newSortBy;
            this.filterIsAltered = true;

            this.panelView.setSortByRow(this.mappings.sortBy, newSortBy);

            if (this.quickView) {
                this.quickView.setSortBy(this.mappings.sortBy, newSortBy);
            }
        }
    });

    Object.defineProperty(WorklistFiltersController.prototype, 'optional', {
        set: function (newOptional) {
            this.filter.optional = newOptional;
            this.filterIsAltered = true;

            this.panelView.setOptionalFiltersRows(this.mappings.optional, newOptional);
        }
    });

    Object.defineProperty(WorklistFiltersController.prototype, 'combined', {
        set: function (newCombined) {
            this.filter.combined = newCombined;
            this.filterIsAltered = true;

            this.panelView.setCombinedStatus(newCombined);
        }
    });

    // Quick filter
    Object.defineProperty(WorklistFiltersController.prototype, 'quick', {
        set: function (newQuick) {
            this.quickProperties.filter = newQuick;

            if (this.quickView) {
                this.quickView.setQuickSelection(newQuick);
            }

            this.updateActiveQuickFilter(this.applyFilter.bind(this));
        }
    });

    // Quick filter (Name component)
    Object.defineProperty(WorklistFiltersController.prototype, 'quickName', {
        set: function (newQuickName) {
            this.quickProperties.patientName = newQuickName;

            if (this.quickView) {
                this.quickView.setQuickName(newQuickName);
            }

            this.updateActiveQuickFilter(this.applyFilter.bind(this));
        }
    });

    // Quick filter (Sort by component)
    Object.defineProperty(WorklistFiltersController.prototype, 'quickSortBy', {
        set: function (newSortBy) {
            this.quickProperties.sortBy = newSortBy;

            if (this.quickView && newSortBy !== null) {
                this.quickView.setSortBy(this.mappings.sortBy, newSortBy);
            }

            this.updateActiveQuickFilter(this.applyFilter.bind(this));
        }
    });

    // Convenience methods

    // Convenience method for optional filter removal
    WorklistFiltersController.prototype.removeOptionalFilter = function (filterType) {
        if (this.filter.optional.has(filterType)) {
            this.filterIsAltered = true;
            this.filter.optional.delete(filterType);
        }
    };

    WorklistFiltersController.prototype.setAvailableLists = function (lists, filteredIdsList) {
        const filteredIds = new Set(filteredIdsList);
        const replacementMappings = new Map();
        const replacementFilteredMappings = new Map();
        const newIds = [];
        const newFilteredIds = [];

        let order = 1;
        let filteredOrder = 1;

        for (const worklist of lists) {
            replacementMappings.set(worklist.id, worklist.title);

            if (!this.mappings.worklists.has(worklist.id)) {
                newIds.push([worklist.id, order]);
            }

            order = order + 1;

            if (filteredIds.has(worklist.id)) {
                replacementFilteredMappings.set(worklist.id, worklist.title);

                if (!this.mappings.filteredWorklists.has(worklist.id)) {
                    newFilteredIds.push([worklist.id, filteredOrder]);
                }

                filteredOrder = filteredOrder + 1;
            }
        }

        replacementMappings.set('all', `All (${lists.length})`);
        replacementFilteredMappings.set('all', `All (${filteredIdsList.length})`);

        this.mappings.worklists = replacementMappings;
        this.mappings.filteredWorklists = replacementFilteredMappings;

        this.panelView.updateAvailableWorklists(this.mappings, newIds, newFilteredIds);
        this.panelView.setListsRow(this.mappings.worklists, this.filter.worklistsArray);
    };

    // Convenience method for lists view, which shows/hides lists when they are uncombined
    WorklistFiltersController.prototype.setShownLists = function (lists) {
        this.shownLists = lists;

        if (this.options.changeShownLists) {
            this.options.changeShownLists(this.activeFilterIsCombined ? 'all' : lists);
        }
    };

    // Convenience method for views/worklist/index.php, which shows/hides lists when they are uncombined
    // Called after data has been refreshed
    WorklistFiltersController.prototype.resetShownLists = function () {
        if (this.options.changeShownLists) {
            this.options.changeShownLists(this.activeFilterIsCombined ? 'all' : this.shownLists);
        }
    };

    // Update Quick filter panel details
    WorklistFiltersController.prototype.updateCounts = function (quickDetails, waitingForDetails, assignedToDetails) {
        if (this.quickView) {
            const data = {
                ...quickDetails,
                waitingFor: waitingForDetails,
                assignedTo: assignedToDetails,
            };

            this.quickView.setListsAndCounts(data);
        }
    };

    // Update Quick filter panel details
    WorklistFiltersController.prototype.updateCountsOnChange = function (changeType, details) {
        switch (changeType) {
        case 'change-assigned-to':
            if (this.quickView) {
                if (details.oldId !== undefined && details.oldId !== '') {
                    this.quickView.changeAssignedTo(this.mappings.users, parseInt(details.oldId), -1);
                }

                this.quickView.changeAssignedTo(this.mappings.users, parseInt(details.newId), 1);
            }

            if (this.options.removeRow &&
                typeof this.quickProperties.filter !== 'string' &&
                this.quickProperties.filter.type === 'assignedTo' &&
                this.quickProperties.filter.value !== parseInt(details.newId)
            ) {
                this.options.removeRow(details.pathwayId);
            }
            break;

        case 'change-waiting-for':
            // No 'waiting for' status means the row may still be filtered out
            const newId = details.newSteps.length > 0 ? details.newSteps[0] : '';

            if (this.quickView) {
                if (details.oldSteps.length === 0) {
                    if (details.newSteps.length !== 0) {
                        this.quickView.changeWaitingFor(details.newSteps[0], 1);

                        for (let step of details.newSteps) {
                            this.quickView.changeWaitingFor(step, 0);
                        }
                    }
                } else if (details.newSteps.length === 0) {
                    this.quickView.changeWaitingFor(details.oldSteps[0], -1);
                } else {
                    if (details.oldSteps[0] !== details.newSteps[0]) {
                        this.quickView.changeWaitingFor(details.oldSteps[0], -1);
                        this.quickView.changeWaitingFor(details.newSteps[0], 1);
                    }

                    for (let step of details.newSteps) {
                        this.quickView.changeWaitingFor(step, 0);
                    }
                }
            }

            if (this.options.removeRow &&
                typeof this.quickProperties.filter !== 'string' &&
                this.quickProperties.filter.type === 'waitingFor' &&
                this.quickProperties.filter.value !== newId)
            {
                this.options.removeRow(details.pathwayId);
            }
            break;

        case 'change-pathway-status':
            const from = typeof details.oldStatus === 'undefined' ? 'later' : details.oldStatus;
            const to = typeof details.newStatus === 'undefined' ? 'later' : details.newStatus;

            if (this.quickView && from !== to) {
                this.quickView.changeStatusTypeCount(from, -1);
                this.quickView.changeStatusTypeCount(to, 1);
            }
            break;
        }
    };

    exports.WorklistFiltersController = WorklistFiltersController;
}(OpenEyes));
