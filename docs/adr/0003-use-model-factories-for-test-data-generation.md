# 3. use model factories for test data generation

Date: 2022-07-25

## Status

Accepted

## Context

When writing tests, it's often necessary to generate instances of data models with realistic data in them. Adopting a consistent and easily extensible means of doing so will make expanding test coverage simpler. 

The system may be tested in several different ways, and there are other circumstances under which being able to programmatically generate sample data will be useful - such as populating a demo system. 

As such, the means by which data can be generated should not be dependent on the testing framework itself.

## Decision

We will have a standard `Factory` pattern through which a model instance can be populated and optionally saved to the database. The means of using and extending this will be documented within the application to ensure that the information remains close to the code, and easier for developers to reference.

## Consequences

1. As test coverage is expanded, it will be expected that additional `ModelFactory` classes are defined for the elements that are under tests. 
1. Older approaches to the problem (the `InteractsWith*` trait pattern that was adopted for Strabismus tests in the Examination module) should be refactored when test changes become necessary.
1. Additional helper functions around data generation should be written when common patterns arise during testing.
