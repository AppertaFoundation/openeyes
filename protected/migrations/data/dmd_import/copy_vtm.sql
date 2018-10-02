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