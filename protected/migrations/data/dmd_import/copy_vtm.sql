INSERT INTO openeyes.medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code)
  SELECT
    'DM+D' AS source_type,
    'VTM' AS source_subtype,

    vtm.nm AS preferred_term,
    vtm.vtmid AS preferred_code,

    vtm.nm AS vtm_term,
    vtm.vtmid AS vtm_code
  FROM {prefix}vtm_vtm vtm;