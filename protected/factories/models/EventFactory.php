<?php

namespace OE\factories\models;

use DateTime;
use Episode;
use Event;
use Firm;
use OE\factories\exceptions\CannotMakeModelException;
use OE\factories\exceptions\FactoryNotFoundException;
use OE\factories\ModelFactory;
use Patient;

class EventFactory extends ModelFactory
{
    protected static ?array $availableEventTypes = null;
    protected array $elementsWithStates = [];

    public static function forModule(string $moduleName)
    {
        $factoryName = "\\{$moduleName}Factory";
        if (class_exists($factoryName, false)) {
            return $factoryName::new();
        }

        $namespacedFactoryName = "\\OEModule\\{$moduleName}\\factories{$factoryName}";
        if (class_exists($namespacedFactoryName)) {
            return $namespacedFactoryName::new();
        }

        throw new FactoryNotFoundException("No Event Factory found for {$moduleName}");
    }

    public function definition(): array
    {
        return [
            'episode_id' => ModelFactory::factoryFor(Episode::class),
            'event_type_id' => $this->faker->randomElement($this->availableEventTypes()),
        ];
    }

    public function withElement(string $element_cls, array $states = []): self
    {
        if (array_key_exists($element_cls, $this->elementsWithStates)) {
            array_push($this->elementsWithStates[$element_cls], ...$states);
        } else {
            $this->elementsWithStates[$element_cls] = $states;
        }

        return $this;
    }

    public function withElements(array $elements_with_states = []): self
    {
        foreach ($elements_with_states as $element_with_states) {
            if (!is_array($element_with_states)) {
                $element_with_states = [$element_with_states];
            }
            $this->withElement(...$element_with_states);
        }

        return $this;
    }

    public function configure()
    {
        return $this->afterMaking(function ($event) {
            if ($event->institution_id) {
                return;
            }
            // base the institution on the firm or the episode for the event
            // if the episode doesn't have a firm, this will still fail
            $event->institution_id = $event->firm
                ? $event->firm->institution_id
                : ($event->episode->firm ? $event->episode->firm->institution_id : null);
        })->afterCreating(function (Event $event) {
            // Would be good to set these elements on the event to allow the getElements
            // method to return them directly, rather than needing to go to the db again.
            foreach ($this->elementsWithStates as $element_class => $states) {
                $element_factory = ModelFactory::factoryFor($element_class);
                foreach ($states as $state) {
                    if (!is_array($state)) {
                        $state = [$state];
                    }

                    $element_factory->{$state[0]}(...array_slice($state, 1));
                }

                $element_factory->create(['event_id' => $event->id]);
            }
        });
    }

    public function forPatient(Patient $patient): self
    {
        return $this->state(function ($attributes) use ($patient) {
            if ($attributes['episode_id'] instanceof ModelFactory) {
                $attributes['episode_id'] = $attributes['episode_id']->forPatient($patient);
            } else {
                $attributes['episode_id']->patient_id = $patient->id;
            }

            return [
                'episode_id' => $attributes['episode_id']
            ];
        });
    }

    public function forFirm(Firm $firm): self
    {
        return $this->state(function ($attributes) use ($firm) {
            if ($attributes['episode_id'] instanceof ModelFactory) {
                $attributes['episode_id'] = $attributes['episode_id']->ForFirm($firm);
            } else {
                $attributes['episode_id']->firm = $firm->id;
            }

            return [
                'episode_id' => $attributes['episode_id'],
                'firm_id' => $firm->id
            ];
        });
    }

    public function forFirmWithName(string $firmName)
    {
        return $this->state(function ($attributes) use ($firmName) {
            if (!($attributes['episode_id'] instanceof ModelFactory)) {
                throw new CannotMakeModelException('forFirmName state only applicable when generating an episode for the event.');
            }

            return [
                'episode_id' => $attributes['episode_id']->forFirmWithName($firmName),
                'firm_id' => Firm::factory()->useExisting([
                    'name' => $firmName
                ])
            ];
        });
    }

    public function onEventDate(DateTime $date): self
    {
        return $this->state(function () use ($date) {
            return [
                'event_date' => $date->format('Y-m-d')
            ];
        });
    }

    public function monthsAfter(DateTime $date, int $months = 1, $range = '+10 days')
    {
        $startDate = $date->add(new \DateInterval("P{$months}M"));
        return $this->state(function () use ($startDate, $range) {
            return [
                'event_date' => $this->faker->dateTimeInInterval($startDate, $range)->format('Y-m-d')
            ];
        });
    }

    public function between($from, $until)
    {
        return $this->state(function () use ($from, $until) {
            return [
                'event_date' => $this->faker->dateTimeBetween($from, $until)->format('Y-m-d')
            ];
        });
    }

    public function forEventTypeWithName($eventTypeName)
    {
        return $this->state(function ($attributes) use ($eventTypeName) {
            return [
                'event_type_id' => $this->getEventTypeByName($eventTypeName)
            ];
        });
    }

    public function modelName()
    {
        // override to allow child factories to always instantiate the correct base model
        return \Event::class;
    }

    protected function getEventTypeByName($eventTypeName)
    {
        if (static::$availableEventTypes === null) {
            $this->cacheAvailableEventTypes();
        }

        return static::$availableEventTypes[$eventTypeName];
    }

    protected function availableEventTypes()
    {
        if (static::$availableEventTypes === null) {
            $this->cacheAvailableEventTypes();
        }

        return array_values(static::$availableEventTypes);
    }

    protected function cacheAvailableEventTypes()
    {
        $cache = [];
        foreach (\EventType::model()->findAll() as $eventType) {
            $cache[$eventType->name] = $eventType;
        }

        static::$availableEventTypes = $cache;
    }
}
