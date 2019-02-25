INSERT INTO medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
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
	{prefix}amp_amps amp
	LEFT JOIN {prefix}vmp_vmps vmp ON vmp.vpid = amp.vpid
	LEFT JOIN {prefix}vtm_vtm vtm ON vtm.vtmid = vmp.vtmid
;