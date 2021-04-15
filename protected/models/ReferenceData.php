<?php

/**
 * Class ReferenceData
 */
final class ReferenceData
{
    public const ALL_LEVELS = array(
        self::LEVEL_INSTITUTION,
        self::LEVEL_SITE,
        self::LEVEL_SPECIALTY,
        self::LEVEL_SUBSPECIALTY,
        self::LEVEL_FIRM,
        self::LEVEL_USER,
        );

    /**
     * Institution level
     */
    public const LEVEL_INSTITUTION = 1;

    /**
     * Site level
     */
    public const LEVEL_SITE = 2;

    /**
     * Specialty level.
     */
    public const LEVEL_SPECIALTY = 4;

    /**
     * Subspecialty level
     */
    public const LEVEL_SUBSPECIALTY = 8;

    /**
     * Firm level
     */
    public const LEVEL_FIRM = 16;

    /**
     * User level
     */
    public const LEVEL_USER = 32;

    /**
     * All levels. Use this as shorthand for supporting saving/querying reference data at all levels.
     */
    public const LEVEL_ALL = 63;
}
