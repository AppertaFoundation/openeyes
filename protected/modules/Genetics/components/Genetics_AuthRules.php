<?php

class Genetics_AuthRules
{
    /**
     * @return bool
     */
    public function canViewStudy()
    {
        //everyone can view the list
        if (!Yii::app()->request->getQuery('id')) {
            return true;
        }

        $user = User::model()->findByPk(Yii::app()->user->id);
        $study = GeneticsStudy::model()->findByPk(Yii::app()->request->getQuery('id'));

        return $study->isUserProposer($user);
    }
}
