<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";?>
<DocumentInformation>
    <PatientNumber><?=htmlspecialchars($data['hos_num']);?></PatientNumber>
    <NhsNumber><?=htmlspecialchars($data['nhs_num']);?></NhsNumber>
    <Name><?=htmlspecialchars($data['full_name']);?></Name>
    <Surname><?=htmlspecialchars($data['last_name']);?></Surname>
    <FirstForename><?=htmlspecialchars($data['first_name']);?></FirstForename>
    <SecondForename></SecondForename>
    <Title><?=htmlspecialchars($data['title']);?></Title>
    <DateOfBirth><?=htmlspecialchars($data['dob']);?></DateOfBirth>
    <Sex><?=htmlspecialchars($data['gender']);?></Sex>
    <Address><?=htmlspecialchars(implode(", ", $data['address']));?></Address>
    <AddressName></AddressName>
    <AddressNumber></AddressNumber>
    <AddressStreet><?=htmlspecialchars($data['address1']);?></AddressStreet>
    <AddressDistrict></AddressDistrict>
    <AddressTown><?=htmlspecialchars($data['city']);?></AddressTown>
    <AddressCounty><?=htmlspecialchars($data['county']);?></AddressCounty>
    <AddressPostcode><?=htmlspecialchars($data['post_code']);?></AddressPostcode>
    <GP><?=htmlspecialchars($data['gp_nat_id']);?></GP>
    <GPName><?=htmlspecialchars($data['gp_name']);?></GPName>
    <Surgery><?=htmlspecialchars($data['practice_code']);?></Surgery>
    <SurgeryName></SurgeryName>
    <ActivityID><?=htmlspecialchars($data['event_id']);?></ActivityID>
    <ActivityDate><?=htmlspecialchars($data['event_date']);?></ActivityDate>
    <ClinicianType></ClinicianType>
    <Clinician></Clinician>
    <ClinicianName></ClinicianName>
    <Specialty><?=htmlspecialchars($data['subspeciality']);?></Specialty>
    <SpecialtyName><?=htmlspecialchars($data['subspeciality_name']);?></SpecialtyName>
    <Location><?=htmlspecialchars($data['site_short_name']);?></Location>
    <LocationName><?=htmlspecialchars($data['site_name']);?></LocationName>
    <SubLocation></SubLocation>
    <SubLocationName></SubLocationName>
<?php if (isset($data['letter_type']) && $data['letter_type']):?>
    <LetterType><?=htmlspecialchars($data['letter_type']);?></LetterType>
<?php endif; ?>
<?php if (isset($data['with_internal_referral']) && $data['with_internal_referral']):?>
    <ServiceTo><?=htmlspecialchars($data['service_to']);?></ServiceTo>
    <ConsultantTo><?=htmlspecialchars($data['consultant_to']);?></ConsultantTo>
    <workflowimportance><?=$data['is_urgent'];?></workflowimportance>
    <SameCondition><?=$data['is_same_condition'];?></SameCondition>
    <ToLocationCode><?=htmlspecialchars($data['location_code']);?></ToLocationCode>
    <Urgent><?=(int)$data['is_urgent'];?></Urgent>
    <SendTo><?=htmlspecialchars($data['output_type'])?></SendTo>
<?php endif; ?>
</DocumentInformation>
