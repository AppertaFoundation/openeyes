<?php /**
* OpenEyes.
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

class SignImporter
{
    private $event;
    private $element;
    public $signature;
    private $sign_img_string;

    public function __construct($event, $element, $element_type, $sign_base64 = null)
    {
        $this->event = $event;
        $this->element = $element;
        $this->setSignBase64($sign_base64);
    }

    public function validate()
    {
        if (!$this->event || !$this->element) {
            \OELog::log("Event and Element must be set to import signature");
            throw new \Exception("Both event and element needs to be set");
        }

        return true;
    }

    public function setSignBase64($sign_base64)
    {
        if ($sign_base64) {
            $this->sign_img_string = base64_decode($sign_base64);
        }

        return true;
    }

    public function getFilePath()
    {
        return $this->signature->signatureFile->getFilePath();
    }

    public function save()
    {
        $protected_file = \ProtectedFile::createForWriting("sign_{$this->event->id}_".strtotime(date('Y-m-d H:i')).".png");
        file_put_contents($protected_file->getPath(), $this->sign_img_string);
        $protected_file->validate();
        $protected_file->title = "";
        $protected_file->description = "";
        $protected_file->validate();
        $protected_file->save(false);

        $signature = new OphCoCvi_Signature();
        $signature->element_id = $this->element->id;
        $signature->type = \BaseSignature::TYPE_PATIENT;
        $signature->signatory_role = 'Patient';
        $signature->signatory_name = $this->event->episode->patient->contact->first_name." ".$this->event->episode->patient->contact->last_name;
        $signature->timestamp = strtotime(date('Y-m-d H:i'));
        $signature->signature_file_id = $protected_file->id;
        $signature->save(false);
        $this->signature = $signature;
    }
}
