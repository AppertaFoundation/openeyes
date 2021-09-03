OphCiExamination
================

This module consists of many different elements that are optionally available. The initial set of elements available can be configured for each sub specialty.

## Dependencies

1. Eyedraw

## Configuration

This module is namespaced. as a result setting the module config in core should read as follows:

'OphCiExamination' => array(
	'class' => '\OEModule\OphCiExamination\OphCiExaminationModule',
),

### DR Grading
This element will optionally have a link to an image popup of sample posterior poles for DR. This image must be provided by the user of the system, and should be copied to:

[moduledir]/assets/img/drgrading.jpg

The image size is expected to be 452 x 699

### Visual Acuity

Both near and standard have a "simple" and "complex" record mode. The default selection for this can be configured through settings.

### Nine Positions

A number of attributes can disabled through configuration. See the widget for the parameters involved.

## Development Guidelines

The Strabismus implementation work has afforded an opportunity to try to update the standards employed for element implementation. This is extending the work previously done to introduce widgets to manage the elements. Unit testing has been a signficant focus during this implementation as well.

### Use of Traits

Common functionality should be abstracted where possible. Yii behaviours offer no benefits over those of the standard PHP trait approach, and therefore traits are preferred. Examples of these are `HasCorrectionType` and `HasWithHeadPosture`.

It should be noted that the `HasRelationOptions` has been abstracted from `SocialHistory`.

### Minimise Code in templates

Where possible, business logic etc should be managed either in the model or in the widget.

### Enforcement of relation rules

Validation rules should be implemented to protect the database, and not assume that the source of data can be trusted to only offer valid values. In particular the `exists` rule is very important for this.

### Don't model input requirements in models

Any conversion of input values to underling model structures should be managed by widgets.

### Testing

#### PHP

The unit tests are typically possible to write without touching the database. A TDD approach to development is recommended to ensure a decent level of coverage (no one goes back and writes tests afterward).

Be aware of the utility in abstracting common testing patterns into re-usable traits and/or abstract classes that can be inherited from.

#### Javascript

Currently outside the scope of automated testing. But End to End testing should cover the basics ...

#### Katalon Tests

Write them, and be willing to adjust the HTML for elements to support the smooth running of these tests. They are a vital part of maintaining stability in a large, fragmented, legacy-filled codebase such as OpenEyes.

### Javascript/Adder Dialog

The adder dialog behaviour should be generated from the form for the element. Duplicate loading of options etc should be avoided. An abstraction of this has been developed as `OpenEyes.UI.ElementController`. A simple example of this can be seen in Convergence and Accommodation, whilst a more complex implementation that supports multiple entries can be seen with Stereo Acuity.

### Naming conventions

Endeavour to ensure method names are clearly descriptive of their purpose. Where possible, keep methods to a minimum length and abstract logic into methods that describe the purpose of the code. This greatly aids readability of the code.

Public methods should be at the top of classes, followed by protected, and finally private.

### Using classnames

Please always use \foo::class for deriving classname rather than "\\foo". The former can be tracked by IDEs and more readily support refactoring.

The only exception is in migrations. If a class name is changed at a later stage, using the original classname in the migration would cause a failure of that migration, and might cause conditional failures in later migrations.
