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
 * This is the model class for table "pedigree".
 *
 * The followings are the available columns in table 'issue':
 *
 * @property int $id
 * @property int $inheritance_id
 * @property string $comments
 * @property int $consanguinity
 * @property int $gene_id
 * @property string $base_change
 * @property string $amino_acid_change
 * @property int $disorder_id
 * @property int $members
 * @property int $affecteds
 *
 * The followings are the available model relations:
 * @property PedigreeInheritance $inheritance
 * @property PedigreeGene $gene
 * @property Disorder $disorder
 */
class Pedigree extends BaseActiveRecord
{
    protected $lowest_version = 37;

    protected $highest_version = 38;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Issue the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pedigree';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'inheritance_id, comments, consanguinity, gene_id, base_change, amino_acid_change, disorder_id, ' .
                'base_change_id, amino_acid_change_id, genomic_coordinate, genome_version, gene_transcript',
                'safe'
            ),
            array('consanguinity', 'required')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'inheritance' => array(self::BELONGS_TO, 'PedigreeInheritance', 'inheritance_id'),
            'gene' => array(self::BELONGS_TO, 'PedigreeGene', 'gene_id'),
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'subjects' =>  array(
                self::MANY_MANY,
                'GeneticsPatient',
                'genetics_patient_pedigree(patient_id, pedigree_id)',
                'with' => 'patient',
            ),
            'members' => array(self::HAS_MANY, 'GeneticsPatientPedigree', 'pedigree_id', 'with' => array('patient')),
            'base_change_type' => array(self::BELONGS_TO, 'PedigreeBaseChangeType', 'base_change_id'),
            'amino_acid_change_type' => array(self::BELONGS_TO, 'PedigreeAminoAcidChangeType', 'amino_acid_change_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Family ID',
            'inheritance_id' => 'Inheritance',
            'base_change' => 'Base Change',
            'gene_id' => 'Gene',
            'amino_acid_change' => 'Amino Acid Change',
            'disorder_id' => 'Disorder',
            'disorder.fully_specified_name' => 'Disorder',
            'base_change_id' => 'Base Change Type',
            'amino_acid_change_id' => 'Amino Acid Change Type',
            'getSubjectsCount' => 'Subjects count',
            'getAffectedSubjectsCount' => 'Affected subjects count',
            'getConsanguinityAsBoolean' => 'Consanguinity',
            'disorder.term' => 'Disorder'
        );
    }

    public function getConsanguinityAsBoolean()
    {
        return $this->consanguinity == 1;
    }

    /**
     * Get the possible versions for a genome
     *
     * @return array
     */
    public function genomeVersions()
    {
        return range($this->lowest_version, $this->highest_version);
    }

    /**
     * Updates the diagnosis of the pedigree to be the most common diagnosis of the pedigrees members.
     */
    public function updateDiagnosis()
    {
        $sql = 'SELECT
                 disorder.id,
                 count(disorder.id),
                 disorder.term
                FROM pedigree
                 JOIN genetics_patient_pedigree ON pedigree.id = genetics_patient_pedigree.pedigree_id
                 JOIN genetics_patient ON genetics_patient.id = genetics_patient_pedigree.patient_id
                 JOIN genetics_patient_diagnosis on genetics_patient_pedigree.patient_id = genetics_patient_diagnosis.patient_id
                 JOIN disorder ON genetics_patient_diagnosis.disorder_id = disorder.id
                WHERE pedigree.id = ' . $this->id . '
                GROUP BY disorder.id
                LIMIT 1';

        $query = $this->getDbConnection()->createCommand($sql);
        $diagnosis = $query->queryRow();

        if ($diagnosis) {
            $this->disorder_id = $diagnosis['id'];
        } else {
            $this->disorder_id = null;
        }

        $this->save();
    }

    /**
     * @return array|CDbDataReader
     */
    public function getAllIdAndText()
    {
        $sql = "SELECT pedigree.id, IF(pedigree_gene.name IS NULL, pedigree.id, CONCAT_WS(' ', pedigree.id, '(Gene: ', pedigree_gene.name, ')') )  AS text
                FROM pedigree
                LEFT JOIN pedigree_gene on pedigree_gene.id = pedigree.gene_id";

        return $this->getDbConnection()->createCommand($sql)->queryAll();
    }

    /**
     * @return int
     */
    public function getSubjectsCount()
    {
        return count($this->subjects);
    }

    /**
     * @return int
     */
    public function getAffectedSubjectsCount()
    {
        $count = 0;
        foreach ($this->subjects as $subject) {
            if ($subject->statusForPedigree($this->id) == 'Affected') {
                $count++;
            }
        }
        return $count;
    }
}
