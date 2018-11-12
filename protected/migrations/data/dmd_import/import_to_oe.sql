


-- WHERE ( rm.source_type='DM+D' AND rm.source_subtype = 'VTM')

/*


UPDATE openeyes.ref_medication_set SET ref_set_id = 33 WHERE ref_set_id IN (31,32);
UPDATE ref_medication_set ms LEFT JOIN ref_medication_form mf ON mf.id = ms.default_form
SET ms.default_dose_unit_term = mf.default_dose_unit_term
WHERE ms.ref_set_id IN (33);
*/


/*------------------------------------ UNIT ?? -------------------------------------------------------------------------*/
SELECT
amp.nm,
vpi.strnt_nmrtr_val,
uom.desc
FROM drugs2.f_amp_amps amp
LEFT JOIN f_vmp_vmps vmp ON vmp.vpid = amp.vpid
LEFT JOIN f_vmp_virtual_product_ingredient vpi ON vpi.vpid = amp.vpid
LEFT JOIN f_lookup_unit_of_measure uom ON uom.cd  = strnt_dnmtr_uomcd

/*----------------------------------- TEMP DEMO!!! --------------------------------------------------------------*/
/*
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
;*/