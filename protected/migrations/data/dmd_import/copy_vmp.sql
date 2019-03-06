INSERT INTO medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code)
  SELECT
    'DM+D' AS source_type,
    'VMP' AS source_subtype,

    vmp.nm AS preferred_term,
    vmp.vpid AS preferred_code,

    vtm.nm AS vtm_term,
    vtm.vtmid AS vtm_code,

    vmp.nm AS vmp_term,
    vmp.vpid AS vmp_code

  FROM
    {prefix}vmp_vmps vmp
    LEFT JOIN {prefix}vtm_vtm vtm ON vtm.vtmid = vmp.vtmid;
