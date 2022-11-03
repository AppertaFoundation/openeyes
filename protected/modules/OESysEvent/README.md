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
                    'event' => FooSystemEvent::class
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