# OE System Event Module

This module provides the infrastructure abstractions for triggering and subscribing to published system events within OpenEyes.

## Basic setup

There are 3 key elements for events to be triggered and handled:

### Defining a system event

An abstract class of `OEModule\OESysEvent\events\SystemEvent::class` has been defined, and any event that is to be dispatched can inherit from this:

```
class FooEvent extends SystemEvent
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
FooEvent::dispatch(true);

FooEvent::dispatch(false, 5);
```

As and when we shift to PHP 8, we will be able  to do:

```
class FooEvent extends SystemEvent
{
    public function __construct(public bool $bar, public int $count = 0)
    {
    }
}
```

### Defining a listener (subscriber)

Listeners should be defined as an invokable class that receives the event

```
class FooListener
{
    public function __invoke(FooEvent $event)
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
                    'listener' => FooListener::class,
                    'event' => FooEvent::class
                ]
            ]
        }
    ]
```
