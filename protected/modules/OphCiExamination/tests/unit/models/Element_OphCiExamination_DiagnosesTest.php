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
class Element_OphCiExamination_DiagnosesTest extends ActiveRecordTestCase
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

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses();
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::model
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
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::tableName
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
        $etDiagString = $this->elDiagnoses('et_further_diagnoses1')->getLetter_string();
        $this->assertStringContainsString('Finding 2', strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary()
    {
        $etDiagString = $this->elDiagnoses('et_further_diagnoses3')->getLetter_string();
        $this->assertStringContainsString('Principal: LEFT Myopia', strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_secondary()
    {
        $etDiagString = $this->elDiagnoses('et_further_diagnoses4')->getLetter_string();
        $this->assertStringContainsString('RIGHT Myopia', strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_and_secondary()
    {
        $etDiagString = $this->elDiagnoses('et_further_diagnoses5')->getLetter_string();
        $this->assertStringContainsString('Principal: LEFT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('RIGHT Myopia', strip_tags($etDiagString));
    }

    /**
     * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::getLetter_string
     */
    public function testGetLetter_string_Diagnoses_primary_and_secondary_and_findings()
    {
        $etDiagString = $this->elDiagnoses('et_further_diagnoses6')->getLetter_string();
        $this->assertStringContainsString('Principal: LEFT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('RIGHT Myopia', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 3 : test twotwotwo', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 1', strip_tags($etDiagString));
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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses7')->getLetter_string();
        $this->assertStringContainsString('Principal: LEFT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Retinal lattice degeneration', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 1;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses7')->getLetter_string();
        $this->assertStringContainsString('Principal: LEFT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString("LEFT Retinal lattice degeneration\n", strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses11')->getLetter_string();
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Posterior vitreous detachment', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses8')->getLetter_string();
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Retinal lattice degeneration', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses9')->getLetter_string();
        $this->assertStringContainsString('Principal: RIGHT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Retinal lattice degeneration', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses10')->getLetter_string();
        $this->assertStringContainsString('RIGHT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses12')->getLetter_string();
        $this->assertCoassertStringContainsStringntains('Principal: LEFT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses13')->getLetter_string();
        $this->assertStringContainsString('Principal: RIGHT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses14')->getLetter_string();
        $this->assertContains('RIGHT Essential hypertension', strip_tags($etDiagString));
        $this->assertContains('Finding 1', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses15')->getLetter_string();
        $this->assertStringContainsString('RIGHT Essential hypertension', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 1', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses16')->getLetter_string();
        $this->assertContains('RIGHT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertContains('Finding 2', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses17')->getLetter_string();
        $this->assertStringContainsString('RIGHT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 2', strip_tags($etDiagString));

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
        Yii::app()->session['selected_firm_id'] = 5;

        $etDiagString = $this->elDiagnoses('et_further_diagnoses18')->getLetter_string();
        $this->assertStringContainsString('Principal: RIGHT Myopia', strip_tags($etDiagString));
        $this->assertStringContainsString('RIGHT Retinal lattice degeneration', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Posterior vitreous detachment', strip_tags($etDiagString));
        $this->assertStringContainsString('LEFT Vitreous haemorrhage', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 1', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 2', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 3', strip_tags($etDiagString));
        $this->assertStringContainsString('Finding 4', strip_tags($etDiagString));

        unset(Yii::app()->session['selected_firm_id']);
    }
}
