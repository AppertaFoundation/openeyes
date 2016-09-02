document.addEventListener("DOMContentLoaded", function() {
  OpenEyes.Dialog.init(
    document.getElementById('dialog-container'),
    document.getElementById('refresh-button'),
    'Are you sure?',
    'This will update the record to match the current status of the patient. If you are unsure do not continue.'
  );

  $('#exit-button').on('click', function (event) {
    event.preventDefault();
    window.close();
  });

  var $commentCard = $('#comment-card');
  $commentCard.find('.material-icons').on('click', function(){
    var $cardContent = $commentCard.find('.mdl-card__supporting-text');
    var icon = this;
    var whiteboardEventId = icon.dataset.whiteboardEventId;
    var textArea;

    if(icon.textContent !== 'done'){
      textArea = $('<textarea />');
      icon.textContent = 'done';
      textArea[0].value = $cardContent.get(0).textContent.trim();
      $cardContent.html(textArea);
    } else {
      var comments = $cardContent.find('textarea').val();
      $.ajax({
        'type': 'POST',
        'url': '/OphTrOperationbooking/whiteboard/saveComment/' + whiteboardEventId,
        'data': {comments: comments, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
        'success': function () {
          $cardContent.text(comments);
          icon.textContent = 'create';
        },
        'error' : function() {
          alert('Something went wrong, please try again.');
        }
      });
    }
  });
});