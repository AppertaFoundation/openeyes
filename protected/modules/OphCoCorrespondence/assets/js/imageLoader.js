/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

OpenEyes.OphCoCorrespondence = OpenEyes.OphCoCorrespondence || {};

(function (exports) {
    function ImageLoaderController(eventId, options) {
        this.eventId = eventId;
        this.options = $.extend(true, {}, ImageLoaderController._defaultOptions, options);
        this.init();
        this.imageLoadFailed = false;
        this.loadCounter = 0;
        this.imagesLoaded = 0;
        this.loadImages();
    }

    ImageLoaderController._defaultOptions = {
        imageContainerSelector: '.js-correspondence-image-overlay',
        imageId: 'correspondence_image_',
        ajaxUrl: '/OphCoCorrespondence/default/getImageInfo',
        spinnerSelector: '.spinner-overlay',
        htmlViewContainerSelector: '#correspondence_out',
        imageMaxWidth: '800px',
        maxLoadCount: 4,
    };

    ImageLoaderController.prototype.init = function () {
        let controller = this;
        this.$imageContainer = $(controller.options.imageContainerSelector);
        this.$spinner = $(controller.options.spinnerSelector);
        this.$htmlViewContainer = $(controller.options.htmlViewContainerSelector);

    };

    ImageLoaderController.prototype.loadImages = function () {
        let controller = this;
        controller.loadCounter++;
        controller.resetVariables(controller);

        $.ajax({
            type: 'GET',
            url: '/eventImage/getImageInfo',
            data: {'event_id': controller.eventId},
        }).success(function (response) {
            controller.$imageContainer.html('');
            if (response) {
                response = JSON.parse(response);
                if (response.error) {
                    controller.showErrorView();
                } else {
                    controller.pageCount = response.page_count;
                    controller.appendImages(response.url);
                }
            } else {
                controller.showErrorView();
            }
        })
            .error(function () {
                controller.showErrorView();
            });
    };

    ImageLoaderController.prototype.resetVariables = function () {
        let controller = this;
        controller.imageLoadFailed = false;
        controller.imagesLoaded = 0;
    };

    ImageLoaderController.prototype.showErrorView = function () {
        let controller = this;
        controller.$htmlViewContainer.show();
        controller.$spinner.hide();
    };

    ImageLoaderController.prototype.showSuccessView = function () {
        let controller = this;
        new OpenEyes.OphCoCorrespondence.DocumentViewerController();
        $('#' + controller.options.imageId + '0').show();
        controller.$spinner.hide();
        controller.$imageContainer.show();
    };

    ImageLoaderController.prototype.appendImages = function (url) {
        let controller = this;
        for (let index = 0; index < controller.pageCount; index++) {
            let imageId = controller.options.imageId + index;
            controller.$imageContainer.append('<img id="' + imageId + '"' +
                ' style="display:none; max-width: ' + controller.options.imageMaxWidth + '">');
            let $image = $('#' + imageId);

            $image.on('error', controller.imageNotFound.bind(controller));
            $image.on('load', function () {
                controller.imageFinishedLoading(controller)
            });

            if (controller.pageCount === 1) {
                $image.attr('src', url);
            } else {
                $image.attr('src', url + '?page=' + index);
            }
        }
    };

    ImageLoaderController.prototype.imageNotFound = function () {
        let controller = this;
        controller.imageLoadFailed = true;
        controller.imageFinishedLoading();
    };

    ImageLoaderController.prototype.allImagesLoaded = function () {
        let controller = this;
        if (controller.imageLoadFailed) {
            if (controller.loadCounter < controller.options.maxLoadCount) {
                controller.loadImages(controller)
            } else {
                controller.showErrorView(controller);
            }
        } else {
            controller.showSuccessView(controller);
        }
    };

    ImageLoaderController.prototype.imageFinishedLoading = function () {
        let controller = this;
        controller.imagesLoaded++;
        if (controller.imagesLoaded === controller.pageCount) {
            controller.allImagesLoaded(controller);
        }
    };

    exports.ImageLoaderController = ImageLoaderController;
})(OpenEyes.OphCoCorrespondence);