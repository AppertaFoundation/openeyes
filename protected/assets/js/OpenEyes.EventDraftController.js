/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

(function (exports) {
    const default_options = {
        formId: null,

        enabledSelector: '.js-auto-save-enabled',
        connectionErrorContentsSelector: '.js-lost-draft-connection-popup-content > div',
        autoSaveWarningListSelector: 'ul.auto-save-warnings-list',
        draftSaveWarningContentsSelector: '.js-auto-save-warnings-popup-content',
        connectionErrorTabSelector: '.js-connection-error-tab',
        retestDraftConnectionButtonSelector: '.js-retest-draft-connection',
        saveDraftButtonSelector: '.js-event-action-save-draft',
        confirmSavePopupButtonSelector: 'button.js-event-action-save-confirm-popup',
        confirmSaveButtonSelector: 'button.js-event-action-save-confirm',
        cancelButtonSelector: 'button.js-event-action-cancel',
        autoSaveAlertsSelector: 'div#js-auto-save-alerts',
        autoSaveAlertsListSelector: '#js-auto-save-alerts-list',
        draftIdSelector: 'input#draft_id',
        loadExistingDraftSelector: '#js-load-existing-draft',
        deleteExistingDraftSelector: '#js-delete-existing-draft',
        existingDraftBannerSelector: '#js-existing-draft-banner',
        saveInterval: 30000
    };

    function EventDraftController(options) {
        this.options = $.extend(true, {}, default_options, options);

        if ($(this.options.enabledSelector).length) {
            this.warnings = [];
            this.connectionDialogOpen = false;

            this.draftSaveConnectionDialog = new OpenEyes.UI.Dialog({
                title: "Auto-save connection error",
                content: $(this.options.connectionErrorContentsSelector).clone(),
            });

            this.draftSaveConnectionDialog.on('open', () => {
                this.connectionDialogOpen = true;
            });

            this.draftSaveConnectionDialog.on('close', () => {
                this.connectionDialogOpen = false;
            });

            $(this.options.confirmSavePopupButtonSelector).on('click', function(event) {
                let $warningList = $(this.options.autoSaveWarningListSelector);
                $warningList.empty();

                for (warning of this.warnings) {
                   $warningList.append(`<li>${warning}</li>`);
                }

                let draftSaveWarningDialog = new OpenEyes.UI.Dialog({
                    title: "Confirm event save",
                    content: $(`${this.options.draftSaveWarningContentsSelector} > div`).clone(),
                });

                draftSaveWarningDialog.open();
            });

            this.hideConnectionErrorUI();

            if ($(this.options.existingDraftBannerSelector).length === 0) {
                setInterval(() => {this.attemptDraftSave()}, this.options.saveInterval);
            } else {
                this.disableSave();
            }

            $(this.options.connectionErrorTabSelector).on('click', () => {
                this.draftSaveConnectionDialog.open();

                $(this.options.retestDraftConnectionButtonSelector).on('click', () => {
                    this.attemptDraftSave();
                });
            });

            $(this.options.saveDraftButtonSelector).on('click', () => {
                this.attemptDraftSave();
            });

            $(this.options.loadExistingDraftSelector).on('click', () => {
                this.loadExistingDraft();
            });
            $(this.options.deleteExistingDraftSelector).on('click', () => {
                this.deleteExistingDrafts();
            });
        } else {
            throw new Error("DraftSaveController should not be instantiated if its requisite elements are not present in the DOM");
        }
    };

    EventDraftController.prototype.constructor = EventDraftController;

    EventDraftController.prototype.loadExistingDraft = function() {
        // Reload the page using the existing draft.
        const draftId = $(this.options.existingDraftBannerSelector).data('existing-draft');
        let url = new URL(
            $(this.options.existingDraftBannerSelector).data('existing-draft-url'),
            `${window.location.protocol}//${window.location.hostname}`
        );
        url.searchParams.set('draft_id', draftId);
        window.location = url;
    };

    EventDraftController.prototype.deleteExistingDrafts = function() {
        // Delete the existing drafts for event creation (there should only be 1 but this handles edge cases),
        // then enable auto-save (but only if a connection can be established).
        $.ajax(
            {
                url: `/${OE_module_class}/Default/deleteDrafts`,
                type: 'POST',
                data: {
                    patient_id: OE_patient_id,
                    event_type: OE_module_class,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                success: (response) => {
                    $(this.options.existingDraftBannerSelector).hide();
                    $(this.options.saveDraftButtonSelector).removeClass('disabled');

                    // Remove the draft entries from the sidebar that were deleted.
                    response.forEach((draft) => {
                        $(`ul.events li[data-draft-id="${draft}"]`).remove();
                    })

                    setInterval(() => {this.attemptDraftSave()}, this.options.saveInterval);
                    this.hideConnectionErrorUI();
                },
                error: (err) => {
                    this.showConnectionErrorUI();
                }
            }
        )
    }

    EventDraftController.prototype.attemptDraftSave = function() {
        let formData = $(`form#${this.options.formId}`).serialize();

        $.ajax(
            {
                url: `/${OE_module_class}/Default/saveDraft`,
                type: 'POST',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                    OE_episode_id: OE_episode_id,
                    OE_event_id: OE_event_id,
                    OE_module_class: OE_module_class,
                    originating_url: window.location.pathname + window.location.search,
                    draft_id: $(this.options.draftIdSelector).val(),
                    form_data: JSON.stringify(formData),
                },
                success: (resp) => {
                    if (resp.errors !== undefined) {
                        $(this.options.autoSaveAlertsSelector).show();
                        let $autoSaveAlertsList = $(`${this.options.autoSaveAlertsSelector} > ${this.options.autoSaveAlertsListSelector}`);
                        $autoSaveAlertsList.empty();

                        for (attribute in resp.errors) {
                            for (error of resp.errors[attribute]) {
                                $autoSaveAlertsList.append(`<li>${error}</li>`);
                            }
                        }
                    }

                    //There is a potential issue here if a new warning appears after the user has already confirmed that they want to save
                    //Perhaps we need to compare previously issued warnings with the current set of warnings
                    if (resp.warnings !== undefined && resp.warnings.length) {
                        this.warnings = resp.warnings;
                        this.requireConfirmBeforeSave();
                    } else {
                        this.allowSaveWithoutConfirm();
                    }

                    if (resp.draft_id !== undefined) {
                        const url = new URL(window.location);

                        if (url.searchParams.get('draft_id') != resp.draft_id) {
                            url.searchParams.set('draft_id', resp.draft_id);
                            history.replaceState(null, '', url);
                        }
                        $(this.options.draftIdSelector).val(resp.draft_id);
                    }

                    this.hideConnectionErrorUI();
                },
                error: (err) => {
                    this.showConnectionErrorUI();
                }
            }
        );
    };

    EventDraftController.prototype.showConnectionErrorUI = function() {
        $(this.options.connectionErrorTabSelector).show();
        $(this.options.confirmSaveButtonSelector).addClass('disabled');
        $(this.options.cancelButtonSelector).addClass('disabled');
    };

    EventDraftController.prototype.hideConnectionErrorUI = function() {
        if (this.connectionDialogOpen) {
            this.draftSaveConnectionDialog.close();
        }
        $(this.options.connectionErrorTabSelector).hide();
        $(this.options.confirmSaveButtonSelector).removeClass('disabled');
        $(this.options.cancelButtonSelector).removeClass('disabled');
    };

    EventDraftController.prototype.requireConfirmBeforeSave = function() {
        $(this.options.confirmSaveButtonSelector).hide();
        $(this.options.confirmSavePopupButtonSelector).show();
    };

    EventDraftController.prototype.allowSaveWithoutConfirm = function() {
        $(this.options.confirmSaveButtonSelector).show();
        $(this.options.confirmSavePopupButtonSelector).hide();
    };

    EventDraftController.prototype.disableSave = function() {
        $(this.options.confirmSaveButtonSelector).addClass('disabled');
        $(this.options.saveDraftButtonSelector).addClass('disabled');
    };

    exports.EventDraftController = EventDraftController;
}(OpenEyes));
