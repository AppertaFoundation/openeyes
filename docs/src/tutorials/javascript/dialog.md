A dialog is a general purpose component that is used for displaying information
to the user that is overlayed over the page content. A dialog can also be used
for user input or confirmation.

***

### Basic usage

To create a new dialog you need to create an instance of it:

	new OpenEyes.Dialog();

The dialog constructor accepts an options object which contains information
to be displayed within the dialog:

	new OpenEyes.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	});

A new instance of a dialog will create the HTML elements in the DOM and will not
be displayed. If you want to display the dialog, you need to use the `open` method:

	new OpenEyes.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	}).open();

Sometimes it's useful to store a reference to the dialog instance, so you can
open or close it at different times:

	var dialog = new OpenEyes.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	});
	dialog.open();

By default, the dialog will destroy itself when closed, thus you need to need to
specify the [destroyOnClose]{@link Dialog#_defaultOptions} option to be `false` if you want to keep the dialog
in the DOM after it has been closed.

	var dialog = new OpenEyes.Dialog({
		title: 'This is a title',
		content: 'This is some content',
		destroyOnClose: false
	});
	dialog.open();