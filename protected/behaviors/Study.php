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
 * Study Trait
 *
 * This trait provides shared functionality that will be found across all studies.
 */
trait Study {

    /**
     * @param User $user
     *
     *  @return bool
     */
    public function canBeProposedByUser(CWebUser $user)
    {
        foreach ($this->proposers as $proposer) {
            if ($proposer->id === $user->id || $user->checkAccess('Genetics Admin')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     *
     *  @return bool
     */
    public function canBeProposedByUserDateCheck(CWebUser $user)
    {

        if ( (Helper::isValidDateTime($this->end_date)) && (new DateTime($this->end_date) < new DateTime('midnight')) ) {
            return false;
        }
        return true;
    }

    /**
     * Returns a list of studies a subject is participating in.
     *
     * Requires the name of the pivot table being used to be set in the class.
     *
     * @param BaseActiveRecord  $subject
     *
     * @return array
     */
    public function participatingStudyIds(BaseActiveRecord $subject)
    {
        if (!$subject->id) {
            return array();
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('subject_id = ?');
        $criteria->select = 'study_id';
        $criteria->params = array($subject->id);
        $existing_studies = $this->getCommandBuilder()
            ->createFindCommand($this->pivot, $criteria)
            ->queryAll();

        $ids = array();
        if ($existing_studies) {
            foreach ($existing_studies as $study) {
                $ids[] = $study['study_id'];
            }
        }

        return $ids;
    }

    /**
     * @param BaseActiveRecord $subject
     *
     * @return CActiveRecord
     */
    public function participationForSubject(BaseActiveRecord $subject)
    {
        $model = new $this->pivot_model();

        return $model->findByAttributes(array('study_id' => $this->id, 'subject_id' => $subject->id));
    }
}
