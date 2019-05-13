
$(function () {
  $(this).on('click', '.js-add-comments', function () {
    if($(this).data('hide-method') === 'display') {
        $(this).hide();
    } else {
        $(this).css('visibility', 'hidden');
    }
    var $container = $($(this).data('comment-container'));
    $container.show();
    $container.find('.js-comment-field').focus();
  });

    $(this).on('blur', '.js-comment-field', function () {
      if ($(this).val().trim() === '') {
        var $container = $(this).closest('.js-comment-container');
        var $button = $($container.data('comment-button'));
        if ($(this).data('hide-method') === 'display') {
          $button.show();
          $container.hide();
        } else {
          $button.css('visibility', 'visible');
          $container.hide();
        }
      }
    });

  $(this).on('click', '.js-remove-add-comments', function () {
    var $container = $(this).closest('.js-comment-container');
    $container.find('.js-comment-field').val(null);
    $container.hide();
    var $button = $($container.data('comment-button'));
      if($button.data('hide-method') === 'display') {
         $button.show();
      } else {
          $button.css('visibility', 'visible');
      }
  });
});
