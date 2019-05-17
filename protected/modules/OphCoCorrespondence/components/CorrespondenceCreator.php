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
        $this->macro = $macro;
        $this->letter_type_id = $letter_type_id;
        $event_type = \EventType::model()->find('name = "Correspondence"');

        if ($macro) {
            $this->macro = $macro;
            $this->populateDocumentData();
        }

        parent::__construct($episode, $event_type->id);

        $this->elements['ElementLetter'] = new \ElementLetter();
    }

    protected function populateDocumentData()
    {
        $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
        $macro_target_data = $api->getMacroTargets($this->episode, $this->macro->id);

\OELog::log('<pre>' . print_r($macro_target_data, true) . '</pre>');

        $this->documents['DocumentTarget'][] = [
             [ //foreach DocumentTargets as DocumentTarge
                'attributes' => [
                    'ToCc' => '',
                    'contact_type' => '',
                    'contact_id' => '', //isset
                    'contact_name' => '', //isset
                    'address' => ''
                ],

                'DocumentOutput' => [
                    'attributes' => [
                        'output_type' => '',
                        ''
                    ],
                ],
            ],
            'macro_id' => $this->macro->id
        ];
    }

    protected function saveElements($event_id)
    {
        $element = $this->elements['ElementLetter'];
        $element->event_id = $event_id;
        $element->date = date("Y-m-d");
        $element->letter_type_id = date("Y-m-d");

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

        if ($this->documents) {
            $document = new Document();
            $document->event_id = $this->event->id;
            $document->is_draft = null;

            $document->createNewDocSet($this->documents);
        }

        return !$this->hasErrors();
    }
}
