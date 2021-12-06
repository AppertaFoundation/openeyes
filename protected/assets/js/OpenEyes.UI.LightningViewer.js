(function (exports) {

  'use strict';

  /**'
   *
   * @param {object} options
   * @constructor
   */
  function LightningViewer() {
    this.selectedEventId = null;
    this.currentEventId = null;
    this.currentPageNumber = null;

    this.optionsDisplayed = false;
    this.currentPreview = null;
    this.sendImageUrlAjaxRequest = true;

    this.previewWidth = $('.js-lightning-view-overlay').width();

    this.create();
  }

  /**
   * @name OpenEyes.UI.LightningViewer#create
   */
  LightningViewer.prototype.create = function () {

    var self = this;

    $('.js-lightning-image-preview-page').each(function () {
      $('<img />', {src: $(this).data('src'), style: 'width:800px'}).on('load error', function () {
        var $page = $(this).closest('.js-lightning-image-preview-page');
        $page.data('loaded', true);
        var $preview = $(this).closest('.js-lightning-image-preview');
        if ($preview.is(self.currentPreview) && $page.data('page-number') == self.currentPageNumber) {
          self.showImage($page);
        }

      }).appendTo($(this));
    });

    $(document).on('mouseover', '.js-lightning-view-icon', function () {
      if (self.selectedEventId === null) {
        self.changePreview($(this).data('event-id'));
      }
    });

    $(document).on('mouseout', '.js-lightning-view-icon', function () {
      $('.icon-event').removeClass('js-hover');
    });

    $(document).on('click', '.js-lightning-view-icon', function () {
      var event_id = $(this).data('event-id');
      if (self.selectedEventId === event_id) {
        self.deselectPreview();
      }
      else {
        self.deselectPreview();
        self.selectPreview(event_id);
      }
    });

    $(document).on('mousemove', '.js-lightning-view-overlay', function (e) {
      var parentOffset = $(this).parent().offset();
      var relX = e.pageX - parentOffset.left;
      var relY = e.pageY - parentOffset.top;
      var xRatio = relX / $(this).width();
      var yRatio = relY / $(this).height();
      self.changePreviewCoords(xRatio, yRatio);
    });

    $('.js-lightning-options-btn').click(this.changeOptions);

    $('.js-lightning-options')
      .mouseenter(this.showOptions)
      .mouseleave(this.hideOptions);

    $('.js-timeline-date').click(function () {
      $('.js-icon-group[data-year="' + $(this).data('year') + '"]').toggle();
      $('.js-icon-group-count[data-year="' + $(this).data('year') + '"]').toggle();
      $(this).toggleClass('collapse expand');
    });
  };

  LightningViewer.prototype.changePreviewCoords = function (xRatio, yRatio) {
    var $icons = $('.js-lightning-view-icon');
    var xIndex = Math.floor($icons.length * xRatio);
    var $icon = $icons.eq(xIndex);

    if (this.selectedEventId === null) {
      this.previewEvent($icon.data('event-id'));
    }

    if (this.currentPreview.data('paged')) {
      var page = Math.floor(this.currentPreview.data('image-count') * yRatio);
      this.changePreviewPage(page);
    }
  };

  LightningViewer.prototype.previewEvent = function (event_id) {
    var $icons = $('.js-lightning-view-icon');
    $icons.removeClass('js-hover');
    var $icon = $icons.filter('[data-event-id="' + event_id + '"]');
    $icon.addClass('js-hover');

    this.changePreview(event_id);
  };

  LightningViewer.prototype.changePreview = function (event_id) {
      const self = this;

    if (this.currentEventId === event_id) {
      return;
    }

    this.currentEventId = event_id;
    this.currentPreview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
    if (this.currentPreview.children(".no-lightning-image").length && this.sendImageUrlAjaxRequest) {
        $.ajax({
            type: 'GET',
            url: '/eventImage/GetImageInfo',
            data: {'event_id': event_id},
            'beforeSend': function () {
                self.sendImageUrlAjaxRequest = false;
            },
            'success': function (response) {
                self.setEventImages(event_id, response);
                $('.spinner').hide();
            },
            'complete': function () {
                self.sendImageUrlAjaxRequest = true;
                self.showPage(self.currentPreview);
            },
            'error': function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Unable to generate a view at this time, please contact your administrator."
                }).open();
            }
        });
    }

    $('.js-lightning-image-preview').hide();
    this.currentPreview.show();

    var $meta = $('.js-lightning-meta');
    $meta.find('.js-lightning-preview-type').text(this.currentPreview.data('preview-type'));
    $meta.find('.js-lightning-date').html(this.currentPreview.data('date'));

    this.showPage(this.currentPreview);
  };

  LightningViewer.prototype.showPage = function ($currentPreview) {
        if ($currentPreview.data('paged')) {
            $currentPreview.find('.js-lightning-image-preview-page').hide();
            this.showImage($currentPreview.find('.js-lightning-image-preview-page').first());
        } else {
            this.showImage($currentPreview.find('.js-lightning-image-preview-page'));
        }
    };

  LightningViewer.prototype.showImage = function ($image) {
    $('.js-preview-image-loader').toggle(!$image.data('loaded'));
    $image.toggle($image.data('loaded'));
  };

  LightningViewer.prototype.setEventImages = function (event_id, response) {
      let $currentPreview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
      for (let i = 0; i < response.page_count; i++) {
          let url = response.url;
          if (response.page_count > 1) {
              url += '&page=' + i;
          }
          let previewImage = '<div class="js-lightning-image-preview-page" ' +
              'data-loaded="true" ' +
              'data-src="' + url + '" ' +
              'data-page-number="' + i + '" ' +
              'style="max-width:' + this.previewWidth + 'px; display: none;">' +
              '<img src="' + url +'" style="width:800px">' +
              '</div>';
          $currentPreview.append(previewImage);
      }
      $currentPreview.attr('data-image-count', response.page_count);
      if (response.page_count > 1) {
          $currentPreview.data('paged', 1);
      }
      $currentPreview.find('.no-lightning-image').remove();
  };

  LightningViewer.prototype.changePreviewPage = function (page) {
    if (this.currentPageNumber === page) {
      return;
    }

    this.currentPageNumber = page;

    if (page !== null) {
      this.currentPreview.find('.js-lightning-image-preview-page').hide();
      this.showImage(this.currentPreview.find('.js-lightning-image-preview-page[data-page-number="' + page + '"]'));
    }
  };

  LightningViewer.prototype.selectPreview = function (event_id) {
    $('.js-lightning-view-icon[data-event-id="' + event_id + '"]').addClass('selected');
    this.selectedEventId = event_id;
    this.changePreview(this.selectedEventId);
  };

  LightningViewer.prototype.deselectPreview = function () {
    $('.js-lightning-view-icon').removeClass('selected');
    this.selectedEventId = null;
  };

  LightningViewer.prototype.changeOptions = function () {
    if (!this.optionsDisplayed) {
      this.showOptions();
    } else {
      this.hideOptions();
    }
  };

  LightningViewer.prototype.showOptions = function () {
    $('.js-change-timeline').show();
    $('.js-lightning-options-btn').addClass('active');
    this.optionsDisplayed = true;
  };

  LightningViewer.prototype.hideOptions = function () {
    $('.js-change-timeline').hide();
    $('.js-lightning-options-btn').removeClass('active');
    this.optionsDisplayed = false;
  };

  exports.LightningViewer = LightningViewer;

}(OpenEyes.UI, OpenEyes.Util));