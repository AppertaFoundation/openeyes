DELETE FROM medication_set_item WHERE medication_set_id IN
    (
        SELECT id
        FROM medication_set
        WHERE `name` = 'DM+D AMP' OR `name` = 'DM+D VMP' OR 'DM+D VTM'
    );

DELETE FROM medication_set_item WHERE medication_id IN (SELECT id FROM medication WHERE source_type='DM+D');

DELETE FROM medication_set_auto_rule_attribute;
DELETE FROM medication_set_auto_rule_set_membership;
DELETE FROM medication_set_auto_rule_medication;

DELETE rmu
FROM event_medication_use rmu
LEFT JOIN (SELECT id FROM medication WHERE medication.source_type = 'DM+D') rm ON rm.id = rmu.medication_id

DELETE FROM medication_search_index WHERE medication_id IN (SELECT id FROM medication WHERE source_type = 'DM+D');
DELETE FROM medication WHERE source_type = 'DM+D';
DELETE FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');
TRUNCATE TABLE medication_attribute_assignment;
TRUNCATE TABLE medication_attribute_option;
TRUNCATE TABLE medication_attribute;
DELETE FROM medication_form WHERE source_type = 'DM+D';
DELETE FROM medication_route WHERE source_type = 'DM+D';