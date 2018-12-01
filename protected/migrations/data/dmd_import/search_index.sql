INSERT INTO medication_search_index (medication_id, alternative_term)
    SELECT id, preferred_term FROM medication WHERE source_type = 'DM+D';