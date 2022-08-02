<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class CorrespondenceCreator extends \EventCreator
{
    /**
     * This is needed because $element->macro will be repopulated when setDefaultOptions() called
     * @var \LetterMacro
     */
    public $macro;

    /**
     * @var array of documents data
     */
    public $documents = [];

    /**
     * @var Int
     */
    public $letter_type_id;

    public function __construct($episode, $macro = null, $letter_type_id = null)
    {
        $event_type = \EventType::model()->find('name = "Correspondence"');
        parent::__construct($episode, $event_type->id);

        $this->macro = $macro;
        $this->letter_type_id = $letter_type_id;

        if ($macro) {
            $this->macro = $macro;
            $this->populateDocumentData();
        }

        $this->elements['ElementLetter'] = new \ElementLetter();
    }

    protected function populateDocumentData()
    {
        $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
        $macro_target_data = $api->getMacroTargets($this->episode->patient->id, $this->macro->id);

        if (isset($macro_target_data['cc'])) {
            foreach ($macro_target_data['cc'] as $cc) {
                $this->documents['DocumentTarget'][] = [
                    'attributes' => [
                        'ToCc' => 'Cc',
                        'contact_type' => $cc['contact_type'],
                        'contact_id' => isset($cc['contact_id']) ? $cc['contact_id'] : '',
                        'contact_name' => isset($cc['contact_name']) ? $cc['contact_name'] : '',
                        'address' => $cc['address']
                    ],

                    'DocumentOutput' => [
                        [
                            'output_type' => \DocumentOutput::TYPE_PRINT
                        ],
                    ],
                ];
            }
        }

        if (isset($macro_target_data['to'])) {
            $this->documents['DocumentTarget'][] =
                 [
                    'attributes' => [
                        'ToCc' => 'To',
                        'contact_type' => $macro_target_data['to']['contact_type'],
                        'contact_id' => isset($macro_target_data['to']['contact_id']) ? $macro_target_data['to']['contact_id'] : '',
                        'contact_name' => isset($macro_target_data['to']['contact_name']) ? $macro_target_data['to']['contact_name'] : '',
                        'address' => $macro_target_data['to']['address']
                    ],

                    'DocumentOutput' => [
                        [
                            //this gp_label param thing is extremely dodgy, we will have problem here I guess later
                            'output_type' => strtolower($macro_target_data['to']['contact_type']) == strtolower(\SettingMetadata::model()->getSetting('gp_label'))
                            ? \DocumentOutput::TYPE_DOCMAN
                            : \DocumentOutput::TYPE_PRINT,
                        ]
                    ],
                 ];
        }

        $this->documents['macro_id'] = $macro_target_data['macro_id'];
    }

    protected function saveElements($event_id)
    {
        $element = $this->elements['ElementLetter'];
        $element->event_id = $event_id;
        $element->date = date("Y-m-d");
        $element->letter_type_id = $this->letter_type_id;

        $esign = new \Element_OphCoCorrespondence_Esign();
        $esign->event_id = $event_id;

        //in $element->setDefaultOptions() there is a check for if action->id == create
        //at the moment it is fine but later we might need to extend
        $element->setDefaultOptions($this->episode->patient);

        if ($this->macro) {
            $element->macro = $this->macro;
            $element->populate_from_macro($this->episode->patient);
        }

        if (!$element->save()) {
            $this->addErrors($element->getErrors());
        }

        if (!$esign->save(false)) {
            $this->addErrors($esign->getErrors());
        }

        if ($this->documents) {
            $document = new Document();
            $document->event_id = $this->event->id;
            $document->is_draft = null;

            $document->createNewDocSet($this->documents);
        }

        return !$this->hasErrors();
    }
}
