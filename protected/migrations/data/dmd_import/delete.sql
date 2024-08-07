START TRANSACTION;

CREATE TEMPORARY TABLE tmp_delete_from_sets
SELECT id
FROM medication_set
WHERE `name` = 'DM+D AMP' OR `name` = 'DM+D VMP' OR `name` = 'DM+D VTM';

DELETE FROM medication_set_item_taper WHERE medication_set_item_id in (select id from medication_set_item WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D'));

DELETE FROM medication_set_item WHERE medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_item WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D');

DELETE FROM medication_set_auto_rule_attribute WHERE medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_set_membership WHERE target_medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_set_membership WHERE source_medication_set_id IN (SELECT id FROM tmp_delete_from_sets);

DELETE FROM medication_set_auto_rule_medication_taper WHERE medication_set_auto_rule_id in (SELECT id FROM medication_set_auto_rule_medication WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D'));

DELETE FROM medication_set_auto_rule_medication WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D');

DELETE FROM event_medication_use WHERE prescription_item_id in (SELECT rmu.id FROM event_medication_use rmu LEFT JOIN medication rm ON rm.id = rmu.medication_id WHERE rm.source_type = 'DM+D');

DELETE FROM ophdrprescription_item_taper WHERE item_id in (SELECT rmu.id FROM event_medication_use rmu LEFT JOIN medication rm ON rm.id = rmu.medication_id WHERE rm.source_type = 'DM+D');

DELETE rmu FROM event_medication_use rmu LEFT JOIN medication rm ON rm.id = rmu.medication_id WHERE rm.source_type = 'DM+D';

DELETE FROM medication_search_index WHERE medication_id IN (SELECT id FROM medication WHERE source_type = 'DM+D');
DELETE FROM medication WHERE source_type = 'DM+D';
DELETE FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');
DELETE FROM medication_attribute_assignment;
ALTER TABLE medication_attribute_assignment AUTO_INCREMENT = 1;

DELETE FROM medication_set_auto_rule_attribute where medication_attribute_option_id in (SELECT id FROM medication_attribute_option);

DELETE FROM medication_attribute_option;
ALTER TABLE medication_attribute_option AUTO_INCREMENT = 1;
DELETE FROM medication_attribute;
ALTER TABLE medication_attribute AUTO_INCREMENT = 1;
DELETE FROM medication_form WHERE source_type = 'DM+D';

DELETE FROM medication_set_item WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D');
DELETE FROM medication_set_auto_rule_medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D');
DELETE FROM medication_set_auto_rule_medication where medication_id in (SELECT id FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D'));
DELETE FROM medication_search_index where medication_id in (SELECT id FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D'));
DELETE FROM medication_set_item where medication_id in (SELECT id FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D'));
DELETE FROM event_medication_use WHERE prescription_item_id in (SELECT id FROM event_medication_use where medication_id in (SELECT id FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D')));
DELETE FROM event_medication_use where medication_id in (SELECT id FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D'));
DELETE FROM medication WHERE default_route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D');
DELETE FROM event_medication_use where route_id in (SELECT id FROM medication_route WHERE source_type = 'DM+D');


DELETE FROM medication_route WHERE source_type = 'DM+D';

DROP TABLE tmp_delete_from_sets;

COMMIT;
