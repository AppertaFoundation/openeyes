DELETE FROM medication_set_rule WHERE medication_set_id IN (SELECT id FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM'));
DELETE FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');

INSERT INTO medication_set (`name`) VALUES ('DM+D AMP');
INSERT INTO medication_set (`name`) VALUES ('DM+D VMP');
INSERT INTO medication_set (`name`) VALUES ('DM+D VTM');