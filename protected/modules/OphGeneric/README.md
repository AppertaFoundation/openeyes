# Generic Event

**Note** This README has been added long after the initial implementation of the module, and therefore may not be complete.

## Manual events

Although originally this event was solely being populated automatically through a combination of the `API` module and the payload processor, it has been extended to support the manual entry of data for Visual Fields. The way in which this has been implemented is intended to be support future requirements, but for now this specifically allows the recording of `OEModule\OphGeneric\models\HFA` elements in an event. This is configured through admin configuration where a manual event subtype can be associated with the supported elements.

The element types that are available for selection in this administration are defined as a constant on the admin controller, and this should be added to when additional elements are updated to support manual data entry.

Note there is some small duplication with the encapsulation of editable element types in the `EventManager`

## EventManager

In an effort to encapsulate the behaviour of the different events that can be recorded, we have added the `OphGeneric\components\EventManager` which is designed to work out whether a specific event is manual or automatic (imported by payload processor) and therefore render the event differently for editing and viewing.
