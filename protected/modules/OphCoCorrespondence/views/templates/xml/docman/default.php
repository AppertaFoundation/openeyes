<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";?>

<DocumentInformation>
    <PatientNumber><?=$data['hos_num'];?></PatientNumber>
    <NhsNumber><?=$data['nhs_num'];?></NhsNumber>
    <Name><?=$data['full_name'];?></Name>
    <Surname><?=$data['last_name'];?></Surname>
    <FirstForename><?=$data['first_name'];?></FirstForename>
    <SecondForename></SecondForename>
    <Title><?=$data['title'];?></Title>
    <DateOfBirth><?=$data['dob'];?></DateOfBirth>
    <Sex><?=$data['gender'];?></Sex>
    <Address><?=implode(", ", $data['address']);?></Address>
    <AddressName></AddressName>
    <AddressNumber></AddressNumber>
    <AddressStreet><?=$data['address1'];?></AddressStreet>
    <AddressDistrict></AddressDistrict>
    <AddressTown><?=$data['city'];?></AddressTown>
    <AddressCounty><?=$data['county'];?></AddressCounty>
    <AddressPostcode><?=$data['post_code'];?></AddressPostcode>
    <GP><?=$data['gp_nat_id'];?></GP>
    <GPName><?=$data['gp_name'];?></GPName>
    <Surgery><?=$data['practice_code'];?></Surgery>
    <SurgeryName></SurgeryName>
    <ActivityID><?=$data['event_id'];?></ActivityID>
    <ActivityDate><?=$data['event_date'];?></ActivityDate>
    <ClinicianType></ClinicianType>
    <Clinician></Clinician>
    <ClinicianName></ClinicianName>
    <Specialty><?=$data['subspeciality'];?></Specialty>
    <SpecialtyName><?=$data['subspeciality_name'];?></SpecialtyName>
    <Location><?=$data['site_short_name'];?></Location>
    <LocationName><?=$data['site_name'];?></LocationName>
    <SubLocation></SubLocation>
    <SubLocationName></SubLocationName>
    <LetterType><?=$data['letter_type'];?></LetterType>
    <LetterTypeId><?=$data['letter_type_id'];?></LetterTypeId>
<?php if (isset($data['with_internal_referral']) && $data['with_internal_referral']):?>
    <!--Internal Referral-->
    <ServiceTo><?=$data['service_to'];?></ServiceTo>
    <ConsultantTo><?=$data['consultant_to'];?></ConsultantTo>
    <!-- is urgent or not -->
    <workflowimportance><?=$data['is_urgent'];?></workflowimportance>
    <SameCondition><?=$data['is_same_condition'];?></SameCondition>
    <ToLocationCode><?=$data['location_code'];?></ToLocationCode>
    <!-- When main recipient is Internalreferral and a CC is a GP the Docman and Internalreferral XMLs look like the same. -->
    <!-- SendTo tag contains the actual output type: Either 'Docman' or 'Internalreferral' -->
    <SendTo><?=$data['output_type']?></SendTo>
<?php endif; ?>
</DocumentInformation>
