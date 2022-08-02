# OpenEyes Testing

## PHP

PHP Tests fall into two groups of tests, those that rely on fixtures, and those that rely on the sample database having been populated.

All new tests should be written to work with the sample database, as this allows developers to run the tests locally and swiftly without intefering with their development environment.

## Sample data

To run these:

`oe-unit-tests --group=sample-data`

### Writing tests

All tests should be tagged with the `@group sample-data` and other tags that will allow easy subset testing.

test instances of models etc should be built using the [model factory](./modelfactories.md) approach.
