### Overview

An event emitter is an object which can be used to emit events. An event
is identified by a name, and is handled by a handler function. Most user
interface components should inherit from the Emitter class.

***

### Basic usage

To create a new event emitter, you need to create an instance of it:

    var emitter = new OpenEyes.Util.EventEmitter();

Now you can bind events onto it using the [on]{@link Emitter#on} method:

    emitter.on('event1', function() {
        alert('Event 1 was emitted!')
    });
    emitter.on('event2', function() {
        alert('Event 2 was emitted!')
    });

You would use the [emit]{@link Emitter#emit} method to call the handlers:

    emitter.emit('event1'); // will alert 'Event 1 was emitted'
    emitter.emit('event2'); // will alert 'Event 2 was emitted'

You can remove handler/s for a event using the [off]{@link Emitter#emit} method:

    emitter.off('event1');
    emitter.emit('event1') // will do nothing

### Handling events via callbacks

The emitter object expects that it will be inherited, and thus will do it's best
to call callback functions when events are emitted. It will look for callback
functions within the `options` property of the instance, in the format of 'onEventName'.
(If event name is 'myEvent', then it expects the callback handler to be 'onMyEvent'.)

For example:

    var myObject = function() {
        this.options = {
            onMyEvent: function() {
                alert('MyEvent Called!')
            }
        };
    };
    MyObject.prototype = Object.create(Emitter.prototype);

    var obj = new MyObject();
    obj.emit('myEvent');