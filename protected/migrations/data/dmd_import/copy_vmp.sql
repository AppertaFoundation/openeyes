INSERT INTO openeyes.ref_medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code,amp_term,amp_code)
  SELECT
    'DM+D' AS source_type,
    'VMP' AS source_subtype,

    vmp.nm AS preferred_term,
    vmp.vpid AS preferred_code,

    NULL,
    NULL,

    vmp.nm AS vmp_term,
    vmp.vpid AS vmp_code,

    amp.desc AS amp_term,
    amp.apid AS amp_code
  FROM
    drugs2.f_vmp_vmps vmp
    LEFT JOIN drugs2.f_amp_amps amp ON vmp.vpid = amp.vpid
