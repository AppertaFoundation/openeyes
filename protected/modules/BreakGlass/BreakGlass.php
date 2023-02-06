<?php

namespace OEModule\BreakGlass;

use Yii;
use Patient;
use OEWebUser;
use User;

class BreakGlass
{
    private Patient $patient;
    private $patient_created_by;
    private $user;

    public function __construct(Patient $patient, OEWebUser $user)
    {
        $this->patient = $patient;
        $this->patient_created_by = User::model()->findByPk($patient->created_user_id);
        $this->user = User::model()->findByPk($user->getId());
    }

    private function resolveHealthboard(string $in): ?string
    {
        if (preg_match("/^(?:NHS Ayrshire & Arran|NHSAA|AA)\s*$/", $in)) {
            return "NHS Ayrshire & Arran";
        }

        if (preg_match("/^(?:NHS Borders|NHSBOR|BOR)\s*$/", $in)) {
            return "NHS Borders";
        }

        if (preg_match("/^(?:NHS Dumfries & Galloway|NHSDG|DG)\s*$/", $in)) {
            return "NHS Dumfries & Galloway";
        }

        if (preg_match("/^(?:NHS Fife|NHSFIFE|FIFE)\s*$/", $in)) {
            return "NHS Fife";
        }

        if (preg_match("/^(?:NHS Forth Valley|NHSFV|FV)\s*$/", $in)) {
            return "NHS Forth Valley";
        }

        if (preg_match("/^(?:NHS Grampian|NHSGrampian|NHSG|NHSGRAM|GRAM)\s*$/", $in)) {
            return "NHS Grampian";
        }

        if (preg_match("/^(?:NHS Greater Glasgow & Clyde|NHSGGC|GGC)\s*$/", $in)) {
            return "NHS Greater Glasgow & Clyde";
        }

        if (preg_match("/^(?:NHS Highland|NHSH)\s*$/", $in)) {
            return "NHS Highland";
        }

        if (preg_match("/^(?:NHS Lanarkshire|NHSLAN|LAN)\s*$/", $in)) {
            return "NHS Lanarkshire";
        }

        if (preg_match("/^(?:NHS Lothian|NHSLOTH|LOTH)\s*$/", $in)) {
            return "NHS Lothian";
        }

        if (preg_match("/^(?:NHS Orkney|NHSORK|ORK)\s*$/", $in)) {
            return "NHS Orkney";
        }

        if (preg_match("/^(?:NHS Shetland|NHSSHET|SHET)\s*$/", $in)) {
            return "NHS Shetland";
        }

        if (preg_match("/^(?:NHS Tayside|NHSTAY|TAY)\s*$/", $in)) {
            return "NHS Tayside";
        }

        if (preg_match("/^(?:NHS Western Isles|NHSWI|WI)\s*$/", $in)) {
            return "NHS Western Isles";
        }
        return null;
    }

    public function patientHealthboard(): ?string
    {
        $health_board = null;
        // Get the break_glass_patient_institution_field setting
        $break_glass_patient_institution_field = \SettingMetadata::model()->getSetting('break_glass_patient_institution_field');
        if ($break_glass_patient_institution_field === 'primary_institution') {
            $health_board = isset($this->patient->primary_institution->remote_id) ?
                $this->resolveHealthboard($this->patient->primary_institution->remote_id) :
                null;
        }
        if ($this->patient->contact->address) {
            if ($break_glass_patient_institution_field === 'county')  {
                $health_board = $this->resolveHealthboard($this->patient->contact->address->county);
            }
        }

        return $health_board;
    }

    public function userHealthboard(): ?string
    {
        if ($this->user) {
            return $this->resolveHealthboard($this->user->{Yii::app()->params['user_breakglass_field']});
        }
        return null;
    }

    public function breakGlassRequired(): bool
    {
        $id = $this->patient->id;
        $index = 'breakglass_break_'.$id;
        if (isset($_SESSION[$index]) && $_SESSION[$index]) {
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

    public function getPath(): string
    {
        return "/BreakGlass";
    }
}
