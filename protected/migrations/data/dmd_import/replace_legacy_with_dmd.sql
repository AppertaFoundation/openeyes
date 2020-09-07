START TRANSACTION;

CREATE TEMPORARY TABLE IF NOT EXISTS tmp_medication_match
SELECT legacy.id AS legacy_id, legacy.preferred_term AS legacy_term, legacy.preferred_code AS legacy_code, legacy.source_old_id AS legacy_old_id, legacy.short_term AS legacy_short_term, dmd.id AS dmd_id, dmd.preferred_term AS dmd_term, dmd.preferred_code AS dmd_code, 'medication' as source 
FROM medication AS legacy
LEFT JOIN medication AS dmd ON dmd.preferred_code = legacy.preferred_code
WHERE legacy.source_type = 'LEGACY' AND legacy.source_subtype = 'medication_drug'
AND dmd.source_type = 'DM+D';

-- Add any items that failed to match on an id, but have an exact name match
INSERT INTO tmp_medication_match
SELECT legacy.id AS legacy_id, legacy.preferred_term AS legacy_term, legacy.preferred_code AS legacy_code, legacy.source_old_id AS legacy_old_id, legacy.short_term AS legacy_short_term, dmd.id AS dmd_id, dmd.preferred_term AS dmd_term, dmd.preferred_code AS dmd_code, 'medication' as source 
FROM medication AS legacy
LEFT JOIN medication AS dmd ON dmd.preferred_term = legacy.preferred_term
WHERE legacy.source_type = 'LOCAL' AND legacy.source_subtype = 'medication_drug'
AND dmd.source_type = 'DM+D'
AND legacy.id not in (SELECT legacy_id from tmp_medication_match);

-- Now add formulary 'drug' matches
INSERT INTO tmp_medication_match
SELECT legacy.id AS legacy_id, legacy.preferred_term AS legacy_term, legacy.preferred_code AS legacy_code, legacy.source_old_id AS legacy_old_id, legacy.short_term AS legacy_short_term, dmd.id AS dmd_id, dmd.preferred_term AS dmd_term, dmd.preferred_code AS dmd_code, 'drug' as source 
FROM medication legacy
JOIN medication dmd
INNER JOIN drug d ON d.id = legacy.source_old_id 
WHERE legacy.source_type = 'LEGACY'  
	AND legacy.source_subtype = 'drug'
	AND dmd.source_type = 'DM+D'
	AND d.national_code = dmd.preferred_code;

-- Add any 'drug' items that failed to match on an id, but have an exact name match
INSERT INTO tmp_medication_match
SELECT legacy.id AS legacy_id, legacy.preferred_term AS legacy_term, legacy.preferred_code AS legacy_code, legacy.source_old_id AS legacy_old_id, legacy.short_term AS legacy_short_term, dmd.id AS dmd_id, dmd.preferred_term AS dmd_term, dmd.preferred_code AS dmd_code, 'drug' as source 
FROM drug d 
LEFT JOIN medication AS legacy ON d.id = legacy.source_old_id 
LEFT JOIN medication AS dmd ON dmd.preferred_term = legacy.preferred_term 
WHERE legacy.source_type = 'LEGACY'
	AND legacy.source_subtype = 'drug' 
	AND dmd.source_type = 'DM+D'
	AND legacy.id not in (SELECT legacy_id from tmp_medication_match WHERE source = 'drug');

-- add an index to speed things up
ALTER TABLE tmp_medication_match ADD INDEX legacy_id_idx (`legacy_id`);

-- set the source_old_id for merged dm+d drugs (just in case we need them later)
-- definitely not needed for medication drugs
-- set the old 'short_term' for both medications AND drugs, where the short_term != preferred_term
UPDATE medication m
INNER JOIN tmp_medication_match t ON m.id = t.dmd_id
SET m.source_old_id = t.legacy_old_id, m.short_term = t.legacy_short_term
WHERE t.source = 'drug' 
	AND t.legacy_old_id IS NOT NULL
	AND t.legacy_short_term != t.dmd_term;

UPDATE medication m
INNER JOIN tmp_medication_match t ON m.id = t.dmd_id
SET m.short_term = t.legacy_short_term
WHERE t.source = 'medication' 
	AND t.legacy_old_id IS NOT NULL
	AND t.legacy_short_term != t.dmd_term;

-- Replace all uses of the old med with the new
UPDATE event_medication_use AS emu
LEFT JOIN tmp_medication_match AS tmp ON tmp.legacy_id = emu.medication_id
SET emu.medication_id = tmp.dmd_id
WHERE tmp.legacy_id IS NOT NULL;

-- Replace all set entries for the old med with the new
UPDATE medication_set_item AS msi
LEFT JOIN tmp_medication_match AS tmp ON tmp.legacy_id = msi.medication_id
SET msi.medication_id = tmp.dmd_id
WHERE tmp.legacy_id IS NOT NULL;

-- replace all the auto-set entries of the old with the new
UPDATE medication_set_auto_rule_medication msarm
JOIN tmp_medication_match tmm
  ON msarm.medication_id = tmm.legacy_id
SET msarm.medication_id = tmm.dmd_id;

-- Combine any medication atrributes from old with the new
-- duplicates should be dropped automatically becasue of a unique index + the IGNORE INTO
INSERT IGNORE INTO medication_attribute_assignment (medication_id,medication_attribute_option_id)
SELECT t.dmd_id, a.medication_attribute_option_id 
FROM tmp_medication_match t
	INNER JOIN medication_attribute_assignment a ON a.medication_id = t.legacy_id;

-- Add alternative terms of the old drug to the new
INSERT IGNORE INTO medication_search_index (medication_id, alternative_term)
SELECT t.dmd_id, a.alternative_term 
FROM tmp_medication_match t
	INNER JOIN medication_search_index a ON a.medication_id = t.legacy_id; 

DELETE FROM medication_search_index WHERE medication_id IN (SELECT legacy_id FROM tmp_medication_match);
DELETE FROM medication_attribute_assignment WHERE medication_id IN (SELECT legacy_id FROM tmp_medication_match);
DELETE FROM medication WHERE id IN (SELECT legacy_id FROM tmp_medication_match);

UPDATE medication SET source_type = 'LOCAL' WHERE source_type='LEGACY';

DROP TABLE tmp_medication_match;

COMMIT;