<?php

/**
 * Class ReferenceData
 * Contains constants for reference data levels
 */
final class ReferenceData
{
    public const ALL_LEVELS = array(
        self::LEVEL_INSTALLATION,
        self::LEVEL_INSTITUTION,
        self::LEVEL_SITE,
        self::LEVEL_SPECIALTY,
        self::LEVEL_SUBSPECIALTY,
        self::LEVEL_FIRM,
        self::LEVEL_USER,
    );

    public const LEVEL_REFS = array(
        ReferenceData::LEVEL_INSTALLATION => 'all',
        ReferenceData::LEVEL_INSTITUTION => 'institution_id',
        ReferenceData::LEVEL_SITE => 'site_id',
        ReferenceData::LEVEL_SPECIALTY => 'specialty_id',
        ReferenceData::LEVEL_SUBSPECIALTY => 'subspecialty_id',
        ReferenceData::LEVEL_FIRM => 'firm_id',
        ReferenceData::LEVEL_USER => 'user_id',
    );

    /**
     * Installation level (layer 1)
     */
    public const LEVEL_INSTALLATION = 1 << 0;

    /**
     * Institution level (layer 2)
     */
    public const LEVEL_INSTITUTION = 1 << 1;

    /**
     * Site level (layer 3)
     */
    public const LEVEL_SITE = 1 << 2;

    /**
     * Specialty level (layer 4)
     */
    public const LEVEL_SPECIALTY = 1 << 3;

    /**
     * Subspecialty level (layer 5)
     */
    public const LEVEL_SUBSPECIALTY = 1 << 4;

    /**
     * Firm level (layer 6)
     */
    public const LEVEL_FIRM = 1 << 5;

    /**
     * User level (layer 7)
     */
    public const LEVEL_USER = 1 << 6;

    /**
     * All levels. Use this as shorthand for supporting saving/querying reference data at all levels.
     */
    public const LEVEL_ALL = 127;

    /**
     * No levels. This is a special case that should only ever be used for bitwise operations and not as an actual supported level.
     */
    public const LEVEL_NONE = 0;

    /**
     * Get the lowest setting level specified in the given bitmask.
     *
     * @param integer $level_mask
     * @return int
     */
    public static function getLowestSettingLevel(int $level_mask)
    {
        $level_hierarchy = array_reverse(self::ALL_LEVELS);
        foreach ($level_hierarchy as $level) {
            if ($level_mask & $level) {
                return $level;
            }
        }
        // If no levels are supported, return 0.
        return self::LEVEL_NONE;
    }
}
