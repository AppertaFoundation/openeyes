/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {

    function FreehandDraw(options = {}) {
        this.options = $.extend(true, {}, FreehandDraw._defaultOptions, options);
        this.imageAnnotators = [];
        this.$wrapper = document.querySelector(this.options.templateAppendTo);

        this.initialiseTriggers();
    }

    FreehandDraw._defaultOptions = {
        'template_selector': '#new_drawing_template',
        'templateAppendTo': 'section.OEModule_OphCiExamination_models_FreehandDraw .element-fields',
        'annoteButtonSelector': '.js-image-annotate'
    };

    FreehandDraw.prototype.initialiseTriggers = function () {
        OpenEyes.UI.DOM.addEventListener(this.$wrapper, 'click', '.js-image-annotate', (e) => {
            e.preventDefault();

            // Disable the event action buttons (i.e., cancel and save) while the annotation is open
            // Prevents exiting event before annotation is saved
            const elements = document.querySelectorAll('[class^="js-event-action-"]');

            elements.forEach(element => {
                element.disabled = true;
            });

            const $button = e.target;
            const index = $button.dataset.annotateRowIndex;
            $button.closest('.freedraw-group').style.display = "none";
            document.getElementById(`annotate-wrapper-${index}`).style.display = "block";

            const $row = document.getElementById(`js-annotate-image-${index}`);
            const is_not_init = $row.querySelector('.canvas-js.js-not-initialized');

            if (is_not_init) {
                this.initTemplate($row.dataset.templateUrl, $row.dataset.key);
                is_not_init.classList.remove('js-not-initialized');

                this.setIsEdited(index, $row);
            }

            // Copy comments textarea content from the un-annotated view
            const $table = $button.closest('table');
            const comment = $table.querySelector('textarea').value;
            document.getElementById(`annote-comments-field-${index}`).value = comment;
        });

        OpenEyes.UI.DOM.addEventListener(this.$wrapper, 'click', '.js-save-annotation', async (e) => {
            e.preventDefault();
            const $button = e.target;
            const index = $button.dataset.annotateRowIndex;
            document.getElementById(`annote-template-view-${index}`).style.display = 'block';
            document.getElementById(`annotate-wrapper-${index}`).style.display = 'none';

            const $row = document.getElementById(`js-annotate-image-${index}`);
            this.setIsEdited(index, $row);

            const data = await this.imageAnnotators[index].getCanvasDataUrl();
            this.setImageData(index, data);

            const img_data_value = this.$wrapper.querySelector(`.js-image-data-${index}`).value;
            document.getElementById(`js-img-preview-${index}`).src = img_data_value;

            this.enableButtonIfNoAnnotation();
        });

        OpenEyes.UI.DOM.addEventListener(this.$wrapper, 'click', '.js-cancel-annotation', (e) => {
            e.preventDefault();
            const $button = e.target;
            const index = $button.dataset.annotateRowIndex;
            const $wrapper = document.getElementById(`annotate-wrapper-${index}`);
            document.getElementById(`annote-template-view-${index}`).style.display = 'block';
            $wrapper.style.display = 'none';

            OpenEyes.UI.DOM.trigger($wrapper.querySelector('.js-clear-all'), 'click');
            document.getElementById(`js-img-preview-${index}`).src = $button.dataset.templateUrl;

            this.removeIsEdited(index);

            this.enableButtonIfNoAnnotation();
        });

        OpenEyes.UI.DOM.addEventListener(this.$wrapper, 'click', '.trash', (e) => {
            e.preventDefault();
            const index = e.target.closest('.freedraw-group').dataset.key;
            document.getElementById(`annote-template-view-${index}`).remove();
            document.getElementById(`annotate-wrapper-${index}`).remove();
            const dividers = document.getElementsByClassName(`js-divider-${index}`);
            if (dividers[0]) {
                dividers[0].remove();
            }

            const annotator_index = this.imageAnnotators.indexOf(index);
            if (annotator_index > -1) {
                this.imageAnnotators.splice(index, 1);
            }
        });

        this.initialiseCommentTriggers();
    };

    FreehandDraw.prototype.initialiseCommentTriggers = function () {

        const $section = document.querySelector('section.OEModule_OphCiExamination_models_FreehandDraw');
        OpenEyes.UI.DOM.addEventListener($section, 'click', '.js-add-comments', function(e) {
            e.preventDefault();
            const button = this;
            const tr = button.closest('tr');

            tr.querySelector('.user-comment').style.display = 'none';
            tr.querySelector('.comments-who').style.display = 'none';
            tr.querySelector('.js-input-comments-wrapper').style.display = 'block';
            autosize($('.autosize'));
        });

        OpenEyes.UI.DOM.addEventListener($section, 'click', '.js-remove-add-comments', function(e) {
            e.preventDefault();
            const button = this;
            const tr = button.closest('tr');
            tr.querySelector('.js-input-comments-wrapper').style.display = 'none';
            tr.querySelector('.js-input-comments').value = null;
            tr.querySelector('.js-add-comments').style.display = 'inline-flex';
        });
    };

    FreehandDraw.prototype.enableButtonIfNoAnnotation = function () {
        let is_open = false;
        const annotators = document.querySelectorAll('[id^="annotate-wrapper-"]');
        annotators.forEach((annotator) => {
            if (annotator.offsetParent !== null) {
                is_open = true;
            }
        });

        // re-enable the event action buttons (i.e., cancel and save)
        const elements = document.querySelectorAll('[class^="js-event-action-"]');

        elements.forEach(element => {
            element.disabled = is_open;
        });
    };

    FreehandDraw.prototype.addTemplate = function (item) {
        const self = this;
        const row_count = OpenEyes.Util.getNextDataKey('.oe-annotate-image', 'key');
        const template = Mustache.render($('#new_drawing_template').text(), {
            row_count: row_count,
            filename: item.filename,
            template_url: item.dateTemplateUrl,
            full_name: item.full_name,
            date: item.created_date
        });

        const adder = this.$wrapper.querySelector('.add-data-actions');
        if (row_count > 0) {
            adder.insertAdjacentHTML('beforebegin', `<hr class='divider js-divider-${row_count}'>`);
        }

        adder.insertAdjacentHTML('beforebegin', template);

        // add image to the image-data, so the image will be saved even if the user
        // does not click on annotate button
        (async function() {
            let blob = await fetch(`${item.dateTemplateUrl}`).then(r => r.blob());
            let data_url = await new Promise(resolve => {
                let reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.readAsDataURL(blob);
            });

            const $wrapper = document.getElementById(`js-annotate-image-${row_count}`);
            self.setIsEdited(row_count, $wrapper);

            // set the actual image-data

            self.setImageData(row_count, data_url);
        })();
    };

    FreehandDraw.prototype.initTemplate = function (dateTemplateUrl, row_count = 0, add_event_listeners = true) {
        const self = this;
        const $annotate = document.getElementById(`js-annotate-image-${row_count}`);
        $annotate.dataset.imageAnnotatorId = row_count;

        const imageAnnotator = new OpenEyes.UI.ImageAnnotator(dateTemplateUrl, {
            'annotateSelector': `#js-annotate-image-${row_count}`,
            'afterInit': async function () {
                    self.setImageData(row_count, await this.getCanvasDataUrl.call(this));
                    // We only show actions after everything is loaded to avoid wrong data being saved
                    self.showAnnotationActions(row_count);
            },
            'withEventListeners': add_event_listeners
        });

        this.imageAnnotators[row_count] = imageAnnotator;

        const $textarea = document.getElementById(`annote-comments-field-${row_count}`);

        OpenEyes.UI.DOM.addEventListener($textarea, 'keyup', '', function(e) {
            const $main_comment = document.getElementById(`comments-field-${row_count}`);
            const $span = document.getElementById(`user-comment-${row_count}`);
            $main_comment.value = this.value;
            $span.innerHTML = this.value;
        });

        return imageAnnotator;
    };

    FreehandDraw.prototype.setIsEdited = function (index, $intoContainer) {
        let $is_edited = document.getElementsByName(`OEModule_OphCiExamination_models_FreehandDraw[entries][${index}][image][is_edited]`);

        if ($is_edited.length === 0) {
            $is_edited = OpenEyes.UI.DOM.createElement('input', {
                type: 'hidden',
                name: `OEModule_OphCiExamination_models_FreehandDraw[entries][${index}][image][is_edited]`,
                value: 1
            });

            $is_edited.dataset.test = 'freehand-drawing-is-edited-input';

            $intoContainer.appendChild($is_edited);
        }
    };

    FreehandDraw.prototype.removeIsEdited = function(index) {
        const $is_edited = document.getElementsByName(`OEModule_OphCiExamination_models_FreehandDraw[entries][${index}][image][is_edited]`);

        if ($is_edited.length) {
            $is_edited[0].remove();
        }
    };

    FreehandDraw.prototype.setImageData = function (rowIndex, data) {
        const imageData = this.$wrapper.querySelector(`.js-image-data-${rowIndex}`);

        if(imageData && data !== false) {
            imageData.value = data;
        }
    };

    FreehandDraw.prototype.showAnnotationActions = function (rowIndex) {
        const annotateWrapper = document.getElementById(`annotate-wrapper-${rowIndex}`);

        if(annotateWrapper !== null) {
            const annotationActionsContainer = annotateWrapper.querySelector('.js-annotation-actions-container');

            if(annotationActionsContainer !== null) {
                annotationActionsContainer.style.display = '';
            }
        }
    };

    exports.FreehandDraw = FreehandDraw;

})(OpenEyes.OphCiExamination);
