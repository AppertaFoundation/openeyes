<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
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
?>
<?php
$cols = array(
    array(
        'id' => 'hos_num',
        'header' => $dp->getSort()->link('hos_num', 'No.', array('class' => 'sort-link')),
        'value' => '$data->event->episode->patient->hos_num',
    ),
    array(
        'id' => 'gender',
        'header' => $dp->getSort()->link('gender', 'Gender', array('class' => 'sort-link')),
        'value' => '$data->event->episode->patient->getGenderString()',
    ),
    array(
        'id' => 'age',
        'header' => $dp->getSort()->link('age', 'Age', array('class' => 'sort-link')),
        'value' => '$data->event->episode->patient->getAge() . "y"',
    ),
    array(
        'id' => 'patient_name',
        'class' => 'CLinkColumn',
        'header' => $dp->getSort()->link('patient_name', 'Patient', array('class' => 'sort-link')),
        'urlExpression' => 'Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $data->event_id))',
        'labelExpression' => '$data->event->episode->patient->getHSCICName()',
        'cssClassExpression' => '"nowrap patient"',
    ),
    array(
        'name' => 'priority',
        'header' => '',
        'value' => function ($data) {
            return $data->urgent ? '
            <svg class="urgent-message" viewBox="0 0 8 8" height="8" width="8">
              <circle cx="4" cy="4" r="4"></circle>
            </svg>' : '';
        },
        'type' => 'raw',
    ),
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('event_date', 'Date', array('class' => 'sort-link')),
        'value' => 'Helper::convertMySQL2NHS($data->created_date)',
    ),
    array(
        'id' => 'user',
        'header' => $dp->getSort()->link('user', $message_type === 'sent' ? 'To' : 'From', array('class' => 'sort-link')),
        'value' => $message_type === 'sent' ?
            '\User::model()->findByPk($data->for_the_attention_of_user_id)->getFullNameAndTitle()' :
            '\User::model()->findByPk($data->created_user_id)->getFullNameAndTitle()',
        'cssClassExpression' => '"nowrap sender"',
    ),
    array(
        'name' => 'Message',
        'value' => function ($data) {
            return '<div class="message">' . Yii::app()->format->Ntext($data->message_text) . '</div>';
        },
        'type' => 'raw',
    ),
    array(
        'name' => 'expand',
        'header' => '',
        'value' => '\'<i class="oe-i small js-expand-message expand"></i>\'',
        'type' => 'raw',
    ),
    array(
        'name' => 'reply',
        'header' => '',
        'value' => '\'<a href="#">reply</a>\'',
        'type' => 'raw',
    ),
);

if (!$read_check) {
    $cols[] = array(
        'header' => 'Actions',
        'class' => 'CButtonColumn',
        'template' => '{mark}{reply}',
        'buttons' => array(
            'mark' => array(
                'options' => array('title' => 'Mark as read'),
                'url' => 'Yii::app()->createURL("/OphCoMessaging/Default/markRead/", array(
                        "id" => $data->event->id,
                        "returnUrl" => \Yii::app()->request->requestUri))',
                'label' => '<button class="warning small">dismiss</button>',
                'visible' => function ($row, $data) {
                    return !$data->message_type->reply_required
                        || $data->comments
                        || (\Yii::app()->user->id === $data->created_user_id);
                },

            ),
            'reply' => array(
                'options' => array('title' => 'Add a comment'),
                'url' => 'Yii::app()->createURL("/OphCoMessaging/Default/view/", array(
                                        "id" => $data->event->id,
                                        "comment" => 1))',
                'label' => '<button class="secondary small">Reply</button>',
                'visible' => function ($row, $data) {
                    return $data->message_type->reply_required
                        && !$data->comments
                        && (\Yii::app()->user->id !== $data->created_user_id);
                },
            ),
        ),
    );
}

$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets')) . '/';
$header_style = 'background: transparent url(' . $asset_path . 'img/small.png) left center no-repeat;';

$this->widget('application.modules.OphCoMessaging.widgets.MessageGridView', array(
    'itemsCssClass' => 'messages',
    'dataProvider' => $dp,
    'htmlOptions' => array('id' => 'inbox-table'),
    'rowCssClassExpression' => '$data->marked_as_read ? "read" : "unread"',
    'summaryText' => '',
    'columns' => $cols,
    'message_type' => $message_type,
    'enableHistory' => true,
));
?>
