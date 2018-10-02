DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D AMP');
DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D VMP');
DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D VTM');
DELETE rmu FROM openeyes.event_medication_uses rmu LEFT JOIN openeyes.ref_medication rm ON rm.id = rmu.ref_medication_id WHERE rm.source_type = 'DM+D';
DELETE FROM openeyes.ref_medication_form WHERE source_type = 'DM+D';
DELETE FROM openeyes.ref_medication_route WHERE source_type = 'DM+D';
DELETE FROM openeyes.ref_medication WHERE source_type = 'DM+D';
DELETE FROM openeyes.ref_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');