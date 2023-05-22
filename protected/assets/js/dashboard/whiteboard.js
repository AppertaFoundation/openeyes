document.addEventListener("DOMContentLoaded", function () {

  $('#js-wb3-openclose-actions').click(function() {
      $(this).toggleClass('up close');
      $('.wb3-actions').toggleClass('down up');
  });

  $('#exit-button').click(function (event) {
    event.preventDefault();
    window.close();
  });

  function toggleEdit(card) {
      $(card).find('.edit-widget-btn i').toggleClass('pencil tick');
      let $wbData = $(card).children('.wb-data');
      $wbData.find('ul').toggle();
      $wbData.find('.edit-widget').toggle();
      autosize();
  }

  $('.edit-widget-btn').on('click', function() {
      let $card = $(this).parent().parent();
      if ($('.oe-i',this).hasClass('tick')) {
          let cardTitle = $(this).parent().text().trim();
          let $cardContent = $($card).find('.wb-data');
          let whiteboardEventId = this.dataset.whiteboardEventId;
          let data = {};
          let contentId;
          let whiteboardComment;

          contentId = (cardTitle === 'Equipment') ? 'predicted_additional_equipment' : cardTitle.toLowerCase();
          whiteboardComment = $cardContent.find('textarea').val();
          data[contentId] = whiteboardComment;
          data.YII_CSRF_TOKEN = YII_CSRF_TOKEN;
          // Save the changes made.
          $.ajax({
              'type': 'POST',
              'url': '/OphTrOperationbooking/whiteboard/saveComment/' + whiteboardEventId,
              'data': data,
              'success': function () {
                  let newContent = whiteboardComment.split("\n");
                  $cardContent.find('ul').empty();
                  newContent.forEach(function(item) {
                      $cardContent.find('ul').append('<li>' + item + '</li>');
                  });
                  toggleEdit($card);
                  window.onbeforeunload = null;
              },
              'error': function () {
                  alert('Something went wrong, please try again.');
              }
          });
      } else {
          toggleEdit($card);
      }
  });

    let toolTip = new OpenEyes.UI.Tooltip({
        className: 'quicklook',
        offset: {
            x: 10,
            y: 10
        },
        viewPortOffset: {
            x: 0,
            y: 32 // height of sticky footer
        }
    });
    $(this).on('mouseover', '.has-tooltip', function() {
        if ($(this).data('tooltip-content') && $(this).data('tooltip-content').length) {
            toolTip.setContent($(this).data('tooltip-content'));
            let offsets = $(this).offset();
            toolTip.show(offsets.left, offsets.top);
        }
    }).mouseout(function () {
        toolTip.hide();
    });


});