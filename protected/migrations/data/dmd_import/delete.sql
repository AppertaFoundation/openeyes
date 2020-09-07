START TRANSACTION;

CREATE TEMPORARY TABLE tmp_delete_from_sets
SELECT id
FROM medication_set
WHERE `name` = 'DM+D AMP' OR `name` = 'DM+D VMP' OR `name` = 'DM+D VTM';

DELETE FROM medication_set_item WHERE medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_item WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D');

DELETE FROM medication_set_auto_rule_attribute WHERE medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_set_membership WHERE target_medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_set_membership WHERE source_medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_medication WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D');

DELETE rmu FROM event_medication_use rmu LEFT JOIN medication rm ON rm.id = rmu.medication_id WHERE rm.source_type = 'DM+D';

DELETE FROM medication_search_index WHERE medication_id IN (SELECT id FROM medication WHERE source_type = 'DM+D');
DELETE FROM medication WHERE source_type = 'DM+D';
DELETE FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');
DELETE FROM medication_attribute_assignment;
ALTER TABLE medication_attribute_assignment AUTO_INCREMENT = 1;
DELETE FROM medication_attribute_option;
ALTER TABLE medication_attribute_option AUTO_INCREMENT = 1;
DELETE FROM medication_attribute;
ALTER TABLE medication_attribute AUTO_INCREMENT = 1;
DELETE FROM medication_form WHERE source_type = 'DM+D';
DELETE FROM medication_route WHERE source_type = 'DM+D';

DROP TABLE tmp_delete_from_sets;

COMMIT;