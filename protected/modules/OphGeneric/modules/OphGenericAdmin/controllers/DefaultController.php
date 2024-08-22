<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\modules\OphGenericAdmin\controllers;

use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\Comments;
use OEModule\OphGeneric\models\DeviceInformation;

class DefaultController extends \ModuleAdminController
{
    public $layout = 'application.views.layouts.admin';

    public const MANUAL_ELEMENT_CLASSES = [
        HFA::class,
        Comments::class,
        DeviceInformation::class
    ];

    public function actionListEventSubTypes()
    {
        $event_subtypes = \EventSubtype::model()->findAll();

        $this->render('list_event_subtypes', ['event_subtypes' => $event_subtypes]);
    }

    public function actionEditEventSubType($id)
    {
        $event_subtype = \EventSubtype::model()->findByPk($id);
        $errors = [];

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->updateEventSubType($event_subtype, \Yii::app()->request->getPost('EventSubtype'));

            if (empty($errors)) {
                $this->redirect('/OphGeneric/admin/Default/listEventSubTypes');
            }
        }

        $elements = \EventSubtypeElementEntry::model()->findAll('event_subtype = :subtype', [':subtype' => $id]);

        $this->render('edit_event_subtype', [
            'event_subtype' => $event_subtype,
            'elements' => $elements,
            'element_types' => $this->getManualElementTypes(),
            'errors' => $errors
        ]);
    }

    private function updateEventSubType($event_subtype, $data) {
        $icon = !empty($data['icon_id']) ? \EventIcon::model()->findByPk($data['icon_id']) : null;

        $event_subtype->icon_name = $icon->name ?? null;
        $event_subtype->manual_entry = $data['manual_entry'];
        $event_subtype->element_type_entries = $data['element_type_entries'] ?? [];

        return $event_subtype->save() ? [] : $event_subtype->getErrors();
    }

    private function getManualElementTypes(): array
    {
        $criteria = new \CDbCriteria();
        $criteria->addInCondition('class_name', self::MANUAL_ELEMENT_CLASSES);

        return \ElementType::model()->findAll($criteria);
    }
}
