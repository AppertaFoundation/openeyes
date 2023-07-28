# 7. Use a Yii application parameter to override setting of HTTP headers for PHPUnit tests

Date: 2023-07-27

## Status

Accepted

## Context

When PHPUnit is running, it writes to standard output before any tests begin such that any calls to the PHP `header` function will fail and cause an error.

This was affecting tests that use the `MakesApplicationRequests` trait to make requests to end points that send responses in JSON format, because they call `header` with a JSON mime type, so they instead receive a response with the error for making that function call.

## Decision

To implement a very simple solution that bypasses the call to `header` in a PHPUnit context but to still check that the appropriate header is being sent, a callback function is passed as a Yii application parameter called `header_wrapper_callback`. It accepts one argument, which is the one that normally would be provided to `header`.

The parameter is set before the call to the `run` method for the application and unset after the use of the output buffer has ended. Two methods inside `MakesApplicationRequests` handle this.
`RenderJsonTrait` has a method that will either call `header` normally, or if `header_wrapper_callback` is set, the callback passed in that application parameter instead.

## Consequences

1. This enables testing of application end points that send responses with differing content types or other header values without erroring out due to trying to set a header after output has already begun ot be written
1. The values that would normally be passed to `header` are stored and sent back to the `ApplicationResponseWrapper` which permits testing on the presence/absence of those values
1. Operating this via a Yii application parameter is not the most graceful way of going about overriding the use of `header`.
1. Care has to be taken to ensure the parameter does not persist across tests or that it is used outside of testing.
