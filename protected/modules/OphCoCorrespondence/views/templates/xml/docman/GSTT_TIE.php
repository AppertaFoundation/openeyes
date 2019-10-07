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
<?php echo "<?xml version=\"1.0\"?>";?>

<ns0:Correspondence xmlns:ns0="http://GSTT.TIE.OpenEyes.Schemas.CorrespondenceSchema">
    <NHSNumber><?=$data['nhs_num'];?></NHSNumber>
    <HospitalNumber><?=$data['hos_num'];?></HospitalNumber>
    <VisitID><?=$data['visit_id'];?></VisitID>
<?php if (isset($data['document_links']) && is_array($data['document_links']) ): ?>
        <?php foreach($data['document_links'] as $link):?>
            <DocumentLink><?=$link;?></DocumentLink>
        <?php endforeach;?>
    <?php endif;?>
    <GP>
        <GPNumber><?=$data['gp_nat_id'];?></GPNumber>
        <PracticeNumber><?=$data['practice_code'];?></PracticeNumber>
        <Title><?=$data['gp_title'];?></Title>
        <FirstName><?=$data['gp_first_name'];?></FirstName>
        <LastName><?=$data['gp_last_name'];?></LastName>
    </GP>
    <Patient>
        <PatientNumber><?=$data['hos_num'];?></PatientNumber>
        <Title><?=$data['patient_title'];?></Title>
        <FirstName><?=$data['first_name'];?></FirstName>
        <LastName><?=$data['last_name'];?></LastName>
        <Sex><?=$data['gender'];?></Sex>
        <DateOfBirth><?=$data['dob'];?></DateOfBirth>
        <DateOfDeath><?=$data['date_of_death'];?></DateOfDeath>
    </Patient>
<?php if (isset($data['with_internal_referral']) && $data['with_internal_referral']):?>
    <Consultant>
        <ConsultantNumber><?=$data['consultant_to'];?></ConsultantNumber>
        <SpecialtyCode><?=$data['location_code'];?></SpecialtyCode>
        <Title><?=$data['consultant_title'];?></Title>
        <FirstName><?=$data['consultant_first_name'];?></FirstName>
        <LastName><?=$data['consultant_last_name'];?></LastName>
    </Consultant>
<?php endif; ?>
</ns0:Correspondence>