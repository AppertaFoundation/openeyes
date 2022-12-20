<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if ($read_check) {
    $link_label = 'View Unread';
    $check_var = 0;
    $viewing_label = 'Read Messages';
} else {
    $link_label = 'View Read';
    $check_var = 1;
    $viewing_label = 'Unread Messages';
}
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];

$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution->id, $selected_site_id);
$sortDirection = $dp->sort->getDirections();
$cols = array(
    array(
        'id' => 'hos_num',
        'header' => $dp->getSort()->link('hos_num', $primary_identifier_prompt, array('class' => 'sort-link')),
        'htmlOptions' => array('class' => 'nowrap'),
        'value' => function ($data) {
            $institution = Institution::model()->getCurrent();
            $selected_site_id = Yii::app()->session['selected_site_id'];
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $data->event->episode->patient->id, $institution->id, $selected_site_id);
            return PatientIdentifierHelper::getIdentifierValue($primary_identifier);
        },
        'type' => 'raw'
    ),
    array(
        'id' => 'gender',
        'header' => 'Sex',
        'value' => '$data->event->episode->patient->getGenderString()',
    ),
    array(
        'id' => 'age',
        'header' => 'Age',
        'value' => '$data->event->episode->patient->getAge() . "y"',
    ),
    array(
        'id' => 'patient_name',
        'class' => 'CLinkColumn',
        'header' => 'Patient',
        'urlExpression' => 'Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $data->event_id))',
        'labelExpression' => '$data->event->episode->patient->getHSCICName()',
        'htmlOptions' => array('class' => 'nowrap patient'),
    ),
    array(
        'name' => 'priority',
        'header' => '',
        'htmlOptions' => array('class' => 'urgent-status'),
        'value' => function ($data) {
            return $data->urgent ? '<i class="oe-i status-urgent small no-hover js-has-tooltip" data-tt-type="basic" data-tooltip-content="Urgent!"></i>' : '';
        },
        'type' => 'raw',
    ),
    array(
        'id' => 'message_type',
        'header' => $dp->getSort()->link('message_type', 'Type', array('class' => 'column-sort ' . (array_key_exists('message_type', $sortDirection)  ?
                'active ' . ($sortDirection['message_type'] ? 'descend' : 'ascend') : 'ascend'))),
        'htmlOptions' => array('class' => 'message-status nowrap'),
        'value' => function ($data) {
            if (isset($data->copyto_users)) {
                $copied_users = $data->copyto_users;
                foreach ($copied_users as $copied_user) {
                    if ($copied_user->user_id == Yii::app()->user->id) {
                        echo '<i class="oe-i duplicate small pad-right no-click"></i>';
                    }
                }
            }
            return $data->message_type->name === 'Query' ?
                (isset($data->last_comment) ?
                '<i class="oe-i status-query-reply small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query reply"></i>' :
                '<i class="oe-i status-query small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query"></i>') :
                '<span class="fade">' . substr(\OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findByPk($data->message_type_id)->name, 0, 3) . '.</span>';
        },
        'type' => 'raw',
    ),
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('event_date', 'Date', array('class' => 'column-sort ' . (array_key_exists('event_date', $sortDirection)  ? 'active ' . ($sortDirection['event_date'] ? 'descend' : 'ascend') : 'ascend'))),
        'value' => function ($data) {
            return '<span class="oe-date">' . Helper::convertDate2HTML(Helper::convertMySQL2NHS($data->created_date)) . '</span>';
        },
        'type' => 'raw'
    ),
    array(
        'id' => 'user',
        'header' => $dp->getSort()->link(
            'user',
            (strpos($message_type, 'sent') !== false) ? 'Recipient' : 'Sender',
            array('class' => 'column-sort ' . (array_key_exists('user', $sortDirection)  ? 'active ' . ($sortDirection['user'] ? 'descend' : 'ascend') : 'ascend'))
        ),
        'value' => (strpos($message_type, 'sent') !== false) ?
            '\User::model()->findByPk($data->for_the_attention_of_user_id)->getFullNameAndTitle()' :
            '\User::model()->findByPk($data->created_user_id)->getFullNameAndTitle()',
        'cssClassExpression' => '"nowrap sender"',
    ),
    array(
        'id' => 'message',
        'name' => 'Message',
        'cssClassExpression' => '"js-message"',
        'value' => function ($data) {
            $commentslist = '';
            if ($data->comments) {
                foreach ($data->comments as $comment) {
                    $commentslist .= '<br><i class="oe-i child-arrow small pad-right no-click"></i>' .
                        Yii::app()->format->Ntext(preg_replace("/[\r\n]+/", "\n", $comment->comment_text));
                }
            }
            return '<div class="js-preview-message message">' . Yii::app()->format->text(rtrim($data->message_text)) .
                (isset($data->last_comment) ? '<i class="oe-i child-arrow no-click small pad"></i>' . Yii::app()->format->text(rtrim($data->last_comment->comment_text)) : '')  . '</div>' .
                '<div class="js-expanded-message message expand">' . Yii::app()->format->Ntext(rtrim(preg_replace("/[\r\n]+/", "\n", $data->message_text))) . $commentslist . '</div>';
        },
        'type' => 'raw',
    ),
    array(
        'name' => 'expand',
        'header' => '',
        'value' => '\'<i class="oe-i small js-expand-message expand"></i>\'',
        'type' => 'raw',
        'cssClassExpression' => '"valign-top"',
    ),
    array(
        'header' => '',
        'class' => 'CButtonColumn',
        'template' => '{mark}',
        'buttons' => array(
            'mark' => array(
                'options' => array('title' => 'Mark as read'),
                'label' => '<i class="oe-i small save pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as Read"></i>',
                'visible' => function ($row, $data) {
                    if (isset($data->copyto_users)) {
                        $copied_users = $data->copyto_users;
                        foreach ($copied_users as $copied_user) {
                            if ($copied_user->user_id == Yii::app()->user->id && $copied_user->marked_as_read === '0') {
                                return true;
                            }
                        }
                    }
                    return ($data->marked_as_read === '0' && $data->for_the_attention_of_user_id === Yii::app()->user->id)
                        || (isset($data->last_comment) && $data->last_comment->marked_as_read === '0' &&
                            $data->last_comment->created_user_id != \Yii::app()->user->id);
                },
            ),
        ),
    ),
    array(
        'name' => 'message-view',
        'header' => '',
        'value' =>
            function ($data) {
                return '
            <a href="' . Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $data->event_id)) . '"><i class="oe-i direction-right-circle small pad"></i></a>';
            },
        'type' => 'raw'
    ),
);

$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets'), true) . '/';
$header_style = 'background: transparent url(' . $asset_path . 'img/small.png) left center no-repeat;';
?>
<div class="messages-all">
    <?php
    $this->widget('application.modules.OphCoMessaging.widgets.MessageGridView', array(
        'itemsCssClass' => 'standard messages highlight-rows',
        'dataProvider' => $dp,
        'htmlOptions' => array('id' => 'inbox-table'),
        'rowCssClassExpression' => '$data->getReadStyleClass()',
        'summaryText' => '',
        'emptyTagName' => 'div',
        'emptyCssClass' => 'alert-box info align-left',
        'emptyText' => 'No Messages',
        'columns' => $cols,
        'enableHistory' => true,
        'enablePagination' => false,
    ));
    ?>
</div>
