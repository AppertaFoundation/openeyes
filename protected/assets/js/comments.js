
$(function () {
  $(this).on('click', '.js-add-comments', function () {
    $(this).css('visibility', 'hidden');
    var $container = $($(this).data('comment-container'));
    $container.show();
    $container.find('.js-comment-field').focus();
  });

  $(this).on('blur', '.js-comment-field', function () {
    if ($(this).val().trim() === '') {
      var $container = $(this).closest('.js-comment-container');
      var $button = $($container.data('comment-button'));
      $button.css('visibility', 'visible');
      $container.hide();
    }
  });

  $(this).on('click', '.js-remove-add-comments', function () {
    var $container = $(this).closest('.js-comment-container');
    $container.find('.js-comment-field').val(null);
    $container.hide();
    var $button = $($container.data('comment-button'));
    $button.css('visibility', 'visible');
  });
});
