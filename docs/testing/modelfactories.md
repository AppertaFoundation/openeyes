# Database Factories

Database factories have been introduced to simplify the process of generating data in the context of testing.

## Model Factories

The `OE\factories\ModelFactory` class provides the means to generate valid test instances of models in OpenEyes. The intent is that it provides a simple interface to be able to write code like the following:

```
$patient = ModelFactory::for(Patient::class)->create();
$this->assertNotNull($patient->dob);

$patient = ModelFactory::for(Patient::class)->create(['dob' => '2005-03-05']);
$this->assertEquals(10, $patient->ageOn('2015-03-05'));
```

It also offers the `make` method, which will populate model attributes without saving the record

```
$patient = ModelFactory::for(Patient::class)->make();

$this->assertNull($patient->id);
```

As different models are tested, the factories for them should be implemented, expanding the available models that can be automatically generated and tested.

## Event Factories

Event Factories should be defined for each `EventType` module, allowing a valid event to be generated for that module. These can then be created:

```
$event = EventFactory::for('OphCoCvi')->create();
$this->assertTrue($event->patient instanceof Patient::class);
```

These are dependent on the appropriate model factories being built for the elements that exist in the module.

## Factory dependencies

The dependent relations for a factory should be defined as calls to the factory for the class of that relationship:

```
class PatientFactory extends ModelFactory
{
    public configure()
    {
        return [
            'contact_id' => ModelFactory::for(Contact::class)
        ];
    }
}
```

## States

Multiple states can be defined on any factory to define what else should be created:

```
class PatientFactory extends ModelFactory
{
    public function male()
    {
        return $this->state(function () {
            return [
                'gender' => 'M',
                'contact_id' => ModelFactory::factoryFor(Contact::class)->male()
            ];
        });
    }
}
```

To apply a state:

    $patient = ModelFactory::for(Patient::class)->male()->create();

Multiple states can be chained:

    $patient = ModelFactory::for(Patient::class)->male()->female()->create();

In this example, the patient will be female, because it will superseed the attributes applied by the `male` state
