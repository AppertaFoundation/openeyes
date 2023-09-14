<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @property OphInBiometry_API $api
 */
class OphInBiometry_APITest extends OEDbTestCase
{
    private $api;

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphInBiometry');
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public static function tearDownAfterClass(): void
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function setUp(): void
    {
        parent::setUp();

        $dataContext = new DataContext(Yii::app(), ['subspecialties' => Subspecialty::model()->findByPk(2)]);
        $this->api = new OphInBiometry_API(Yii::app(), $dataContext);
    }

    public function testGetPredictedRefractionWarningWhenTargetRefractionIsText()
    {
            $this->assertNull($this->api
                ->getPredictedRefractionDiffersFromTargetRefractionWarning(1, 'text'));
    }

    public function testGetPredictedRefractionWarningWhenPredictedRefractionIsText()
    {
        $this->assertNull($this->api
            ->getPredictedRefractionDiffersFromTargetRefractionWarning('text', 1));
    }

    public function testGetPredictedRefractionWarningWhenBothValuesAreText()
    {
        $this->assertNull($this->api
            ->getPredictedRefractionDiffersFromTargetRefractionWarning('text', 'text'));
    }

    public function testGetPredictedRefractionWarningWhenValuesDifferenceLessThanThreshold()
    {
        $this->assertNull(
            $this->api
            ->getPredictedRefractionDiffersFromTargetRefractionWarning('-3','-2.75' ));
    }

    public function testGetPredictedRefractionWarningWhenValuesDifferenceMoreThanThreshold()
    {
        $this->assertEquals(
            Element_OphInBiometry_Calculation::$PREDICTED_REFRACTION_DIFFERS_FROM_TARGET_REFRACTION_WARNING,
            $this->api
            ->getPredictedRefractionDiffersFromTargetRefractionWarning('-3.00', '-2.00'));
    }
}
