# 6. Reference data lookup filtering should be implemented in a consistent fashion

Date: 2023-02-21

## Status

Accepted

## Context

OpenEyes has a large number of Reference Data (lookup) models. In a large number of cases, the options that should be presented from these lookups varies depending on the context within which the application is running. Installation, Institution, Specialty, Subspecialty etc may all be used to constrain the final list of options.

This has been implemented in a number of different ways, leading to inconsistency in the way the filtering of options is performed, and in the way the options are administered.

## Decision

Reference data behaviour should be consistently implemented. This will be acheived by attaching specific traits to the Reference Data models, and defining options on those models to affect the behaviours surfaced by those traits.

## Consequences

Two traits are defined: `MappedReferenceData` and `OwnedReferenceData`. Attaching these traits to models allows these Reference Data models to be called:

```
MappedModel::model()->findAllAtLevels(ReferenceData::LEVEL_ALL);
OwnedModel::model()->findAllAtLevels(ReferenceData::LEVEL_ALL);
```

More detailed documentation is [provided here](https://openeyes.atlassian.net/l/cp/adzNjQoq)

## Limitations

The initial implementation still requires that the calling code is aware of the level by which the model should be filtered. In the future, we should implement:

```
public function findAllForContext(?DataContext $context = null)
{
    $context ??= DataContext::getCurrent();
    // magic resolution of relevant attributes for data retrieval
}
```

## Risks

1. Abstractions can lead to complexity in the generated queries, and may make optimisation more challenging.
1. The volume of legacy reference data implementations will make it challenging to push the adoption of this pattern in the project.
