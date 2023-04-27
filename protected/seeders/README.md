# Seeders

Seeders have been added to the project to allow the abstraction of generating data for end to end testing (at the time of implemention, these are run through Cypress). Seeders are created in the modules that are closest to the functionality that is being tested, and are invokable classes that have access to an application context for seeding data through the use of factories.

When possible, factories should be used directly to keep tests simple, but when test cases require more complexity, seeders allow a single PHP call to perform DB setup, rather than multiple (slower) requests.

## Application Context

The `SeederBuilder` will generate the appropriate `ApplicationContext` to be passed to the seeder. If a valid Http-based `OESession` exists, it will use that as the basis for its defaults, but they can be overridden. The lack of a valid session is currently not handled gracefully, and will simply lead to a PHP runtime error due to invalid property assignment. In the future this should throw a more meaningful exception.

## Resources

A basic DTO pattern has been defined for models in the `OE\seeders\resources` namespace. This is to provide convenient conversion of seeded models to arrays etc, which seeders can use to generate the structure of their output:

```
public function __invoke(): array
{
    $model = ExampleModel::factory()->create();

    return [
        'created_model' => GenericModelResource::from($model)->toArray()
    ];
}
```