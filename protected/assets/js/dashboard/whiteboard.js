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
  })
});