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

    // When items is an array, a the zero-based index will be returned as the id in the filter data
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

        users: [],
        sites: [],

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
    };

    function WorklistFiltersController(options = {}) {
        this.options = $.extend(true, {}, default_options, options);

        const panelViewSelector = this.options.worklistFilterPanelSelector;
        this.panelView = new OpenEyes.UI.WorklistFilterPanel(this, panelViewSelector,
            this.options.saveFilterPopupSelector,
            this.options.saveFilterPopupButtonSelector);

        const quickViewSelector = this.options.quickFilterPanelSelector;
        this.quickView = quickViewSelector !== ''
            ? new OpenEyes.UI.WorklistQuickFilterPanel(this, quickViewSelector, this.options.sortByPopupSelector)
            : null;

        this.establishMappings();

        this.filter = new OpenEyes.WorklistFilter();
        this.filter.site = this.options.selectedSite;
        this.filterIsAltered = false;

        this.quickProperties = {
            filter: 'all',
            name: '',
        };

        this.savedFilters = [];
        this.recentFilters = [];

        this.shownLists = 'all';

        this.panelView.setupPanel(this.mappings);

        if (this.mappings.sites.size === 0 && this.mappings.contexts.size === 0) {
            const results = this.panelView.getSitesAndContexts();

            this.filter.site = results.selectedSite;
            this.mappings.sites = results.sites;
            this.mappings.contexts = results.contexts;
        }

        this.panelView.updatePanel(this.mappings, this.filter);

        this.updateActiveFilter();

        this.retrieveFilters();
    }

    WorklistFiltersController.prototype.constructor = WorklistFiltersController;

    WorklistFiltersController.prototype.establishMappings = function () {
        this.mappings = {
            sites: new Map(this.options.sites),
            contexts: new Map(this.options.contexts),
            periods: new Map(this.options.periodOptions),
        };

        this.mappings.worklists = new Map();
        this.mappings.users = new Map();
        this.mappings.steps = new Map();

        this.mappings.worklists.set('all', `All (${this.options.worklists.length})`);

        for (worklistData of this.options.worklists) {
            this.mappings.worklists.set(worklistData.id, worklistData.title);
        }

        for (userData of this.options.users) {
            this.mappings.users.set(parseInt(userData.id), userData.label);
        }

        for (stepData of this.options.steps) {
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
    }

    // The active filter, which contains the representation used in filtering worklist requests
    WorklistFiltersController.prototype.updateActiveFilter = function () {
        this.activeFilter = this.filter.clone();

        // Always represent periods as from - to ranges when sending to the server
        this.activeFilter.period = OpenEyes.WorklistFilter.getDateRange(this.activeFilter.period);

        // Currently quick filters are applied server side
        this.activeFilter.quick = this.quickProperties;

        this.activeFilterJSON = this.activeFilter.asJSON();
    }

    WorklistFiltersController.prototype.updateActiveQuickFilter = function (optionalSortBy) {
        // Currently quick filters are applied server side
        this.activeFilter.quick = this.quickProperties;

        // Sort by chosen on the quick filters panel overrides the value set on the rhs panel
        if (optionalSortBy) {
            this.activeFilter.sortBy = optionalSortBy;
        }

        this.activeFilterJSON = this.activeFilter.asJSON();
    }

    WorklistFiltersController.prototype.getFilterJSON = function () {
        return this.activeFilterJSON;
    }

    // Retrieval and storage of filters
    WorklistFiltersController.prototype.retrieveFilters = function () {
        const controller = this;

        // TODO Deal with success and failure
        $.get('/worklist/retrieveFilters',
            {},
            function (resp) {
                if (!resp) {
                    return;
                }

                controller.savedFilters = resp.saved.map(OpenEyes.WorklistFilter.fromJSON);
                controller.recentFilters = resp.recent.map(OpenEyes.WorklistFilter.fromJSON);

                controller.maxRecentFilters = resp.max_recents
                    ? resp.max_recents
                    : controller.options.maxRecentFilters;

                controller.panelView.setSavedTabList(controller.mappings, controller.savedFilters);
                controller.panelView.setRecentTabList(controller.mappings, controller.recentFilters);
            });
    }

    WorklistFiltersController.prototype.storeFilter = function (isRecent) {
        // TODO deal with success and failure
        $.post('/worklist/storeFilter',
            {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                is_recent: isRecent,
                id: this.filter.id ? this.filter.id : '',
                name: this.filter.name ? this.filter.name : '',
                filter: this.filter.asJSON()
            },
            function (resp) {
                if (!resp) {
                    return;
                }
            });
    }

    WorklistFiltersController.prototype.applyFilterWhenAltered = function () {
        if (this.filterIsAltered) {
            this.pushRecentFilter();

            this.applyFilter();
        }
    }

    WorklistFiltersController.prototype.applyFilter = function () {
        this.updateActiveFilter();

        if (this.options.applyFilter) {
            this.options.applyFilter(this);
        }
    }

    // Handling of saved (starred) and recent filters
    WorklistFiltersController.prototype.saveFilter = function (name) {
        this.filter.name = name;
        this.filterIsAltered = false;

        this.savedFilters.push(this.filter.clone());
        this.storeFilter(false);

        this.panelView.setSavedTabList(this.mappings, this.savedFilters);
    }

    WorklistFiltersController.prototype.pushRecentFilter = function () {
        if (this.recentFilters.some(this.filter.compare.bind(this.filter))) {
            return;
        }

        if (this.recentFilters.length >= this.maxRecentFilters) {
            this.recentFilters.shift();
        }

        this.filterIsAltered = false;

        this.recentFilters.push(this.filter.clone());
        this.storeFilter(true);

        this.panelView.setRecentTabList(this.mappings, this.recentFilters);
    }

    WorklistFiltersController.prototype.loadSavedFilter = function (index) {
        this.filter = this.savedFilters[index].clone();
        this.filterIsAltered = false;

        this.applyFilter();

        this.panelView.updatePanel(this.mappings, this.filter);
    }

    WorklistFiltersController.prototype.loadRecentFilter = function (index) {
        this.filter = this.recentFilters[index].clone();
        this.filterIsAltered = false;

        this.applyFilter();

        this.panelView.updatePanel(this.mappings, this.filter);
    }

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

            this.updateActiveQuickFilter();
            this.applyFilter();
        }
    });

    // Quick filter (Name component)
    Object.defineProperty(WorklistFiltersController.prototype, 'quickName', {
        set: function (newQuickName) {
            this.quickProperties.name = newQuickName;

            if (this.quickView) {
                this.quickView.setQuickName(newQuickName);
            }

            this.updateActiveQuickFilter();
            this.applyFilter();
        }
    });

    // Quick filter (Sort by component)
    Object.defineProperty(WorklistFiltersController.prototype, 'quickSortBy', {
        set: function (newSortBy) {
            this.sortBy = newSortBy;

            this.updateActiveQuickFilter(newSortBy);
            this.applyFilter();
        }
    });

    // Convenience methods

    // Convenience method for optional filter removal
    WorklistFiltersController.prototype.removeOptionalFilter = function (filterType) {
        if (this.filter.optional.has(filterType)) {
            this.filterIsAltered = true;
            this.filter.optional.delete(filterType);
        }
    }

    // Convenience method for lists view, which shows/hides lists when they are uncombined
    WorklistFiltersController.prototype.setShownLists = function (lists) {
        this.shownLists = lists;

        if (this.options.changeShownLists) {
            this.options.changeShownLists(this.activeFilter.combined ? 'all' : lists);
        }
    }

    // Convenience method for views/worklist/index.php, which shows/hides lists when they are uncombined
    // Called after data has been refreshed
    WorklistFiltersController.prototype.resetShownLists = function () {
        if (this.options.changeShownLists) {
            this.options.changeShownLists(this.activeFilter.combined ? 'all' : this.shownLists);
        }
    }

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
    }

    exports.WorklistFiltersController = WorklistFiltersController;
}(OpenEyes));
