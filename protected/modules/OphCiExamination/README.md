OphCiExamination
================

This module consists of many different elements that are optionally available. The initial set of elements available can be configured for each sub specialty.

Dependencies
------------

1. Eyedraw

Configuration
-------------

This module is namespaced. as a result setting the module config in core should read as follows:

'OphCiExamination' => array(
	'class' => '\OEModule\OphCiExamination\OphCiExaminationModule',
),

DR Grading
----------
This element will optionally have a link to an image popup of sample posterior poles for DR. This image must be provided by the user of the system, and should be copied to:

[moduledir]/assets/img/drgrading.jpg

The image size is expected to be 452 x 699
