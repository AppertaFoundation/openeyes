# OpenEyes Testing

The base testing classes and traits (see `test-traits`) are intended to provide appropriate abstractions to common testing requirements in the application.

Typically, most tests for models should inherit from the `ModelTestCase` class. This extends the `ActiveRecordTestCase` to provide a couple of additional default tests.

(Note, it may prove appropriate for these two be merged, but they remain separated for now as they have been developed in parallel).

`ActiveRecordTestCase` in turn, inherits from `OEDbTestCase`, which is the recommended base class for tests. This provides support for initialising support traits for your test cases, and the ability to build out/tear down temporary tables for testing.

Developers are encouraged to continue adding to the library of useful abstractions for tests, and refactoring where appropriate if improvements can be made.

## Testing Traits

`OEDbTestCase` inspects the test class to determine which traits are being used. If those traits have a setup method, this will be run as part of the setup for each test. The setup method should be called setup[TraitName]. For example, `WithTransactions` has a `setupWithTransactions` method.

### `WithTransactions`

This trait wraps each test case insides its own transaction. This enables tests to be run against a database without permanenent effect on that database, which is extremely useful during development to avoid messing with your development setup.

Important note, this trait cannot be used with fixtures. It will fail the tests if this conflict arises. The reason for this is because the way yii loads fixtures causes an implicit commit to the database, which prevents us from containing the tests in a transaction.

### `WithFaker`

This trait initialises a `Faker` instance (see https://github.com/fzaninotto/Faker) to allow the generation of random sample data in your tests.
