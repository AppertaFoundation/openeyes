/*------------------------------------------ SET --------------------------------------------------------*/
DELETE FROM openeyes.medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');

INSERT INTO openeyes.medication_set (`name`) VALUES ('DM+D AMP');
INSERT INTO openeyes.medication_set (`name`) VALUES ('DM+D VMP');
INSERT INTO openeyes.medication_set (`name`) VALUES ('DM+D VTM');