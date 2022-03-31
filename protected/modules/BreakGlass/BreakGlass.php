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
        if (preg_match("/^(?:NHS Ayrshire & Arran|NHSAA|AA)\s*$/", $in)) {
            return "NHS Ayrshire & Arran";
        } elseif (preg_match("/^(?:NHS Borders|NHSBOR|BOR)\s*$/", $in)) {
            return "NHS Borders";
        } elseif (preg_match("/^(?:NHS Dumfries & Galloway|NHSDG|DG)\s*$/", $in)) {
            return "NHS Dumfries & Galloway";
        } elseif (preg_match("/^(?:NHS Fife|NHSFIFE|FIFE)\s*$/", $in)) {
            return "NHS Fife";
        } elseif (preg_match("/^(?:NHS Forth Valley|NHSFV|FV)\s*$/", $in)) {
            return "NHS Forth Valley";
        } elseif (preg_match("/^(?:NHS Grampian|NHSGrampian|NHSG|NHSGRAM|GRAM)\s*$/", $in)) {
            return "NHS Grampian";
        } elseif (preg_match("/^(?:NHS Greater Glasgow & Clyde|NHSGGC|GGC)\s*$/", $in)) {
            return "NHS Greater Glasgow & Clyde";
        } elseif (preg_match("/^(?:NHS Highland|NHSH)\s*$/", $in)) {
            return "NHS Highland";
        } elseif (preg_match("/^(?:NHS Lanarkshire|NHSLAN|LAN)\s*$/", $in)) {
            return "NHS Lanarkshire";
        } elseif (preg_match("/^(?:NHS Lothian|NHSLOTH|LOTH)\s*$/", $in)) {
            return "NHS Lothian";
        } elseif (preg_match("/^(?:NHS Orkney|NHSORK|ORK)\s*$/", $in)) {
            return "NHS Orkney";
        } elseif (preg_match("/^(?:NHS Shetland|NHSSHET|SHET)\s*$/", $in)) {
            return "NHS Shetland";
        } elseif (preg_match("/^(?:NHS Tayside|NHSTAY|TAY)\s*$/", $in)) {
            return "NHS Tayside";
        } elseif (preg_match("/^(?:NHS Western Isles|NHSWI|WI)\s*$/", $in)) {
            return "NHS Western Isles";
        }
        return null;
    }

    public function patientHealthboard()
    {
        $health_board = null;
        if ($this->patient->contact->address) {
            $health_board = $this->resolveHealthboard($this->patient->contact->address->county);
        }

        // If the patient's county doesn't match with a board, fall back to health board of created user
        if (!$health_board && $this->patient_created_by) {
            $health_board = $this->resolveHealthboard($this->patient_created_by->{Yii::app()->params['user_breakglass_field']});
        }

        return $health_board;
    }

    public function userHealthboard()
    {
        if ($this->user) {
            return $this->resolveHealthboard($this->user->{Yii::app()->params['user_breakglass_field']});
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
