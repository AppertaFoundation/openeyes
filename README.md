# PASAPI

## Overview

This module is designed to provide a simplified API for managing patients in OpenEyes from an external source, specifically a PAS.

At the time of development, a core API does exist in the OpenEyes core, but it is based on an out of date FHIR specification, and early testing indicated some issues that would need updating to resolve this. Furthermore, by developing a specific API for PAS integration, we are able to manage the external references cleanly, and reduce the number of calls required to add or update a patient.

## Design

The basic design is to support a PUT call to openeyes with the details of a patient. If the patient doesn't exist, it will be created. If it does already exist, then the details of the patient will be updated. Within this, the patient GP and Practice relations will be defined using the standard external identifiers that have been imported on those records.

The external identifier is used for defining a patient instance, and is tracked internally in the module via the PasApiAssignment model.
 
### Patient Addresses
 
 Patient addresses have no external identifier for tracking their changes. As a result, the system verifies that an address is the same as a previously provided address by comparing postcodes. If there is a postcode match, then an address will be updated, rather than a new Address instance created.
 
## Example
 
 The URL pattern for the PUT call is as follows:
 
    http://[oe-base-url]/PASAPI/v1/Patient/[external-id]
    
The XML for defining a patient is as follows.

 
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
    </Patient>
 
 The following elements will generate warnings if the provided codes are not provided
 
 * Gender
 * EthnicGroup
 * PracticeCode
 * GpCode
 
 