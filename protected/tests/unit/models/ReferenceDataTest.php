<?php

use PHPUnit\Framework\TestCase;

class ReferenceDataTest extends TestCase
{
    /**
     * Get level mask fixtures.
     *
     * @return array
     */
    public function getLevelMasks(): array
    {
        return array(
            'Single level' => array(
                'level_mask' => ReferenceData::LEVEL_INSTALLATION,
                'expected' => ReferenceData::LEVEL_INSTALLATION
            ),
            'Multiple sequential levels' => array(
                'level_mask' => ReferenceData::LEVEL_INSTITUTION | ReferenceData::LEVEL_INSTALLATION,
                'expected' => ReferenceData::LEVEL_INSTITUTION
            ),
            'Multiple non-sequential levels' => array(
                'level_mask' => ReferenceData::LEVEL_FIRM | ReferenceData::LEVEL_INSTITUTION,
                'expected' => ReferenceData::LEVEL_FIRM
            ),
            'All levels' => array(
                'level_mask' => ReferenceData::LEVEL_ALL,
                'expected' => ReferenceData::LEVEL_USER
            ),
            'No levels' => array(
                'level_mask' => ReferenceData::LEVEL_NONE,
                'expected' => ReferenceData::LEVEL_NONE
            ),
        );
    }

    /**
     * Test getting the lowest setting level.
     *
     * @dataProvider getLevelMasks
     *
     * @param int $level_mask
     * @param int $expected
     * @return void
     */
    public function testGetLowestSettingLevel(int $level_mask, int $expected)
    {
        $actual = ReferenceData::getLowestSettingLevel($level_mask);
        $this->assertEquals($expected, $actual);
    }
}
