ALTER TABLE medication ADD INDEX vpid_id (`source_type`);
ALTER TABLE medication ADD INDEX vpid_id (`preferred_code`);
ALTER TABLE medication ADD INDEX vpid_id (`source_subtype`);

CREATE TEMPORARY TABLE tmp_medication_match
SELECT legacy.id AS legacy_id, legacy.preferred_term AS legacy_term, legacy.preferred_code AS legacy_code, dmd.id AS dmd_id, dmd.preferred_term AS dmd_term, dmd.preferred_code AS dmd_code
FROM medication AS legacy
LEFT JOIN medication AS dmd ON dmd.preferred_code = legacy.preferred_code
WHERE legacy.source_type = 'LEGACY' AND legacy.source_subtype = 'medication_drug'
AND dmd.source_type = 'DM+D';

ALTER TABLE tmp_medication_match ADD INDEX legacy_id_idx (`legacy_id`);

UPDATE event_medication_use AS emu
LEFT JOIN tmp_medication_match AS tmp ON tmp.legacy_id = emu.medication_id
SET emu.medication_id = tmp.dmd_id
WHERE tmp.legacy_id IS NOT NULL;

UPDATE medication_set_item AS msi
LEFT JOIN tmp_medication_match AS tmp ON tmp.legacy_id = msi.medication_id
SET msi.medication_id = tmp.dmd_id
WHERE tmp.legacy_id IS NOT NULL;

DELETE FROM medication_search_index WHERE medication_id IN (SELECT legacy_id FROM tmp_medication_match);
DELETE FROM medication_attribute_assignment WHERE medication_id IN (SELECT legacy_id FROM tmp_medication_match);
DELETE FROM medication WHERE id IN (SELECT legacy_id FROM tmp_medication_match);

UPDATE medication SET source_type = 'LOCAL' WHERE source_type='LEGACY';
