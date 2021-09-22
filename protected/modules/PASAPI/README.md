# PASAPI

## Overview

This module is designed to provide a simplified API for managing patient and patient related data in OpenEyes from an external source, specifically a PAS.

At the time of development, a core API does exist in the OpenEyes core, but it is based on an out of date FHIR specification, and early testing indicated some issues that would need updating to resolve this. Furthermore, by developing a specific API for PAS integration, we are able to manage the external references cleanly, and reduce the number of calls required to add or update a patient.

## Developers

### Running tests

The tests will currently only run within the context of the wider OpenEyes implementation, and relies on the Rest Test Case in core (which in turn relies on guzzle - this may not be present if you haven't got the right composer setup in place). 

__NB__ You need to be using the shell wrapper to phpunit to ensure everything is bootstrapped appropriately.

### File history

Because this module was merged in from a separate repository, the individual file history is only available through the use of the `--follow` option:

`git log --follow PASAPIModule.php`

## Design

### Patient Information

The basic design is to support a PUT call to openeyes with the details of a patient. If the patient doesn't exist, it will be created. If it does already exist, then the details of the patient will be updated. Within this, the patient GP and Practice relations will be defined using the standard external identifiers that have been imported on those records.

The external identifier is used for defining a patient instance, and is tracked internally in the module via the PasApiAssignment model.

### Patient Appointments

As an extension to the original functionality, PUT and DELETE requests are now supported to create entries on automatic worklists that have been defined in the Worklist Admin. Similarly to the Patient implementation, this enables the external tracking of any appointments that are created, thereby supporting the ability to update or remove specific entries. 

## Configuration

The usual configuration for the module must be added to the modules array:

    'PASAPI' => array( 'class' => '\OEModule\PASAPI\PASAPIModule', )
 
### Patient Addresses
 
 Patient addresses have no external identifier for tracking their changes. As a result, the system verifies that an address is the same as a previously provided address by comparing postcodes. If there is a postcode match, then an address will be updated, rather than a new Address instance created.
 
## Details

The API is designed to be RESTful, making extensive use of Http Status codes to describe the result of requests.

### Custom Headers

The API supports two custom headers that will affect the way that PUT requests are handled:

#### Update Only

The update only header will prevent a record being processed unless it already exists (as defined by matching on the given external identifier). The header value is:

```X-OE-Update-Only: 1```

#### Partial Update

In some situations it can be convenient to only provide the data that should be changed for a given record. If this is the case, the Partial Update header should be used:

```X-OE-Partial-Record: 1```

If this is provided, then any items that are not provided in the request will be assumed to be remaining the same as they currently are on the record. It should be noted that in some circumstances this will cause an error (e.g. if a change in a value will cause a patient appointment to move from its current list, an error will be thrown).

### Patient
 
#### Create/Update

Create and Update of patients is managed with PUT requests:

##### URL
 
    http://[oe-base-url]/PASAPI/V1/Patient/[external-id]
    
##### Request Body
 
    <Patient>
        <NHSNumber>0123456789</NHSNumber>
        <HospitalNumber>92312423</HospitalNumber>
        <Title>MRS</Title>
        <FirstName>Violet</FirstName>
        <Surname>Coffin</Surname>
        <DateOfBirth>1978-03-01</DateOfBirth>
        <Gender>F</Gender>
        <AddressList>
            <Address>
                <Line1>82 Scarisbrick Lane</Line1>
                <Line2/>
                <City>Bethersden</City>
                <County>West Yorkshire</County>
                <Postcode>QA88 2GC</Postcode>
                <Country>GB</Country>
                <Type>HOME</Type>
            </Address>
        </AddressList>
        <TelephoneNumber>03040 6024378</TelephoneNumber>
        <EthnicGroup>A</EthnicGroup>
        <DateOfDeath/>
        <PracticeCode>F001</PracticeCode>
        <GpCode>G0102926</GpCode>
        <LanguageCode>alb</LanguageCode>
        <InterpreterRequired>alb</InterpreterRequired>
    </Patient>
    
##### Response Body (Success)

    <Success>
        <Message>Patient created</Message>
        <Id>[internal patient id]</Id>
    </Success>

##### Response Body (Warnings)

The status code for a response with warnings will still be in the 2xx range, as the request will be processed. The following elements will generate warnings if the given values do not match valid values in the system:
 
* Gender
* EthnicGroup
* PracticeCode
* GpCode

e.g.

    <Success>
        <Message>Patient created</Message>
        <Id>[internal patient id]</Id>
        <Warnings>
            <Warning>Unrecognised Gender X</Warning>
        </Warnings>
    </Success>
    
##### Response Body (Errors)

Internal errors (5xx status code) should still provide an error body response of some kind where feasible.

    <Failure>
        <Errors>
            <Error>[error message]</Error>
        </Errors>
    </Failure>
 
#### Update only [deprecated]

This attribute approach is deprecated, please see the headers section above.

If the intention is for the patient to only be updated, and not created if it doesn't exist, the updateOnly attribute should be used:

    <Patient updateOnly="1">
         <NHSNumber>0123456789</NHSNumber>
         ...

### Patient Appointment

#### Create/Update

##### URL
 
    http://[oe-base-url]/PASAPI/V1/PatientAppointment/[external-id]
    
##### Request Body

    <PatientAppointment>
        <PatientId>
            <Id>[internal patient id]</Id>
        </PatientId>
        <Appointment>
            <AppointmentDate>yyyy-mm-dd</AppointmentDate>
            <AppointmentTime>hh-mm</AppointmentTime>
            <AppointmentMappingItems>
                <AppointmentMapping>
                    <Key>[key1]</Key>
                    <Value>[value1]</Value>
                </AppointmentMapping>
                 ...
            </AppointmentMappingItems>
        </Appointment>
    </PatientAppointment>

The ```[internal patient id]``` should correspond to the returned value from the Patient PUT request above.

The ```AppointmentMapping``` entries should correspond to mappings defined for automatic worklists in the admin interface for OpenEyes. For a mapping to be performed successfully, a unique worklist entry must be distinguishable through the combination of ```AppointmentDate```, ```AppointmentTime``` and ```AppointmentMappingItems```.

Further PUT requests with the same ```external-id``` will update the worklist entry. If the ```AppointmentMappingItems``` are updated and correspond to a different worklist, then the appointment will be moved accordingly.

##### Response Body (Success)

    <Success>
        <Id>[internal appointment id]</Id>
        <Message>PatientAppointment created.</Message>
    </Success>

##### Response Body (Failure)

    <Failure>
        <Errors>
            <Error>[value1] not valid for key '[key1]'</Error>
            <Error>No worklist found for criteria</Error>
            <Error>Could not update patient worklist entry</Error>
        </Errors>
    </Failure>
    
#### Delete

To delete a patient appointment, A DELETE call should be made to the same URL pattern:

    http://[oe-base-url]/PASAPI/V1/PatientAppointment/[external-id]
    
A successful DELETE will illicit a 204 Status Code response

## Remapping values

To deal with the issue of external sources not mapping to internal resource values within OpenEyes, a remapping of values can be configured through the admin.

Each XpathRemap object is defined as having an xpath and a name attribute (which is simply a descriptive label). This has zero or more RemapValue attributes that will swap any matching input values for a different output value.

These can be managed through the module admin screen.

(note that whilst in the admin one can only provide an empty string, the code supports a null value which will lead the entire node to be removed, rather than emptied).

For example, if the code AX is coming through from the message sender for ethnic group:

1. Create an XpathRemap object with Xpath /Patient/EthnicGroup and name "Ethnic Group Remapping".
1. Click on the value count (which will be zero initially), and add an entry with input AX, and output A.
