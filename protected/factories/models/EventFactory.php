<?php

namespace OE\factories\models;

use DateTime;
use Episode;
use OE\factories\exceptions\FactoryNotFoundException;
use OE\factories\ModelFactory;
use OE\factories\models\traits\HasFirm;

class EventFactory extends ModelFactory
{
    use HasFirm;

    protected static ?array $availableEventTypes = null;

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
            'event_type_id' => $this->faker->randomElement($this->availableEventTypes())
        ];
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
