<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphCiExamination_DiagnosesTest extends CDbTestCase
{
    /**
     * @var /OEModule/OphCiExamination/models/Element_OphCiExamination_Diagnoses
     */
    protected $model;
    public $fixtures = array(
        'finding' => '\Finding',
        'elFurtherFindings' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings',
        'furtherFindingsAssignment' => 'OEModule\OphCiExamination\models\OphCiExamination_FurtherFindings_Assignment',
        'elDiagnoses' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses',
        'disorders' => '\Disorder',
        'diagnoses' => 'OEModule\OphCiExamination\models\OphCiExamination_Diagnosis',
        'common_oph' => '\CommonOphthalmicDisorder',
        'secto' => '\SecondaryToCommonOphthalmicDisorder',
        'firms' => '\Firm',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses();
    }

    /**
     * @covers /OEModule/OphCiExamination/models/Element_OphCiExamination_Diagnoses::model
     */
    public function testModel()
    {
        $this->assertInstanceOf(
            OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::class,
            OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::model(),
            'Class name should match model.'
        );
    }

    /**
     * @covers /OEModule/OphCiExamination/models/Element_OphCiExamination_Diagnoses::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophciexamination_diagnoses', $this->model->tableName());
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_FurtherFindings()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        $etDiagString = $this->elDiagnoses('et_further_diagnoses1')->getLetter_string();
        $this->assertEquals("Further Findings: Finding 2\n", strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        $etDiagString = $this->elDiagnoses('et_further_diagnoses3')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Left Myopia\n", strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        $etDiagString = $this->elDiagnoses('et_further_diagnoses4')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Right Myopia\n", strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_and_secondary()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        $etDiagString = $this->elDiagnoses('et_further_diagnoses5')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Left Retinal lattice degeneration\nSecondary diagnosis: Right Myopia\n", strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_and_secondary_and_findings()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        $etDiagString = $this->elDiagnoses('et_further_diagnoses6')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Left Retinal lattice degeneration\nSecondary diagnosis: Right Myopia\nFurther Findings: Finding 3: test twotwotwo, Finding 1\n", strip_tags($etDiagString));
    }

    /*
    parent primary disorder with secondary secto disorder
    letter macro text defined
    same eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_with_secondaryto_secondary_with_letter_macro_text_same_eye()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses7')->getLetter_string();
        $this->assertEquals("Left testing blahblah7\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent primary disorder with secondary secto disorder for *different* subspecialty
    letter macro text defined
    same eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_with_secondaryto_secondary_with_letter_macro_text_same_eye_different_subspecialty()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 1;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses7')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Left Vitreous haemorrhage\nSecondary diagnosis: Left Retinal lattice degeneration\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent primary disorder with secondary secto disorder
    letter macro text NOT defined
    same eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_with_secondaryto_secondary_withOUT_letter_macro_text_same_eye()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses11')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Left Vitreous haemorrhage\nSecondary diagnosis: Left Posterior vitreous detachment\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent secondary disorder with secondary secto disorder
    letter macro text defined
    same eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary_with_secondaryto_secondary_with_letter_macro_text_same_eye()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses8')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Left testing blahblah7\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent primary disorder with secondary secto disorder
    letter macro text defined
    different eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_with_secondaryto_secondary_with_letter_macro_text_diff_eyes()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses9')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Right Vitreous haemorrhage\nSecondary diagnosis: Left Retinal lattice degeneration\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent secondary disorder with secondary secto disorder
    letter macro text defined
    different eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary_with_secondaryto_secondary_with_letter_macro_text_diff_eyes()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses10')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Right Retinal lattice degeneration\nSecondary diagnosis: Left Vitreous haemorrhage\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent secondary disorder with primary secto disorder
    letter macro text defined
    same eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary_with_secondaryto_primary_with_letter_macro_text_same_eyes()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses12')->getLetter_string();
        $this->assertEquals("Left testing blahblah7\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent secondary disorder with primary secto disorder
    letter macro text defined
    different eyes
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary_with_secondaryto_primary_with_letter_macro_text_diff_eyes()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses13')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Right Retinal lattice degeneration\nSecondary diagnosis: Left Vitreous haemorrhage\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent finding with primary secto disorder
    letter macro text defined
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_finding_with_secondaryto_primary_with_letter_macro_text()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses14')->getLetter_string();
        $this->assertEquals("Right combined finding maculoppithy\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent finding with secondary secto disorder
    letter macro text defined
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_finding_with_secondaryto_secondary_with_letter_macro_text()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses15')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Right combined finding maculoppithy\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent primary disorder with finding secto
    letter macro text defined
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_with_secondaryto_finding_with_letter_macro_text()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses16')->getLetter_string();
        $this->assertEquals("Right test test 1234\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    parent secondary disorder with finding secto
    letter macro text defined
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary_with_secondaryto_finding_with_letter_macro_text()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses17')->getLetter_string();
        $this->assertEquals("Secondary diagnosis: Right test test 1234\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }

    /*
    multiple pairs of findings and diagnoses
    */
    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_multiple()
    {
        $this->markTestIncomplete('Fixture data inconsistent with output from getLetter_string.');
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses18')->getLetter_string();
        $this->assertEquals("Principal diagnosis: Right Myopia\nSecondary diagnosis: Left test test 4567\nSecondary diagnosis: Left test test 1234\nSecondary diagnosis: Right Retinal lattice degeneration\nFurther Findings: Finding 1, Finding 4\n", strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }
}
