<?php

/**
 * (C) Copyright Apperta Foundation 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DocumentController extends \BaseApiController
{
    public function accessRules()
    {
        return [
            [
                'allow',
                'actions' => ['search'],
                'verbs' => ['GET'],
                'users' => ['@'],
            ],
            [
                'allow',
                'actions' => ['create'],
                'verbs' => ['POST'],
                'users' => ['@'],
            ],
            [
                'allow',
                'actions' => ['update'],
                'verbs' => ['PUT','PATCH'],
                'users' => ['@'],
            ],
            [
                'allow',
                'actions' => ['delete'],
                'verbs' => ['DELETE'],
                'users' => ['@'],
            ],
            [
                'deny',
                'users' => ['*'],
            ],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'BasicAuthBehavior' => ['class' => 'application.modules.Api.behaviors.BasicAuthBehavior'],
        ]);
    }

    private function findPatient($patient_identifier, $patient_id)
    {
        // Check if patient identifier exists
        $patient_identifier_type = \PatientIdentifierType::model()->find("unique_row_string=?", array($patient_identifier));
        if (!$patient_identifier_type) {
            $this->renderJSON(['error' => 'Invalid patient identifier type: ' . $patient_identifier], 400);
            \Yii::app()->end();
        }

        // Check if patient exists
        $pid = \PatientIdentifier::model()->find("value=? AND patient_identifier_type_id=?", array($patient_id, $patient_identifier_type->id)) ? \PatientIdentifier::model()->find("value=? AND patient_identifier_type_id=?", array($patient_id, $patient_identifier_type->id))->patient_id : null;
        if (!$pid) {
            $this->renderJSON(['error' => 'Invalid patient identifier: ' . $patient_id], 400);
            \Yii::app()->end();
        }

        return $pid;
    }

    private function deleteDocumentEvent($event, $soft_delete = false, $element = null)
    {
        if (!$soft_delete) {
            $element->delete();
            \Audit::model()->deleteAll('event_id = :event_id', array(':event_id' => $event->id));
            $event->delete();
        } else {
            $event->softDelete();
        }
    }

    private function createProtectedFile($document_data, $document_title)
    {
        $protected_file = new \ProtectedFile();
        $protected_file = $protected_file->createForWriting($document_title);
        if (file_put_contents($protected_file->getPath(), $document_data)) {
            $protected_file->save();
        } else {
            $this->renderJSON(['error' => 'Failed to save file'], 500);
            \Yii::app()->end();
        }
        return $protected_file;
    }

    private function checkDateFormat($date)
    {
        // Ensures the date is in the format yyyymmdd
        if (preg_match('/^\d{8}$/', $date) && checkdate(substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4))) {
            return true;
        } else {
            $this->renderJSON(['error' => 'Date format is incorrect (expected yyyymmdd): ' . $date], 400);
            \Yii::app()->end();
        }
    }

    public function actionSearch()
    {
        $query = \Yii::app()->db->createCommand()
            ->select('ep.id AS episode_id, e.id AS event_id, eod.id AS element_id, eod.unique_ref AS unique_ref, eod.event_sub_type AS event_sub_type, ep.firm_id AS firm_id,
            eod.left_document_id AS left_document_id, eod.right_document_id AS right_document_id, eod.single_document_id AS single_document_id,
            eod.left_comment AS left_comment, eod.right_comment AS right_comment, eod.single_comment AS single_comment, e.event_date AS document_date')
            ->from('episode ep')
            ->join('event e', 'ep.id = e.episode_id')
            ->join('et_ophcodocument_document eod', 'e.id = eod.event_id')
            ->where('e.deleted = 0');

        $unique_ref = \Yii::app()->request->getParam('unique_ref');
        // If unique_ref is provided then ignore all other parameters
        if ($unique_ref) {
            $query->andWhere('eod.unique_ref = :uid', array(':uid' => $unique_ref));
        } else {
            // Required variables
            $patient_identifier = \Yii::app()->request->getParam('patient_identifier_type');
            $patient_id = \Yii::app()->request->getParam('patient_id');
            $document_title = \Yii::app()->request->getParam('document_title');

            if(!$patient_identifier || !$patient_id || !$document_title) {
                $this->renderJSON(['error' => 'Missing required parameters:' . ($patient_identifier ? '' : ' patient_identifier_type ') . ($patient_id ? '' : ' patient_id ') . ($document_title ? '' : ' document_title ')], 400);
                \Yii::app()->end();
            }

            // Optional variables
            $document_subtype_name = \Yii::app()->request->getParam('document_subtype');
            $firm_id = \Yii::app()->request->getParam('firm_id');
            $laterality = \Yii::app()->request->getParam('laterality');
            $document_date = \Yii::app()->request->getParam('document_date');

            $pid = $this->findPatient($patient_identifier, $patient_id);

            $query->andWhere('ep.patient_id = :pid', array(':pid' => $pid));

            if ($firm_id) {
                $query->join('firm f', 'ep.firm_id = f.id')
                    ->andWhere('f.id = :firm_id', array(':firm_id' => $firm_id));
            }

            if ($document_subtype_name) {
                $query->join('ophcodocument_sub_types ost', 'eod.event_sub_type = ost.id')
                    ->andWhere('ost.name = :document_subtype_name', array(':document_subtype_name' => $document_subtype_name));
            }

            if ($laterality == 'L') {
                $query->join('protected_file pf', 'eod.left_document_id = pf.id')
                    ->andWhere('pf.name = :document_title', array(':document_title' => $document_title));
            } elseif ($laterality == 'R') {
                $query->join('protected_file pf', 'eod.right_document_id = pf.id')
                    ->andWhere('pf.name = :document_title', array(':document_title' => $document_title));
            } elseif ($laterality == 'N') {
                $query->join('protected_file pf', 'eod.single_document_id = pf.id')
                    ->andWhere('pf.name = :document_title', array(':document_title' => $document_title));
            } else {
                $query->leftJoin('protected_file spf', 'eod.single_document_id = spf.id')
                    ->leftJoin('protected_file lpf', 'eod.left_document_id = lpf.id')
                    ->leftJoin('protected_file rpf', 'eod.right_document_id = rpf.id')
                    ->andWhere('(spf.name = :sdocument_title OR lpf.name = :ldocument_title OR rpf.name = :rdocument_title)', array(':sdocument_title' => $document_title, ':ldocument_title' => $document_title, ':rdocument_title' => $document_title));
            }

            if ($document_date && $this->checkDateFormat($document_date)) {
                $query->andWhere('e.event_date LIKE ":document_date%"', array(':document_date' => date('Y-m-d', strtotime($document_date))));
            }
        }
        $results = $query->queryAll();

        $this->renderJSON($results, 200);
    }

    public function actionCreate()
    {
        // Gather request parameters
        $patient_identifier = \Yii::app()->request->getParam('patient_identifier_type');
        $patient_id = \Yii::app()->request->getParam('patient_id');
        $document_title = \Yii::app()->request->getParam('document_title', '');
        $document_subtype_name = \Yii::app()->request->getParam('document_subtype', 'General');
        $comments = \Yii::app()->request->getParam('comments', '');
        $firm_id = \Yii::app()->request->getParam('firm_id');
        $unique_ref = \Yii::app()->request->getParam('unique_ref');
        $document_date = \Yii::app()->request->getParam('document_date');

        // Check required parameters
        if (!$patient_identifier || !$patient_id || !$firm_id) {
            $this->renderJSON(['error' => 'Missing required parameters:' . ($patient_identifier ? '' : ' patient_identifier_type ') . ($patient_id ? '' : ' patient_id ') . ($firm_id ? '' : ' firm_id ')], 400);
            \Yii::app()->end();
        }

        // Eye laterality
        // None - N (default)
        // Left - L
        // Right - R
        $laterality = \Yii::app()->request->getParam('laterality', 'N');

        // Decode document if needed
        if (!($document_data = base64_decode(\Yii::app()->request->getRawBody(), true))) {
            $document_data = \Yii::app()->request->getRawBody();
        }

        $pid = $this->findPatient($patient_identifier, $patient_id);

        // Other variables
        $institution_id = preg_match('/-(\d+)-/', $patient_identifier, $matches) ? $matches[1] : null;
        $firm = \Firm::model()->findByPk($firm_id);
        if (!$firm) {
            $this->renderJSON(['error' => 'Invalid firm_id: ' . $firm_id], 400);
            \Yii::app()->end();
        }
        $document_subtype = \OphCoDocument_Sub_Types::model()->find("name=?", array($document_subtype_name));
        if (!$document_subtype) {
            $this->renderJSON(['error' => 'Invalid document subtype: ' . $document_subtype_name], 400);
            \Yii::app()->end();
        }

        // Find or create episode
        $episode = \Episode::getCurrentEpisodeByFirm($pid, $firm);
        if (!$episode) {
            $episode = new \Episode();
            $episode->patient_id = $pid;
            $episode->firm_id = $firm->id;
            $episode->save(false);
        }

        // Create event
        $event_type = \EventType::model()->find("name=?", array('Document'));

        $event = new \Event();
        $event->event_type_id = $event_type->id;
        $event->episode_id = $episode->id;
        $event->event_date = ($document_date ? ($this->checkDateFormat($document_date) ? date('Y-m-d', strtotime($document_date)) : null) : date('Y-m-d')) . ' 00:00:00';
        $event->institution_id = $institution_id;
        $event->save(false);

        // Creating and saving file
        $protected_file = $this->createProtectedFile($document_data, $document_title);

        // Creating Element
        $element = new \Element_OphCoDocument_Document();
        $element->event_id = $event->id;
        $element->event_sub_type = $document_subtype->id;
        $element->unique_ref = $unique_ref;

        switch ($laterality) {
            case 'L':
                $element->left_document_id = $protected_file->id;
                $element->left_comment = $comments;
                break;
            case 'R':
                $element->right_document_id = $protected_file->id;
                $element->right_comment = $comments;
                break;
            default:
                $element->single_document_id = $protected_file->id;
                $element->single_comment = $comments;
                break;
        }

        $element->save();

        $this->renderJSON(['success' => 'Document created'], 201);
        \Yii::app()->end();
    }

    public function actionUpdate()
    {
        // Gather request parameters
        $element_id = \Yii::app()->request->getParam('element_id');
        $document_title = \Yii::app()->request->getParam('document_title');
        $document_subtype_name = \Yii::app()->request->getParam('document_subtype');
        $comments = \Yii::app()->request->getParam('comments');
        $laterality = \Yii::app()->request->getParam('laterality');
        $unique_ref = \Yii::app()->request->getParam('unique_ref');

        // Check if element_id is provided
        if (!$element_id) {
            $this->renderJSON(['error' => 'Missing required parameter: element_id'], 400);
            \Yii::app()->end();
        }

        // Check if element exists
        $element = \Element_OphCoDocument_Document::model()->findByPk($element_id);
        if (!$element) {
            $this->renderJSON(['error' => 'Element with id ' . $element_id . ' not found'], 404);
            \Yii::app()->end();
        }

        // Check if document subtype exists
        if ($document_subtype_name) {
            $document_subtype = \OphCoDocument_Sub_Types::model()->find("name=?", array($document_subtype_name));
            if (!$document_subtype) {
                $this->renderJSON(['error' => 'Invalid document subtype: ' . $document_subtype_name], 400);
                \Yii::app()->end();
            }
        }

        // Decode document if needed
        if (!($document_data = base64_decode(\Yii::app()->request->getRawBody(), true))) {
            $document_data = \Yii::app()->request->getRawBody();
        }

        if (\Yii::app()->request->getRequestType() === 'PUT') {
            if (!$document_data) {
                $this->renderJSON(['error' => 'Document data is required for this request'], 400);
                \Yii::app()->end();
            }
            $document_title = $document_title ?? '';
            $document_subtype_name = $document_subtype_name ?? 'General';
            $comments = $comments ?? '';
            $laterality = $laterality ?? 'N';
        }

        if (\Yii::app()->request->getRequestType() === 'PUT') {
            // Blank all fields
            $element->left_document_id = null;
            $element->left_comment = null;
            $element->right_document_id = null;
            $element->right_comment = null;
            $element->single_document_id = null;
            $element->single_comment = null;
        }

        // Save as new protected file if document data is present
        $protected_file = null;
        if ($document_data) {
            if (!$document_title) {
                $this->renderJSON(['error' => 'Document title is required when updating document data'], 400);
                \Yii::app()->end();
            }
            $protected_file = $this->createProtectedFile($document_data, $document_title);
        }

        switch ($laterality) {
            case 'L':
                $element->left_document_id = $protected_file ? $protected_file->id : $element->left_document_id;
                $element->left_comment = $comments ?? $element->left_comment;

                if (!$element->left_document_id) {
                    $this->renderJSON(['error' => 'Left document not found and none provided'], 400);
                    \Yii::app()->end();
                }
                break;
            case 'R':
                $element->right_document_id = $protected_file ? $protected_file->id : $element->right_document_id;
                $element->right_comment = $comments ?? $element->right_comment;

                if (!$element->right_document_id) {
                    $this->renderJSON(['error' => 'Right document not found and none provided'], 400);
                    \Yii::app()->end();
                }
                break;
            case 'N':
            case null:
                $element->single_document_id = $protected_file ? $protected_file->id : $element->single_document_id;
                $element->single_comment = $comments ?? $element->single_comment;

                if (!$element->single_document_id) {
                    $this->renderJSON(['error' => 'Document not found and none provided'], 400);
                    \Yii::app()->end();
                }
                break;
            default:
                $this->renderJSON(['error' => 'Invalid laterality: ' . $laterality], 400);
                break;
        }

        $element->event_sub_type = $document_subtype->id ?? $element->event_sub_type;
        $element->unique_ref = $unique_ref ?? $element->unique_ref;

        $element->save();

        $this->renderJSON(['success' => 'Document updated'], 200);
        \Yii::app()->end();
    }

    public function actionDelete()
    {
        // Gather request parameters
        $element_id = \Yii::app()->request->getParam('element_id');
        $laterality = \Yii::app()->request->getParam('laterality');
        $soft_delete = \Yii::app()->request->getParam('soft_delete', false);

        // Check if element exists
        $element = \Element_OphCoDocument_Document::model()->findByPk($element_id);
        if (!$element) {
            $this->renderJSON(['error' => 'Element with id ' . $element_id . ' not found'], 404);
            \Yii::app()->end();
        }

        switch ($laterality) {
            case 'L':
                $element->left_document_id = null;
                $element->left_comment = null;
                break;
            case 'R':
                $element->right_document_id = null;
                $element->right_comment = null;
                break;
            case 'N':
                $element->single_document_id = null;
                $element->single_comment = null;
                break;
            case null:
                // Delete event if no laterality is provided
                $event = \Event::model()->findByPk($element->event_id);
                $this->deleteDocumentEvent($event, $soft_delete, $element);
                break;
            default:
                $this->renderJSON(['error' => 'Invalid laterality: ' . $laterality], 400);
                break;
        }

        // Check if any documents or comments are left
        if (!$element->left_document_id && !$element->right_document_id && !$element->single_document_id && !$element->left_comment && !$element->right_comment && !$element->single_comment) {
            $event = \Event::model()->findByPk($element->event_id);
            $this->deleteDocumentEvent($event, $soft_delete, $element);
        } else {
            $element->save();
        }

        $this->renderJSON(['success' => 'Document deleted'], 202);
        \Yii::app()->end();
    }
}
