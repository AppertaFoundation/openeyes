# 4. Standardise on native datepicker functionality with a consistent widget

Date: 2022-09-06

## Status

Accepted

## Context

OpenEyes has used several different approaches to providing UIX for selecting dates in the application. This is a result of the long running nature of the application, and the fact that native support for dates was extremely poor when the application began. As [per IDG](https://idg.knowego.com/gui/ui/ds/dates/), native browser date picker functionality has now moved on sufficiently that this should be used as the de-facto standard for entering dates.

## Decision

It's not possible to make a change to all fields in the application in one go. As such, a new widget should be implemented that specifically uses native browser functionality. As date fields are added, or UI with date fields are changed, this widget should be adopted.

## Consequences

1. Inline date fields, jqueryui datepickers and the original `protected/widgets/DatePicker` should no longer be used.
1. `protected/widgets/DatePicker` should be marked as deprecated.
1. The new widget `protected/widgets/DatePickerNative` should be adopted for all date fields.
