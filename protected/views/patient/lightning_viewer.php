<?php

/**
 * @var string $selectedDocumentType
 * @var array $documentGroups
 * @var array $documentChunks
 */

$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';

$documentTypes = array_keys($documentGroups);
sort($documentTypes);
?>

<?php $this->renderPartial('//patient/episodes_sidebar'); ?>

<main class="oe-lightning-viewer">
  <div class="lightning-timeline">
    <div class="timeline-options js-lightning-options">

      <div class="lightning-btn js-lightning-options-btn">
        <svg viewBox="0 0 30 30" class="lightning-icon">
          <use xlink:href="<?= $navIconUrl ?>#lightning-viewer-icon"></use>
        </svg>
        <i class="oe-i small-icon arrow-down-bold"></i>
      </div>

      <div class="change-timeline js-change-timeline" style="display: none;">
        <ul>
            <?php foreach ($documentTypes as $documentType) {
                $events = $documentGroups[$documentType];
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <li class="<?php if ($documentType === $selectedDocumentType): ?>selected<?php endif; ?>">
                <i class="oe-i-e <?= $events[0]->eventType->getEventIconCssClass() ?>"></i>
                <a href="<?= Yii::app()->createUrl('/patient/lightningViewer',
                    array('id' => $this->patient->id, 'document_type' => $documentType)) ?>"
                >
                    <?= $documentType ?> (<?= count($events) ?>)
                </a>
              </li>
            <?php } ?>
        </ul>
      </div>
    </div>

    <div class="timeline">
      <table>
        <tbody>
        <tr>
            <?php foreach ($documentChunks as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td class="date-divider js-timeline-date" data-year="<?= $year ?>">
                  <?= $year ?>
                <i class="oe-i small collapse"></i>
              </td>
            <?php } ?>
        </tr>
        <tr>
            <?php
            foreach ($documentChunks as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td>
                <div class="icon-group js-icon-group" data-year="<?= $year ?>">
                    <?php foreach ($events as $event) { ?>
                      <span class="icon-event js-lightning-view-icon"
                            data-event-id="<?= $event->id ?>"
                            data-event-image-url="<?= Yii::app()->createUrl('/eventImage/view/',
                                array('id' => $event->id)) ?>"
                      >
                        <i class="oe-i-e <?= $event->eventType->getEventIconCssClass() ?>"></i>
                      </span>
                    <?php } ?>
                </div>
                <div class="js-icon-group-count" data-year="<?= $year ?>" style="display: none;">
                  (<?= count($events) ?>)
                </div>
              </td>
            <?php } ?>
        </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="flex-layout flex-left flex-top">
    <div class="oe-lightning-meta">
      <div class="help">
        swipe to scan | click to lock
      </div>
    </div>
    <div class="oe-lightning-quick-view js-lightning-view-image-container">
    </div>

  </div>
</main>

<script>
  $(function () {
      $('.js-lightning-view-icon').each(function () {
        var $img = $('<img />', {
          src: $(this).data('event-image-url'),
          class: 'js-lightning-image-preview',
          style: 'display: none; width: 520px;',
          alt: 'No preview available at this time',
          'data-event-id': $(this).data('event-id'),

        });

        $img.appendTo('.js-lightning-view-image-container');
      });

      $('.js-lightning-image-preview').first().show();

      function changePreview(event_id) {
        $('.js-lightning-image-preview').hide();
        $('.js-lightning-image-preview[data-event-id="' + event_id + '"]').show();
      }

      $(this).on('mouseover', '.js-lightning-view-icon', function () {
        changePreview($(this).data('event-id'));
      });

      $(this).on('mouseout', '.js-lightning-view-icon', function () {
        $('.icon-event').removeClass('js-hover');
      });

      $(this).on('mousemove', '.js-lightning-image-preview', function (e) {
        var parentOffset = $(this).parent().offset();
        //or $(this).offset(); if you really just want the current element's offset
        var relX = e.pageX - parentOffset.left;
        //var relY = e.pageY - parentOffset.top;
        var percentage = relX / $(this).width();
        console.log(relX + ':' + percentage);

        var $icons = $('.js-lightning-view-icon');
        var index = Math.floor($icons.length * percentage);
        console.log(index);
        var $icon = $icons.eq(index);
        $('.icon-event').removeClass('js-hover');
        $icon.addClass('js-hover');
        changePreview($icon.data('event-id'));
      });

      var optionsDisplayed = false;

      // handles touch
      $('.js-lightning-options-btn').click(changeOptions);

      // enchance with mouseevents through DOM wrapper
      $('.js-lightning-options')
        .mouseenter(showOptions)
        .mouseleave(hideOptions);

      // controller
      function changeOptions() {
        if (!optionsDisplayed) {
          showOptions()
        } else {
          hideOptions()
        }
      }

      function showOptions() {
        $('.js-change-timeline').show();
        $('.js-lightning-options-btn').addClass('active');
        optionsDisplayed = true;
      }

      function hideOptions() {
        $('.js-change-timeline').hide();
        $('.js-lightning-options-btn').removeClass('active');
        optionsDisplayed = false;
      }

      $('.js-timeline-date').click(function (e) {
        $('.js-icon-group[data-year="' + $(this).data('year') + '"]').toggle();
        $('.js-icon-group-count[data-year="' + $(this).data('year') + '"]').toggle();
        $(this).toggleClass('collapse expand');
      });

      /**
       Demo Lightening Viewer functionality
       Set up for Letters but could any doc type...
       **/
      var letters = {
        init: function () {

          letters.selected = '#lqv_0';
          letters.revertToSelected();
          letters.xscrollIndex = 0;


          $('.icon-event').hover(
            function () {
              var letterdata = $(this).data('lightning');
              letters.changeLetter(letterdata);
            }, function () {
              letters.revertToSelected();
            }
          );

          $('.icon-event').click(function () {
            letters.newSelected(this.id);
          });


          // mouse position on letter
          $('.oe-lightning-quick-view img').mousemove(function (e) {
            var offset = $(this).offset();
            letters.xscroll(e.pageX - offset.left, e);
          });

          $('.oe-lightning-quick-view img').mouseout(function (e) {
            $('.icon-event').removeClass('js-hover');
            letters.revertToSelected();
          });

          $('.oe-lightning-quick-view img').click(function () {
            letters.newSelected(letters.xscrollIcon);
          });

        },

        changeLetter: function (letterdata) {
          var meta = $('.oe-lightning-meta');
          var d = letterdata.split(',');
          meta.children('.letter-type').text('Letter ' + d[0]);
          meta.children('.date').text(d[1]);
          meta.children('.sender').text(d[2]);

          if (d[3] !== undefined) {
            letters.letterPNG(d[3]);
          }

        },

        xscroll: function (xCoord, e) {
          var $letterImg = $('.oe-lightning-quick-view img');
          var numOfletters = 17;
          var imageWidth = 520;
          var currentIndex = Math.round(xCoord / (imageWidth / numOfletters));
          var icon = $('#lqv_' + currentIndex);
          $('.icon-event').removeClass('js-hover');
          icon.addClass('js-hover');
          letters.changeLetter(icon.data('lightning'));
          letters.xscrollIcon = 'lqv_' + currentIndex;
        },

        letterPNG: function (pngName) {
          var img = $('.oe-lightning-quick-view img');
          var imgPath = "../assets/img/_letters/";
          img.attr("src", imgPath + pngName + ".png");
        },

        revertToSelected: function () {
          var data = $(letters.selected).data('lightning');
          letters.changeLetter(data);
        },

        newSelected: function (id) {
          $(letters.selected).removeClass('selected');
          console.log(id);
          $('#' + id).addClass('selected');
          letters.selected = '#' + id; // update
          letters.revertToSelected();
        }

      };

      letters.init();

    }
  ); // ready


</script>