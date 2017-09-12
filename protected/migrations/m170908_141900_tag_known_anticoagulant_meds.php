m080917_141900_tag_known_anticoagulant_meds

<?php

class m170908_141900_tag_known_anticoagulant_meds extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_meds (med_id varchar(255), med_name VARCHAR(100), med_source VARCHAR(10), tag_id INT);


-- Load Anitcoagulants
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Acenocoumarol 1mg tablets', '319740004', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Apixaban 2.5mg tablets', '703907006', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Apixaban 5mg tablets', '703908001', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Coumadin 4mg tablets (Imported (Canada))', '18509011000001102', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Dabigatran etexilate 110mg capsules', '13532811000001109', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Dabigatran etexilate 150mg capsules', '19469811000001101', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Dabigatran etexilate 75mg capsules', '13532911000001104', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Phenindione 10mg tablets', '319745009', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Phenindione 25mg tablets', '319746005', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Phenindione 50mg tablets', '319747001', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Rivaroxaban 10mg tablets', '14254711000001104', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Rivaroxaban 15mg tablets', '19842111000001101', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Rivaroxaban 2.5mg tablets', '27810711000001104', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Rivaroxaban 20mg tablets', '19842211000001107', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1.5mg/5ml oral solution', '13016611000001102', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 100micrograms/5ml oral solution', '13016711000001106', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 10mg/5ml oral solution', '8797911000001107', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 10mg/5ml oral suspension', '8798011000001109', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1mg capsules', '32751111000001103', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1mg tablets', '319733000', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1mg/5ml oral solution', '8798111000001105', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1mg/5ml oral suspension', '8798211000001104', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 1mg/ml oral suspension sugar free', '18290011000001102', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 2.5mg/5ml oral solution', '13016811000001103', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 25mg/5ml oral solution', '13016911000001108', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 2mg/5ml oral solution', '8798311000001107', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 2mg/5ml oral suspension', '8798411000001100', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 3mg capsules', '32751211000001109', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 3mg tablets', '319734006', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 3mg/5ml oral solution', '8798511000001101', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 3mg/5ml oral suspension', '8798611000001102', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 4.16mg/5ml oral solution', '13017011000001107', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 4mg tablets', '375374009', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 4mg/5ml oral solution', '13017111000001108', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 500microgram tablets', '319736008', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 500micrograms/5ml oral solution', '13017211000001102', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 5mg capsules', '32751311000001101', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 5mg tablets', '319735007', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 5mg/5ml oral solution', '8798711000001106', 'DMD-VMP');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin 5mg/5ml oral suspension', '8798811000001103', 'DMD-VMP');

INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Acenocoumarol', '79356008', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Apixaban', '19510611000001104', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Dabigatran etexilate', '13568411000001103', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Phenindione', '47527007', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Rivaroxaban', '442539005', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Phenindione', '47527007', 'DMD-VTM');
INSERT INTO `tmp_meds` (`med_name`, `med_id`, `med_source`) VALUES ('Warfarin', '48603004', 'DMD-VTM');

-- Add missing meds to db

INSERT INTO `medication_drug` (`name`, `external_code`, `external_source`)
SELECT med_name, med_id, med_source FROM tmp_meds WHERE med_id NOT IN (SELECT external_code FROM medication_drug);

-- Add anticoagulant tag_id
SET @acTagId = (SELECT id FROM tag WHERE `name` = 'Anticoagulant');

INSERT INTO medication_drug_tag (`medication_drug_id`, `tag_id`)
SELECT m.id, @acTagId FROM medication_drug m INNER JOIN tmp_meds t ON m.external_code = t.med_id AND m.id NOT IN (SELECT medication_drug_id FROM medication_drug_tag WHERE medication_drug_id = m.id AND tag_id = @acTagId);

DROP TABLE tmp_meds;"
        );
    }

    public function down()
    {
        # No down required as this is a non-destructive migration
    }
}
