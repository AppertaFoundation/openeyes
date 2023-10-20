var OpenEyes = OpenEyes || {};

(function (exports) {
    /* Helper functions */
    function decodeWorklists(external)
    {
        if (typeof external === 'string' ||
            (typeof external === 'object' && external.constructor === Set)) {
            return external;
        } else {
            return new Set(external);
        }
    }

    function encodeWorklists(internal)
    {
        if (typeof internal === 'string') {
            return internal;
        } else {
            return [...internal];
        }
    }

    function compareTimePeriods(lhs, rhs)
    {
        if (typeof lhs === 'string') {
            return lhs === rhs;
        } else if (typeof lhs === typeof rhs) {
            return lhs.from === rhs.from && lhs.to === rhs.to;
        } else {
            return false;
        }
    }

    function compareWorklists(lhs, rhs)
    {
        if (typeof lhs === 'string') {
            return lhs === rhs;
        } else if (typeof rhs === typeof lhs && lhs.size === rhs.size) {
            // This assumes lhs & rhs are both sets
            for (let x of lhs) {
                if (!rhs.has(x)) {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    function compareOptional(lhs, rhs)
    {
        if (lhs.size !== rhs.size) {
            return false;
        }

        for ([name, items] of lhs) {
            if (!rhs.has(name)) {
                return false;
            }

            let rhsItems = rhs.get(name);

            if (typeof items !== 'object') {
                if (items !== rhsItems) {
                    return false;
                }
            } else if (items.length !== rhsItems.length
                || !items.every(rhsItems.includes.bind(rhsItems))) {
                return false;
            }
        }

        return true;
    }

    function filterToJSONReplacer(key, value)
    {
        switch (key) {
            case 'id':
            case 'name':
            case 'type':
                return undefined;

            case 'worklistDefinitions':
                return encodeWorklists(value);

            case 'worklists':
                return encodeWorklists(value);

            case 'optional':
                return [...value];

            default:
                return value;
        }
    }

    /* Definition */
    function WorklistFilter()
    {
        // Type and Id are internal details filtered out when supplying the whole filter
        // to the server, but may sent instead of the filter when loading a recent/saved filter
        // or applying a quick filter.
        this.type = null;
        this.id = null;

        this.site = 1;
        this.context = 'all';

        this.period = 'today';

        this.worklistDefinitions = 'all';
        this.worklists = 'all';
        this.sortBy = 0; // Time
        this.optional = new Map();

        this.combined = false;
    }

    WorklistFilter.prototype.constructor = WorklistFilter;

    WorklistFilter.prototype.compare = function (other) {
        const timePeriodsEqual = compareTimePeriods(this.period, other.period);
        const worklistDefinitionsEqual = compareWorklists(this.worklistDefinitions, other.worklistDefinitions);
        const worklistsEqual = compareWorklists(this.worklists, other.worklists);
        const optionalEqual = compareOptional(this.optional, other.optional);

        return this.site === other.site
            && this.context === other.context
            && timePeriodsEqual
            && worklistDefinitionsEqual
            && worklistsEqual
            && this.sortBy === other.sortBy
            && optionalEqual
            && this.combined === other.combined;
    };

    WorklistFilter.prototype.clone = function () {
        let cloned = Object.assign(Object.create(WorklistFilter.prototype), this);

        if (typeof this.worklists === 'object') {
            cloned.worklists = new Set([...this.worklists]);
        }

        cloned.optional = new Map([...this.optional]);

        return cloned;
    };

    WorklistFilter.fromJSON = function (json) {
        const data = JSON.parse(json['filter']);
        const result = new WorklistFilter();

        result.id = json['id'];
        result.name = json['name'];

        result.site = data.site;
        result.context = data.context;

        result.period = data.period;

        result.worklistDefinitions = decodeWorklists(data.worklistDefinitions ?? 'all');
        result.worklists = decodeWorklists(data.worklists ?? 'all');
        result.sortBy = data.sortBy;
        result.optional = new Map(data.optional);

        result.combined = data.combined;

        return result;
    };

    WorklistFilter.prototype.asJSON = function () {
        return JSON.stringify(this, filterToJSONReplacer);
    };

    Object.defineProperty(WorklistFilter.prototype, 'worklistDefinitionsArray', {
        get: function () {
            return encodeWorklists(this.worklistDefinitions);
        },
        set: function (newLists) {
            this.worklistDefinitions = decodeWorklists(newLists);
        }
    });

    Object.defineProperty(WorklistFilter.prototype, 'worklistsArray', {
        get: function () {
            return encodeWorklists(this.worklists);
        },
        set: function (newLists) {
            this.worklists = decodeWorklists(newLists);
        }
    });

    // Helper for dates
    WorklistFilter.getDateRange = function (period) {
        if (typeof period !== 'string') {
            return period;
        }

        const range = {from: '', to: ''};
        const fromDate = new Date();
        const toDate = new Date();

        switch (period) {
            case 'yesterday':
                fromDate.setDate(fromDate.getDate() - 1);
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                break;

            case 'today':
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                break;

            case 'tomorrow':
                fromDate.setDate(fromDate.getDate() + 1);
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                break;

            case 'this-week':
                toDate.setDate(toDate.getDate() + 6);
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                range.to = $.datepicker.formatDate('yy-mm-dd', toDate);
                break;

            case 'next-week':
                fromDate.setDate(fromDate.getDate() + 7);
                toDate.setDate(toDate.getDate() + 13);
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                range.to = $.datepicker.formatDate('yy-mm-dd', toDate);
                break;

            case 'next-7-days':
                toDate.setDate(toDate.getDate() + 7);
                range.from = $.datepicker.formatDate('yy-mm-dd', fromDate);
                range.to = $.datepicker.formatDate('yy-mm-dd', toDate);
                break;
        }

        return range;
    };

    exports.WorklistFilter = WorklistFilter;
}(OpenEyes));
