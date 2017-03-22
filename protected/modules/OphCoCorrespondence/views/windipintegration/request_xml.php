<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<DocumentInformation>
    <PatientNumber><?=$hos_num;?></PatientNumber>
    <NhsNumber><?=$nhs_num;?></NhsNumber>
    <Name><?=$full_name;?></Name>
    <Surname><?=$last_name;?></Surname>
    <FirstForename><?=$first_name;?></FirstForename>
    <SecondForename><?=$second_first_name;?></SecondForename>
    <Title><?=$title;?></Title>
    <DateOfBirth><?=$date_of_birth;?></DateOfBirth>
    <Sex><?=$gender;?></Sex>
    <Address><?=$address;?></Address>
    <AddressName><?=$address_name;?></AddressName>
    <AddressNumber><?=$address_number;?></AddressNumber>
    <AddressStreet><?=$address_street;?></AddressStreet>
    <AddressDistrict></AddressDistrict>
    <AddressTown><?=$address_town;?></AddressTown>
    <AddressCounty><?=$address_county;?></AddressCounty>
    <AddressPostcode><?=$address_postcode;?></AddressPostcode>
    <GP><?=$gp_nat_id;?></GP> <!-- NOT NEEDED -->
    <GPName><?=$gp_name;?></GPName> <!-- NOT NEEDED -->
    <Surgery><?=$surgery_code;?></Surgery> <!-- NOT NEEDED -->
    <SurgeryName><?=$surgery_name;?></SurgeryName> <!-- NOT NEEDED -->
    <LetterType><?=$letter_type;?></LetterType> <!-- Note: New type 'Internal Referral' -->
    <ActivityID><?=$activity_id;?></ActivityID> <!-- Assuming this is the event ID? -->
    <ActivityDate><?=$activity_date;?></ActivityDate>
    <ClinicianType><?=$clinician_type;?></ClinicianType> <!-- used for referring clinician -->
    <Clinician><?=$clinician;?></Clinician> <!-- used for referring clinician -->
    <ClinicianName><?=$clinician_name;?></ClinicianName> <!-- used for referring clinician -->
    <Specialty><?=$specialty_red_spec;?></Specialty> <!-- used for referring specialty -->
    <SpecialtyName><?=$specialty_name;?></SpecialtyName>
    <Location><?=$location;?></Location>  <!-- NOT NEEDED -->
    <LocationName><?=$location_name;?></LocationName>  <!-- NOT NEEDED -->
    <SubLocation><?=$sub_location;?></SubLocation>  <!-- NOT NEEDED -->
    <SubLocationName><?=$sub_location_name;?></SubLocationName>  <!-- NOT NEEDED -->
    <ServiceTo><?=$service_to;?></ServiceTo> <!-- -NEW- The PAS service code that the patient is being referred to -->
    <ConsultantTo><?=$consultant_to;?></ConsultantTo> <!-- -NEW-The (optional) PAS consultant code that the patient is being referred to -->
    <Urgent><?=$is_urgent;?></Urgent> <!-- -NEW- Is this an aurgent referral? -->
    <SameCondition><?=$is_same_condition;?></SameCondition> <!-- -NEW- Is this a referral for same or different condition -->
</DocumentInformation>