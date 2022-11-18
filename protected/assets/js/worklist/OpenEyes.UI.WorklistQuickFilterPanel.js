var OpenEyes = OpenEyes || {};
OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {
    const filterOptionTemplate =
        '<div class="filter-btn js-filter-option" data-id="{{id}}">\
               <div class="name">{{label}}</div>\
               <div class="count">{{count}}</div>\
           </div>';

    function WorklistQuickFilterPanel(controller, panelElementSelector, sortByPopupSelector) {
        this.controller = controller;
        this.panel = $(panelElementSelector);

        this.nameEntryTimeoutId = 0;

        this.buttons = [];
        this.menus = [];

        this.sortByPopup = sortByPopupSelector !== '' ? $(sortByPopupSelector) : null;

        this.setupPanel();
    }

    WorklistQuickFilterPanel.prototype.constructor = WorklistQuickFilterPanel;

    WorklistQuickFilterPanel.prototype.setupPanel = function () {
        const view = this;
        const controller = this.controller;

        this.panel.find('.js-clinic-btn-filter').click(function () {
            controller.quick = $(this).data('filter');
        });

        const waitingFor = this.panel.find('.js-clinic-filter-menu[data-filter="waitingFor"]');
        const assignedTo = this.panel.find('.js-clinic-filter-menu[data-filter="assignedTo"]');

        new OpenEyes.UI.NavBtnPopup('waiting-for', waitingFor.find('.filter-btn'), waitingFor.find('nav.options'))
            .useWrapperEvents(waitingFor);

        new OpenEyes.UI.NavBtnPopup('assigned-users', assignedTo.find('.filter-btn'), assignedTo.find('nav.options'))
            .useWrapperEvents(assignedTo);

        // Preserve current menu label contents for later, when they need to be restored
        this.panel.find('.js-clinic-filter-menu > .filter-btn .name').each(function () {
            const label = $(this);

            label.data('old-label', label.html());
        });

        this.panel.find('input.search').keyup(function (e) {
            const input = $(this);

            clearTimeout(view.nameEntryTimeoutId);

            view.nameEntryTimeoutId = setTimeout(function () {
                controller.quickName = input.val();
            }, 500);
        });

        if (this.sortByPopup) {
            const sortByPopup = this.sortByPopup;

            this.panel.find('.popup-filter.table-sort').click(function () {
                sortByPopup.show();
            });

            sortByPopup.find('.js-close-popup-btn').click(function () {
                sortByPopup.hide();
            });

            sortByPopup.find('.js-set-sort-by-btn').click(function () {
                // Override the value set on the rhs panel
                controller.quickSortBy = sortByPopup.find('.btn-list input:checked').val();

                sortByPopup.hide();
            });
        }
    }

    WorklistQuickFilterPanel.prototype.setSortBy = function (idMappings, sortBy) {
        this.panel.find('.popup-filter.table-sort').text(idMappings[sortBy]);

        if (this.sortByPopup) {
            this.sortByPopup.find('.btn-list input:checked').prop('checked', false);
            this.sortByPopup.find(`.btn-list input[value=${sortBy}]`).prop('checked', true);
        }
    }

    WorklistQuickFilterPanel.prototype.setQuickSelection = function (selected) {
        this.panel.find('.js-clinic-btn-filter').removeClass('selected');
        this.panel.find('.js-filter-option').removeClass('selected');
        this.panel.find('.js-clinic-filter-menu').removeClass('selected');
        this.panel.find('.js-clinic-filter-menu').attr('data-selected-id', null);

        this.panel.find('.js-clinic-filter-menu > .filter-btn .name').each(function () {
            $(this).html($(this).data('old-label'));
        });

        this.panel.find('.js-clinic-filter-menu > .filter-btn .count').text('');

        if (typeof selected === 'string') {
            this.panel
                .find(`.js-clinic-btn-filter[data-filter="${selected}"]`)
                .addClass('selected');
        } else {
            const type = selected.type;
            const id = selected.value;
            const menu = this.panel.find(`.js-clinic-filter-menu[data-filter="${type}"]`);
            const menuButton = menu.children('.filter-btn');

            menu.addClass('selected');

            const option = menu.find(`.js-filter-option[data-id="${id}"]`);

            option.addClass('selected');

            menu.attr('data-selected-id', id);
            menuButton.children('.name').text(option.children('.name').text());
            menuButton.children('.count').text(option.children('.count').text());
        }
    }

    WorklistQuickFilterPanel.prototype.setQuickName = function (name) {
        this.panel.find('input.search').val(name);
    }

    WorklistQuickFilterPanel.prototype.setListsAndCounts = function (data) {
        const controller = this.controller;

        const panelButtons = this.panel.find('.js-clinic-btn-filter');
        const panelMenus = this.panel.find('.js-clinic-filter-menu');

        panelButtons.each(function () {
            const button = $(this);
            const name = button.data('filter');

            button.find('div.count').text(data[name]);
        });

        panelMenus.each(function () {
            const menu = $(this);
            const name = menu.data('filter');
            const into = menu.children('nav.options');

            into.children().remove();

            for (option of data[name]) {
                const entry = Mustache.render(filterOptionTemplate, option);

                into.append(entry);
            }

            into.find('.js-filter-option').off('click').on('click', function () {
                controller.quick = { type: name, value: $(this).data('id') };
            });
        });
    }

    WorklistQuickFilterPanel.prototype.changeWaitingFor = function (id, amount) {
        const controller = this.controller;
        const button = this.panel.find(`.js-clinic-filter-menu[data-filter="waitingFor"] .js-filter-option[data-id="${id}"]`);

        if (button.length > 0 && amount !== 0) {
            const header = this.panel.find(`.js-clinic-filter-menu[data-filter="waitingFor"][data-selected-id="${id}"] > .filter-btn .count`);
            const count = parseInt(button.children('.count').text().trim()) + amount;

            if (count > 0) {
                button.children('.count').text(count);
            } else {
                button.remove();
            }

            if (header.length > 0) {
                header.text(Math.max(count, 0));
            }
        } else if (button.length === 0 && amount >= 0) {
            const data = { id: id, label: `… ${id}`, count: amount };
            const entry = Mustache.render(filterOptionTemplate, data);

            this.panel.find('.js-clinic-filter-menu[data-filter="waitingFor"] nav.options').append(entry);
            this.panel.find(`.js-clinic-filter-menu[data-filter="waitingFor"] .js-filter-option[data-id="${id}"]`)
                .click(function () {
                    controller.quick = { type: 'waitingFor', value: $(this).data('id') };
                });
        }
    }

    WorklistQuickFilterPanel.prototype.changeAssignedTo = function (mappings, id, amount) {
        const controller = this.controller;
        const button = this.panel.find(`.js-clinic-filter-menu[data-filter="assignedTo"] .js-filter-option[data-id="${id}"]`);

        if (button.length > 0) {
            const header = this.panel.find(`.js-clinic-filter-menu[data-filter="assignedTo"][data-selected-id="${id}"] > .filter-btn .count`);
            const count = parseInt(button.children('.count').text().trim()) + amount;

            if (count > 0) {
                button.children('.count').text(count);
            } else {
                button.remove();
            }

            if (header.length > 0) {
                header.text(Math.max(count, 0));
            }
        } else if (amount > 0) {
            const data = { id: id, label: mappings.get(id), count: amount };
            const entry = Mustache.render(filterOptionTemplate, data);

            this.panel.find('.js-clinic-filter-menu[data-filter="assignedTo"] nav.options').append(entry);
            this.panel.find(`.js-clinic-filter-menu[data-filter="assignedTo"] .js-filter-option[data-id="${id}"]`)
                .click(function () {
                    controller.quick = { type: 'assignedTo', value: $(this).data('id') };
                });
        }
    }

    WorklistQuickFilterPanel.prototype.changeCategoryCount = function (statusCategory, amount) {
        const controller = this.controller;
        const button = this.panel.find(`.js-clinic-btn-filter[data-filter="${statusCategory}"]`);

        if (button.length > 0) {
            const count = parseInt(button.children('.count').text().trim()) + amount;

            button.children('.count').text(Math.max(count, 0));
        }
    }

    WorklistQuickFilterPanel.prototype.changeStatusTypeCount = function (status, amount) {
        // The following string constants used in the switch statement need to reflect
        // those in getStatusString() inside protected/models/Pathway.php
        switch (status) {
            case 'stuck':
            case 'waiting':
            case 'long-wait':
            case 'break':
                this.changeCategoryCount('clinic', amount);
                this.changeCategoryCount('issues', amount);
                break;

            case 'active':
                this.changeCategoryCount('clinic', amount);
                break;

            case 'checked-out':
                this.changeCategoryCount('discharged', amount);
                break;

            case 'done':
                this.changeCategoryCount('clinic', amount);
                this.changeCategoryCount('done', amount);
                break;
        }
    }

    exports.WorklistQuickFilterPanel = WorklistQuickFilterPanel;
}(OpenEyes.UI));
