# 5. Subscribable events must be consistently defined and applied across the system

Date: 2022-11-09

## Status

Accepted

## Context

As the complexity of the OpenEyes application has increased, more events are being created to allow other components to hook into changes and carry out further processing. The approach to this has been inconsistent, leading to a lack of clarity in the system,

## Decision

All events that can be subscribed to must be well defined as class instances. Similarly, all subscribers (listeners) must be implemented as invokable classes that can act on these events.

## Consequences

1. The new OEModule\OESysEvents module has been introduced to define and support this pattern.
1. This has been implemented to support the legacy pattern of strings being dispatched with an arbitary number of parameters.

## Risks

1. There is a risk that the old events will not be refactored to support the new pattern. This in turn risks future unaware development following the old pattern rather than this more robust new pattern.
1. An effort has been made to make clear the distinction between system events and clinical events, but this may still prove unwieldy to the uninitiated.
