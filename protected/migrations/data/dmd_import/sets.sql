/*------------------------------------------ SET --------------------------------------------------------*/
DELETE FROM openeyes.ref_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');

INSERT INTO openeyes.ref_set (`name`) VALUES ('DM+D AMP');
INSERT INTO openeyes.ref_set (`name`) VALUES ('DM+D VMP');
INSERT INTO openeyes.ref_set (`name`) VALUES ('DM+D VTM');