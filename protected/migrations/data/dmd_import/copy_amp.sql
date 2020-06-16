-- Copy's the AMP drugs into the medication table
--
-- NOTE: * Supplier name is striped off the end of the desc (using a regex).
--       * Any desc that is the same as it's VMP term is not imported
-- 	     * Only "Unique" / Distinct drug names are imported (after supplier has been stripped) - DA descision
--       * The extra ordered sub-query before the GROUP BY helps give a consistent result when running multiple times

INSERT IGNORE INTO medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code, default_form_id)
SELECT * FROM 
	(SELECT
		'DM+D' AS source_type,
		'AMP' AS source_subtype,
	
		short_term.desc AS preferred_term, 
		amp.apid AS preferred_code,
	
		vtm.nm AS vtm_term,
		vtm.vtmid AS vtm_code,
	
		vmp.nm AS vmp_term,
		vmp.vpid AS vmp_code,
	
		short_term.desc AS amp_term,
		amp.apid AS amp_code,
	       mf.id
	FROM
		{prefix}amp_amps amp
		LEFT JOIN (SELECT apid, 
						  REGEXP_REPLACE(a.desc, "\\([\\w,\\s,\\(,',_,&,\+,-]*[^\\(]*\\)$", '') AS `desc`
			       FROM {prefix}amp_amps a) AS short_term ON short_term.apid = amp.apid
	    LEFT JOIN {prefix}vmp_vmps vmp ON vmp.vpid = amp.vpid
	    LEFT JOIN {prefix}vtm_vtm vtm ON vtm.vtmid = vmp.vtmid
	    LEFT JOIN {prefix}vmp_drug_form dft ON dft.vpid = amp.vpid
	    LEFT JOIN {prefix}lookup_form fhit ON fhit.cd = dft.formcd
	    LEFT JOIN medication_form mf ON mf.term COLLATE utf8_general_ci = fhit.desc AND mf.source_type = 'DM+D'
	WHERE short_term.desc != vmp.nm
		AND amp.invalid != '1'
	) AS x
GROUP BY x.preferred_term
;

-- NOTE: The form and route should not be added here. It creates duplicates. I've put in a temporary hack by adding a unique index and using INSERT IGNORE to ignore the duplicates
-- But this could lead to some odd selections when multiple forms/routes exist (only the first option will be inserted)
-- TODO: remove joins for route and form here (instert to medication without default form/route), then add the form and route later in the process from the 'attributes', giving priority to 'Eye' route.