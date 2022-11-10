# OE System Event Module

This module provides the infrastructure abstractions for triggering and subscribing to published system events within OpenEyes.

## Basic setup

There are 3 key elements for events to be triggered and handled:

### Defining a system event

An abstract class of `OEModule\OESysEvent\events\SystemEvent::class` has been defined, and any event that is to be dispatched can inherit from this:

```
class FooSystemEvent extends SystemEvent
{
    public bool $bar;
    public int $count;

    public function __construct(bool $bar, int $count = 0)
    {
        $this->bar = $bar;
        $this->count = $count;
    }
}
```

This enables strict definition of the parameters required for the event to be dispatched. Dispatching can be triggered thus:

```
FooSystemEvent::dispatch(true);

FooSystemEvent::dispatch(false, 5);
```

As and when we shift to PHP 8, we will be able  to do:

Note that for clarity, system event classnames should explicitly be postfixed with `SystemEvent`

### Defining a listener (subscriber)

Listeners should be defined as an invokable class that receives the event

```
class FooListener
{
    public function __invoke(FooSystemEvent $event)
    {
        OELog::log("it's " . ($event->bar ? "true" : "false") . " {$event->count} times!");
    }
}
```

### Linking the two

At the moment we leverage standard Yii configuration on the event component:

```
    'components' => [
        'event' => {
            'observers' => [
                [
                    'system_event' => FooSystemEvent::class
                    'listener' => FooListener::class,
                ]
            ]
        }
    ]
```

### Additional notes

To ensure clarity, a distinction must be made between the standard OpenEyes `Event` class and system events. As such, any `Event` variables should be described as a clinical event. Any system events must also be labelled as such:

No:

```
class EventTouchedEvent extends SystemEvent
```

yes:

```
class ClinicalEventTouchedSystemEvent extends SystemEvent
```

## Future development

### Removal of legacy support

Once legacy events have been removed from the application, we can clean up the `Manager` implementation to enforce the use of class based events.

### Webhook event listener configuration

Providing a mechanism in configuration for one or more webhook to be defined to receive system event notifications. This would allow a 3rd party to then call OpenEyes API endpoints to retrieve state that would have been affected by the system event.

### System event broadcasting

Using websockets through something like soketi.io we would be able to use system events to trigger UI updates in OpenEyes.

### Event faking

Infrastructure to help support testing of events being triggered.