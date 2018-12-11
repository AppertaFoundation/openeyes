DELETE FROM openeyes.medication_set_item WHERE medication_set_id IN (SELECT id FROM openeyes.medication_set WHERE `name` = 'DM+D AMP');
DELETE FROM openeyes.medication_set_item WHERE medication_set_id IN (SELECT id FROM openeyes.medication_set WHERE `name` = 'DM+D VMP');
DELETE FROM openeyes.medication_set_item WHERE medication_set_id IN (SELECT id FROM openeyes.medication_set WHERE `name` = 'DM+D VTM');
DELETE rmu FROM openeyes.event_medication_use rmu LEFT JOIN openeyes.medication rm ON rm.id = rmu.medication_id WHERE rm.source_type = 'DM+D';
DELETE FROM openeyes.medication_form WHERE source_type = 'DM+D';
DELETE FROM openeyes.medication_route WHERE source_type = 'DM+D';
DELETE FROM openeyes.medication WHERE source_type = 'DM+D';
DELETE FROM openeyes.medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');