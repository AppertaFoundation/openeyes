INSERT INTO openeyes.ref_medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
SELECT
	'DM+D' AS source_type,
	'AMP' AS source_subtype,

	amp.desc AS preferred_term,
	amp.apid AS preferred_code,

	NULL,
	NULL,

	NULL,
	NULL,

	amp.desc AS amp_term,
	amp.apid AS amp_code
FROM
	drugs2.f_amp_amps amp;