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

    this.create();
  }

  /**
   * @name OpenEyes.UI.LightningViewer#create
   */
  LightningViewer.prototype.create = function () {

    var self = this;

    $('.js-lightning-image-preview-page').each(function () {
      $('<img />', {src: $(this).data('src')}).on('load error', function () {
        $(this).closest('.js-lightning-image-preview-page').data('loaded', true);
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

      var xPercentage = relX / $(this).width();

      var $icons = $('.js-lightning-view-icon');
      var xIndex = Math.floor($icons.length * xPercentage);
      var $icon = $icons.eq(xIndex);

      var $preview;

      if (self.selectedEventId === null) {
        $('.icon-event').removeClass('js-hover');
        $icon.addClass('js-hover');
        $preview = self.changePreview($icon.data('event-id'));
      } else {
        $preview = $('.js-lightning-image-preview[data-event-id="' + self.selectedEventId + '"]');
      }

      if ($preview.data('paged')) {
        var relY = e.pageY - parentOffset.top;
        var yPercentage = relY / $(this).height();
        var page = Math.floor($preview.data('image-count') * yPercentage);
        self.changePreviewPage($preview.data('event-id'), page);
      }
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

  LightningViewer.prototype.changePreview = function (event_id) {
    var $preview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
    if (this.currentEventId === event_id) {
      return $preview;
    }

    this.currentEventId = event_id;
    $('.js-lightning-image-preview').hide();
    $preview.show();

    var $meta = $('.js-lightning-meta');
    $meta.find('.js-lightning-preview-type').text($preview.data('preview-type'));
    $meta.find('.js-lightning-date').html($preview.data('date'));

    if ($preview.data('paged')) {
      $preview.find('.js-lightning-image-preview-page').hide();
      this.showImage($preview.find('.js-lightning-image-preview-page').first());
    } else {
      this.showImage($preview.find('.js-lightning-image-preview-page'));
    }
    return $preview;
  };

  LightningViewer.prototype.showImage = function ($image) {
    $('.js-preview-image-loader').toggle(!$image.data('loaded'));
    $image.toggle($image.data('loaded'));
  };

  LightningViewer.prototype.changePreviewPage = function (event_id, page) {
    if (this.currentEventId === event_id && this.currentPageNumber === page) {
      return;
    }

    this.currentPageNumber = page;

    var $preview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
    if (page !== null) {
      $preview.find('.js-lightning-image-preview-page').hide();
      this.showImage($preview.find('.js-lightning-image-preview-page[data-page-number="' + page + '"]'));
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