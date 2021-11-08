<?php

namespace OEModule\BreakGlass;

use Yii;
use Patient;
use OEWebUser;
use User;

class BreakGlass
{

    private $patient;
    private $patient_created_by;
    private $user;

    public function __construct(Patient $patient, OEWebUser $user)
    {
        $this->patient = $patient;
        $this->patient_created_by = User::model()->findByPk($patient->created_user_id);
        $this->user = User::model()->findByPk($user->getId());
    }

    private function resolveHealthboard(string $in)
    {
        if (preg_match("/^(?:NHS Grampian|NHSGrampian|NHSG)$/", $in)) {
            return "NHS Grampian";
        } elseif (preg_match("/^(?:NHS Forth Valley|NHSFV)$/", $in)) {
            return "NHS Forth Valley";
        }
        return null;
    }

    public function patientHealthboard()
    {
        if ($this->patient_created_by) {
            return $this->resolveHealthboard($this->patient_created_by->{Yii::app()->params['breakglass_field']});
        }
        return null;
    }

    public function userHealthboard()
    {
        if ($this->user) {
            return $this->resolveHealthboard($this->user->{Yii::app()->params['breakglass_field']});
        }
        return null;
    }

    public function breakGlassRequired()
    {
        $id = $this->patient->id;
        if (isset($_SESSION['breakglass_break_'.$id]) && $_SESSION['breakglass_break_'.$id]) {
            return false;
        }

        $user_healthboard = $this->userHealthboard();
        $patient_healthboard = $this->patientHealthboard();

        if ($user_healthboard && $patient_healthboard && $user_healthboard === $patient_healthboard) {
            return false;
        }

        $_SESSION['breakglass_challengefor'] = $id;
        return true;
    }

    public function getPath()
    {
        return "/BreakGlass";
    }
}
