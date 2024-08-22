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

This enables strict definition of the parameters required for the event to be dispatched. Dispatching is performed through the static `dispatch` accessor, the parameters of which are passed through to the `*SystemEvent` constructor:

```
FooSystemEvent::dispatch(true);

FooSystemEvent::dispatch(false, 5);
```

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

### Testing

#### Events

It's possible to prevent events being dispatched, and then assert that they would have been dispatched in the normal run of things. This provides a mechanism to sanity check that events are setup for dispatching correctly when actions take place. For testing convenience, this is wrapped up in a single testing trait that provides some standardised assertions

```
class ASystemEventTest extends OEDbTestCase
{
    use HasSysEventAssertions;

    /** @test */
    public function check_event_with_properties_dispatched_state()
    {
        $this->fakeEvents();

        // carry out some actions

        $this->assertEventDispatched(ExpectedEvent::class);
        $this->assertEventNotDispatched(UnexpectedEvent::class);

        $this->assertEventDispatched(ConditionalEvent::class, function (ConditionalEvent $event) {
            return $event->truthyProperty === true;
        }

        $this->assertEventNotDispatched(ConditionalEvent::class, function (ConditionalEvent $event) {
            return $event->truthyProperty === false;
        }
    }

    /** @test */
    public function check_specific_event_dispatched()
    {
        $this->fakeEvents([EventUnderTest::class]);

        // actions that dispatches several events
        $this->assertEventDispatched(EventUnderTest::class); // passes
        $this->assertEventDispatched(EventNotFaked::class); // fails as would not be tracked
    }
}
```

#### Listeners

The Manager uses the `ListenerBuilder` factory class to instantiate and invoke listeners that are triggered by events. In turn, this factory class supports faking of the classes that it is asked to build. This pattern is intended to provide support for ensuring that listeners have been configured for events correctly:

```
/** @test */
public function listener_is_triggered_for_event()
{
    $mock_listener = $this->createMock(ListenerToTest::class);
    $mock_listener->expects($this->once())
        ->method('__invoke');

    ListenerBuilder::fakeWith(ListenerToTest::class, $mock_listener);

    SystemEventThatShouldTriggerListener::dispatch();
}
```

For convenience, this behaviour is wrapped up in a test_trait in this module:

```
class ListenerTest extends OEDbTestCase
{
    use HasSysEventListenerAssertions;

    /** @test */
    public function ensure_listener_is_invoked()
    {
        $this->expectListenerToBeInvoked(ExpectedListener::class);

        EventToTriggerListener::dispatch();
    }

    /** @test */
    public function listener_with_custom_method_is_called()
    {
        $this->expectListenerWithMethod(
            CustomExpectedListener::class,
            'aMethod',
            function (...$args) {
                return $args[0] === true;
            }
        );

        EventToTriggerCustomListener::dispatch();
    }
}

To test the listener behaviour itself, the class should instantitiated and called with the SystemEvent it's expecting to be triggered by.

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
