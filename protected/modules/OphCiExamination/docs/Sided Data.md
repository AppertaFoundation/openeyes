# Sided Data in Examination

During the implementation of Strabismus functionality, the limitations of the current original implementation for tracking sided data with the `SplitEventTypeElement` base class became apparent.

There were two specific problems with this base class:

1. Could not support additional "sides".
2. Only supported element level data, not reading level data.

This first issue was a problem for the introduction of BEO readings for Visual Acuity, whilst the second introduces issues for Contrast Sensitivity.

## Supporting BEO

The `SplitEventTypeElement` tracks what side data has been recorded for using the `eye_id` attribute. This is set to `Eye::LEFT`, `Eye::RIGHT` or `Eye::BOTH`, depending on whether there is data for the right side, left side, or both sides.

By a convenient coincidence (or undocumented design choice) the primary key values for the different eye options correspond to using Bitwise operations for the side values:

```php
// 1 | 2 === 3;
Eye::LEFT | Eye::RIGHT === Eye::BOTH;
```

As such, introducing a new constant to the element to represent BEO would then allow bitwise operators to be used to define what should be recorded, and to determine what has been recorded:

`Eye::BEO = 4;`

However, it was determined that this should not be introduced on the `Eye` model, as this might lead to unintended consequences, or at least a widening of scope of the Strabismus implementation.

## Consistency for sided data

Another issue that exists with VA is that the reading values record what eye a reading is for with different numeric values. With scope limitations in mind, that has not yet been addressed. However, any new elements or records referencing side should be consistent.

As such, the introduction of abstract code to support this was deemed the most appropriate design choice.

## The Components

As touched upon in the introduction, the `SplitEventTypeElement` does not support multiple reading sided data. Using composition of components provides for better support for different scenarios:

**Interfaces**

The interfaces provide the constant definitions for the concrete side values, and a couple of methods that should be implemented in the data specific implementation.

**Traits**

The traits provide the core components of behaviour that are required to support the actual recording of the sided data.

### SidedData

```OEModule\OphCiExamination\models\interfaces\SidedData```

```OEModule\OphCiExamination\models\traits\HasSidedData```

### BEOSidedData

```OEModule\OphCiExamination\models\interfaces\BEOSidedData``` - inherits from `SidedData`

```OEModule\OphCiExamination\models\traits\HasBEOSidedData``` - uses `HasSidedData`

### JS

The expectation is that the required `eye_id` attribute that is recorded on the element will be provided through the form for the data element. The widget js controller for VisualAcuity provides an example of how that can be implemented within the context of the UI.