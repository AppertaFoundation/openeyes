### Overview

A dialog is a general purpose component that is used for displaying information
to the user that is overlayed over the page content. A dialog can also be used
for user input or confirmation.

***

### Basic usage

To create a new dialog you need to create an instance of it:

	new OpenEyes.UI.Dialog();

The dialog constructor accepts an options object which contains information
to be displayed within the dialog:

	new OpenEyes.UI.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	});

A new instance of a dialog will create the HTML elements in the DOM and will not
be displayed. If you want to display the dialog, you need to use the {@link Dialog#open}
method:

	new OpenEyes.UI.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	}).open();

Sometimes it's useful to store a reference to the dialog instance, so you can
open or close it at different times:

	var dialog = new OpenEyes.UI.Dialog({
		title: 'This is a title',
		content: 'This is some content'
	});
	dialog.open();

By default, the dialog will destroy itself when closed. If you don't want the
dialog destroyed when closed, you need to specify the [destroyOnClose]{@link Dialog#_defaultOptions}
option to be `false`.

	var dialog = new OpenEyes.UI.Dialog({
		title: 'This is a title',
		content: 'This is some content',
		destroyOnClose: false
	});
	dialog.open();

***

### Events

The dialog extends the [EventEmitter]{@link Emitter} class and emits events
which you can bind to. You use the {@link Dialog#on} method to bind event handlers:

    var dialog = new OpenEyes.UI.Dialog({
        title: 'This is a title',
        content: 'This is some content'
    });
    dialog.on('open', function() {
        console.log('Dialog is opened');
    });
    dialog.on('close', function() {
        console.log('Dialog is closed');
    });
    dialog.open();

#### Available events

* [open]{@link Dialog#event:open}
* [close]{@link Dialog#event:close}
* [destroy]{@link Dialog#event:destroy}