INSERT INTO ref_medications_search_index (ref_medication_id, alternative_term)
    SELECT id, preferred_term FROM ref_medication WHERE source_type = 'DM+D';