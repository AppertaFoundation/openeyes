# Internalreferral

An OpenEyes event module to track internal referrals within the institution

## Configuration

Add the module to the application configuration:

    'Internalreferral' => array('class' => '\OEModule\Internalreferral\InternalreferralModule')

### Integration

A separate integration config file should be setup in the modules config path to setup any integration component for the module. A sample file is provided which includes the details required for this.

## Architecture

The aim of this module is to provide an abstract event to track an internal referral between departments (utilising the subspecialty definitions). Alongside this, it is designed to support integration with a 3rd party application to manage the details of this referral. Components to support this should be implemented based on the components\ExternalIntegration interface.

The following implementations are currently defined

### WinDIP

The WinDIP integration requires the request that is passed to WinDIP to be hashed. The details of this algorithm are proprietary, and as such are not committed to the public repository for this module. The functionality should be implemented as a callback function in the integration component. Please contact the OpenEyes Foundation for details of this if you are implementing this.

### File history

Because this module was merged in from a separate repository, the individual file history is only available through the use of the `--follow` option:

`git log --follow InternalReferralModule.php`

## Status

This module is in initial development and not intended for any use outside of core development. Feel free to take a look as code is developed though.

