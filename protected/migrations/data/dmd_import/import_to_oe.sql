DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D AMP');
DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D VMP');
DELETE FROM openeyes.ref_medication_set WHERE ref_set_id = (SELECT id FROM openeyes.ref_set WHERE `name` = 'DM+D VTM');
DELETE rmu FROM openeyes.event_medication_uses rmu LEFT JOIN openeyes.ref_medication rm ON rm.id = rmu.ref_medication_id WHERE rm.source_type = 'DM+D';
DELETE FROM openeyes.ref_medication_form WHERE source_type = 'DM+D';
DELETE FROM openeyes.ref_medication_route WHERE source_type = 'DM+D';
DELETE FROM openeyes.ref_medication WHERE source_type = 'DM+D';
//------------------------------------------ AMP --------------------------------------------------------
INSERT INTO openeyes.ref_medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
SELECT
	'DM+D' AS source_type,
	'AMP' AS source_subtype,

	amp.desc AS preferred_term,
	amp.apid AS preferred_code,

	vtm.nm AS vtm_term,
	vtm.vtmid AS vtm_code,

	vmp.nm AS vmp_term,
	vmp.vpid AS vmp_code,

	amp.desc AS amp_term,
	amp.apid AS amp_code
FROM
	drugs2.f_amp_amps amp
	LEFT JOIN drugs2.f_vmp_vmps vmp ON vmp.vpid = amp.vpid
	LEFT JOIN drugs2.f_vtm_vtm vtm ON vtm.vtmid = vmp.vtmid
;
//------------------------------------------ VMP --------------------------------------------------------
INSERT INTO openeyes.ref_medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
SELECT
	'DM+D' AS source_type,
	'VMP' AS source_subtype,

	vmp.nm AS preferred_term,
	vmp.vpid AS preferred_code,

	vtm.nm AS vtm_term,
	vtm.vtmid AS vtm_code,

	vmp.nm AS vmp_term,
	vmp.vpid AS vmp_code,

	amp.desc AS amp_term,
	amp.apid AS amp_code
FROM
	drugs2.f_vmp_vmps vmp
	LEFT JOIN drugs2.f_amp_amps amp ON vmp.vpid = amp.vpid
	LEFT JOIN drugs2.f_vtm_vtm vtm ON vtm.vtmid = vmp.vtmid
;

//------------------------------------------ VTM --------------------------------------------------------
INSERT INTO openeyes.ref_medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
SELECT
	'DM+D' AS source_type,
	'VTM' AS source_subtype,

	vtm.nm AS preferred_term,
	vtm.vtmid AS preferred_code,

	vtm.nm AS vtm_term,
	vtm.vtmid AS vtm_code,

	vmp.nm AS vmp_term,
	vmp.vpid AS vmp_code,

	amp.desc AS amp_term,
	amp.apid AS amp_code
FROM drugs2.f_vtm_vtm vtm
	LEFT JOIN drugs2.f_vmp_vmps vmp ON vmp.vtmid = vtm.vtmid
	LEFT JOIN drugs2.f_amp_amps amp ON amp.vpid = vmp.vpid
;
//------------------------------------------ FORMS ------------------------------------------------------
INSERT INTO openeyes.ref_medication_form (`term`,`code`,`unit_term`,`default_dose_unit_term`,`source_type`)
SELECT DISTINCT
	`desc`,
	cd,
	`desc`,
	`desc`,
	'DM+D' AS source_type
FROM drugs2.f_lookup_form
;
//------------------------------------------ ROUTES -----------------------------------------------------
INSERT INTO openeyes.ref_medication_route (term,`code`, source_type)
SELECT
	lr.desc,
	lr.cd,
	'DM+D'
FROM
	drugs2.f_lookup_route lr
;
//------------------------------------------ SET --------------------------------------------------------
INSERT INTO openeyes.ref_set (`name`) VALUES ('DM+D');
//------------------------------------------ REF_MEDICATION_SET -----------------------------------------
INSERT INTO openeyes.ref_medication_set (ref_medication_id,ref_set_id,default_form,default_dose,default_route,default_frequency,default_dose_unit_term,deleted_date)
SELECT
	rm.id,
	(
		SELECT id FROM openeyes.ref_set rs WHERE
		rs.name = CASE rm.source_subtype
			WHEN 'AMP' THEN 'DM+D AMP'
			WHEN 'VMP' THEN 'DM+D VMP'
			WHEN 'VTM' THEN 'DM+D VTM'
		END
	) AS setId,
	(
		SELECT
			mf.id
		FROM openeyes.ref_medication m
			LEFT JOIN drugs2.f_amp_amps amp2 ON amp2.apid = m.amp_code
			LEFT JOIN drugs2.f_vmp_drug_form dft ON dft.vpid = amp2.vpid
			LEFT JOIN drugs2.f_lookup_form fhit ON fhit.cd = dft.formcd
			LEFT JOIN openeyes.ref_medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'
		WHERE m.id = rm.id
	) AS FormId,
	NULL AS dose,
	mr.id AS RouteId,
	NULL AS requency,
	NULL AS defaultDoseUnit,
	NULL AS deletedDate
FROM openeyes.ref_medication rm
	LEFT JOIN openeyes.ref_medication_route mr ON mr.id IN (
		SELECT
			mr2.id
		FROM openeyes.ref_medication m2
			LEFT JOIN drugs2.f_amp_amps amp3 ON amp3.apid = m2.amp_code
			LEFT JOIN drugs2.f_vmp_drug_route drt ON drt.vpid = amp3.vpid
			LEFT JOIN drugs2.f_lookup_route lr ON lr.cd = drt.routecd
			LEFT JOIN openeyes.ref_medication_route mr2 ON mr2.term = lr.desc AND mr2.source_type = 'DM+D'
		WHERE m2.id = rm.id
	)
WHERE ( rm.source_type='DM+D' AND rm.source_subtype <> 'VTM')
;

-- WHERE ( rm.source_type='DM+D' AND rm.source_subtype = 'VTM')

//------------------- EGYEB --------------------
ALTER TABLE `openeyes`.`ref_medication_form` ADD KEY (`term`);
ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`vmp_code`);
ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`vtm_code`);
ALTER TABLE `openeyes`.`ref_medication` ADD KEY (`amp_code`);
ALTER TABLE `openeyes`.`ref_medication_route` ADD KEY (`term`);

ALTER TABLE `openeyes`.`ref_medication` CHANGE `source_type` `source_type` VARCHAR(10) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `source_subtype` `source_subtype` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `preferred_term` `preferred_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `preferred_code` `preferred_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `vtm_term` `vtm_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vtm_code` `vtm_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vmp_term` `vmp_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `vmp_code` `vmp_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `amp_term` `amp_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `amp_code` `amp_code` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `short_term` `short_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, COLLATE=utf8_general_ci;
ALTER TABLE `openeyes`.`ref_medication_form` CHANGE `term` `term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `code` `code` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `unit_term` `unit_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `default_dose_unit_term` `default_dose_unit_term` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `source_type` `source_type` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `source_subtype` `source_subtype` VARCHAR(45) CHARSET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `openeyes`.`ref_medication_form` COLLATE=utf8_general_ci;

UPDATE openeyes.ref_medication_set SET ref_set_id = 33 WHERE ref_set_id IN (31,32);
UPDATE ref_medication_set ms LEFT JOIN ref_medication_form mf ON mf.id = ms.default_form
SET ms.default_dose_unit_term = mf.default_dose_unit_term
WHERE ms.ref_set_id IN (33);

//------------------------------------ UNIT ?? -------------------------------------------------------------------------
SELECT
amp.nm,
vpi.strnt_nmrtr_val,
uom.desc
FROM drugs2.f_amp_amps amp
LEFT JOIN f_vmp_vmps vmp ON vmp.vpid = amp.vpid
LEFT JOIN f_vmp_virtual_product_ingredient vpi ON vpi.vpid = amp.vpid
LEFT JOIN f_lookup_unit_of_measure uom ON uom.cd  = strnt_dnmtr_uomcd

----------------------------------- IDEIGLENES, DEMO!!! -----------------------------------------------------------------

INSERT INTO openeyes.ref_medication_set (ref_medication_id,ref_set_id,default_form,default_dose,default_route,default_frequency,default_dose_unit_term,deleted_date)
SELECT
	rm.id,
	(
		SELECT id FROM openeyes.ref_set rs WHERE
		rs.name = CASE rm.source_subtype
			WHEN 'AMP' THEN 'DM+D AMP'
			WHEN 'VMP' THEN 'DM+D VMP'
			WHEN 'VTM' THEN 'DM+D VTM'
		END
	) AS setId,
	(
		SELECT
			mf.id
		FROM openeyes.ref_medication m
			LEFT JOIN drugs2.f_vmp_vmps vmp2 ON vmp2.vpid = m.vmp_code
			LEFT JOIN drugs2.f_vmp_drug_form dft ON dft.vpid = vmp2.vpid
			LEFT JOIN drugs2.f_lookup_form fhit ON fhit.cd = dft.formcd
			LEFT JOIN openeyes.ref_medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'
		WHERE m.id = rm.id
	) AS FormId,
	NULL AS dose,
	mr.id AS RouteId,
	NULL AS requency,
	NULL AS defaultDoseUnit,
	NULL AS deletedDate
FROM openeyes.ref_medication rm
	LEFT JOIN openeyes.ref_medication_route mr ON mr.id IN (
		SELECT
			mr2.id
		FROM openeyes.ref_medication m2
			LEFT JOIN drugs2.f_vmp_vmps vmp3 ON vmp3.vpid = m2.vmp_code
			LEFT JOIN drugs2.f_vmp_drug_route drt ON drt.vpid = vmp3.vpid
			LEFT JOIN drugs2.f_lookup_route lr ON lr.cd = drt.routecd
			LEFT JOIN openeyes.ref_medication_route mr2 ON mr2.term = lr.desc AND mr2.source_type = 'DM+D'
		WHERE m2.id = rm.id
	)
WHERE ( rm.source_type='DM+D' AND rm.source_subtype <> 'VTM')
;