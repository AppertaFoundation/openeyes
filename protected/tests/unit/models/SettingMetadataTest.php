<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @covers SettingMetadata
 */
class SettingMetadataTest extends ActiveRecordTestCase
{
    /**
     * @var SettingMetadata $model
     */
    protected SettingMetadata $model;
    public $fixtures = array(
        'setting_installation' => 'SettingInstallation',
        'setting_institution' => 'SettingInstitution',
        'institution' => 'Institution',
    );

    protected array $columns_to_skip = [
        'default_value',
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new SettingMetadata();
    }

    public function testModel(): void
    {
        self::assertInstanceOf(
            SettingMetadata::class,
            SettingMetadata::model(),
            'Class name should match model.'
        );
    }

    public function testTableName(): void
    {
        self::assertEquals('setting_metadata', $this->model->tableName());
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @dataProvider settingMetadataProvider
     * @param $key
     * @param $value
     * @param $expected
     */
    public function testCheckSetting($key, $value, $expected): void
    {
        Yii::app()->session['selected_site_id'] = null;
        $result = SettingMetadata::checkSetting($key, strtolower($value));
        self::assertSame($expected, $result);
    }

    /**
     * @dataProvider settingMetadataProvider
     * @param $key
     * @param $value
     * @param $expected
     * @param $allowedClasses
     */
    public function testGetSettingName($key, $value, $expected, $allowedClasses): void
    {
        SettingMetadata::resetCache();
        Yii::app()->session['selected_site_id'] = 1;
        $settingMetadata = new SettingMetadata();
        $setting = $settingMetadata->getSettingName($key, $allowedClasses);
        self::assertSame($value, $setting);
    }

    public function settingMetadataProvider(): array
    {
        return [
            'dataProvider1'  => ['global_institution_remote_id', 'NHS', true, ['SettingInstallation']],
            'dataProvider2'  => ['global_institution_remote_id', 'FOO', false, ['SettingInstitution']],
        ];
    }
}
