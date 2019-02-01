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
        'htmlOptions' => array('class' => 'nowrap patient'),
    ),
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => '<a href="#" class="sortable">Messages <i class="oe-i arrow-down-bold small pad"></i></a>',
        'value' => function ($data) {
            return '<span class="oe-date">'. Helper::convertDate2HTML(Helper::convertMySQL2NHS($data->created_date)).'</span>';
        },
        'type' => 'raw'
    ),
    array(
        'name' => 'priority_and_type',
        'header' => '',
        'htmlOptions'=>array('class' => 'nowrap'),
        'value' => function ($data) {
        		$urgent_icon = $data->urgent ? '
            <i class="js-has-tooltip" data-tooltip-content="Urgent"><svg class="urgent-message" viewBox="0 0 8 8" height="8" width="8"><circle cx="4" cy="4" r="4"/></svg></i>' : '';
        		$query_icon = $data->message_type_id === '2' ? '
						<i class="js-has-tooltip" data-tooltip-content="Reply requested"><svg class="reply-message" viewBox="0 0 8 8" height="8" width="8"><circle cx="4" cy="4" r="4"/></svg></i>' : '';
            return $urgent_icon . $query_icon;
        },
        'type' => 'raw',
    ),
    array(
        'id' => 'user',
        'header' => '',
        'value' => $message_type === 'sent' ?
            '\User::model()->findByPk($data->for_the_attention_of_user_id)->getFullNameAndTitle()' :
            '\User::model()->findByPk($data->created_user_id)->getFullNameAndTitle()',
        'cssClassExpression' => '"nowrap sender"',
    ),
    array(
        'id' => 'message',
        'name' => 'Message',
        'cssClassExpression' => '"js-message"',
        'value' => function ($data) {
            return '<div class="js-preview-message message">' . Yii::app()->format->text(rtrim($data->message_text)) . '</div>' .
							'<div class="js-expanded-message message expand">' . Yii::app()->format->Ntext(rtrim($data->message_text)) . '</div>';
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
        'header' => '',
        'class' => 'CButtonColumn',
        'template' => '{mark}',
        'buttons' => array(
            'mark' => array(
                'options' => array('title' => 'Mark as read'),
                'label' => '<i class="oe-i small tick pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as Read"></i>',
                'visible' => function ($row, $data) {
                    return $data->marked_as_read === '0'
												&& ($data->message_type_id !== '2'
                        || $data->comments
                        || (\Yii::app()->user->id === $data->created_user_id));
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
            <a href="'.Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $data->event_id)).'"><i class="oe-i direction-right-circle small pad"></i></a>';
        },
        'type' => 'raw'
    ),
);

$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets')) . '/';
$header_style = 'background: transparent url(' . $asset_path . 'img/small.png) left center no-repeat;';
?>
<div class="messages-all">
<?php
$this->widget('application.modules.OphCoMessaging.widgets.MessageGridView', array(
    'itemsCssClass' => 'standard messages highlight-rows',
    'dataProvider' => $dp,
    'htmlOptions' => array('id' => 'inbox-table'),
    'rowCssClassExpression' => '$data->marked_as_read ? "read" : "unread"',
    'summaryText' => '',
    'columns' => $cols,
    'enableHistory' => true,
    'enablePagination' => false,
));
?>
</div>
