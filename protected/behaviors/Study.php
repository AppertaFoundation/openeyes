<?php

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
        if(!$subject->id) {
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