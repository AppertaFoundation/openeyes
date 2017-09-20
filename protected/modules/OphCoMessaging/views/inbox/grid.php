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
<script type="text/javascript">
    $(document).ready(function() {
        $('#grid_header_form .datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
    });
</script>
<form id="grid_header_form">
    <div class="row">
        <div class="messages-filter">
            <div class="large-3 column">
            <button type="submit" class="small secondary" name="OphCoMessaging_read" value="<?=$check_var?>"><?=$link_label?></button>
            </div>
            <div class="large-9 column text-right">
                <label for="OphCoMessaging_from">From:</label><input type="text" id="OphCoMessaging_from" name="OphCoMessaging_from" class="datepicker" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_from', '')?>" />
                <label for="OphCoMessaging_to">To:</label><input type="text" id="OphCoMessaging_to" name="OphCoMessaging_to" class="datepicker" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_to', '')?>" />
                <button type="submit" class="small secondary" name="OphCoMessaging_read" value="<?=intval(!$check_var)?>">Search</button>
            </div>
        </div>
    </div>
</form>

<?php
$cols = array(
    array(
        'name' => 'priority',
        'value' => function ($data) {
            $img_url = Yii::app()->assetManager->createUrl('img/alert.png');
            return $data->urgent ? '<img src="'.$img_url.'" />' : '';
        },
        'type' => 'raw',
        'htmlOptions' => array(
            'class' => 'text-center',
        ),
    ),
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('event_date', 'Date', array('class' => 'sort-link')),
        'value' => 'Helper::convertMySQL2NHS($data->created_date)',
        'htmlOptions' => array('class' => 'date'),
    ),
    array(
        'id' => 'hos_num',
        'header' => $dp->getSort()->link('hos_num', 'Hospital No.', array('class' => 'sort-link')),
        'value' => '$data->event->episode->patient->hos_num',
    ),
    array(
        'id' => 'patient_name',
        'class' => 'CLinkColumn',
        'header' => $dp->getSort()->link('patient_name', 'Name', array('class' => 'sort-link')),
        'urlExpression' => 'Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $data->event_id))',
        'labelExpression' => '$data->event->episode->patient->getHSCICName()',
    ),
    array(
        'id' => 'dob',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('dob', 'DOB', array('class' => 'sort-link')),
        'value' => 'Helper::convertMySQL2NHS($data->event->episode->patient->dob)',
        'htmlOptions' => array('class' => 'date'),
    ),
    array(
        'id' => 'user',
        'header' => $dp->getSort()->link('user', 'From', array('class' => 'sort-link')),
        'value' => '\User::model()->findByPk($data->created_user_id)->getFullNameAndTitle()',
    ),
    array(
        'name' => 'Message',
        'value' => function ($data) {
            return strlen($data->message_text) > 50 ? \Yii::app()->format->Ntext(substr($data->message_text, 0, 50).' ...') : \Yii::app()->format->Ntext($data->message_text);
        },
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

$this->widget('zii.widgets.grid.CGridView', array(
    'itemsCssClass' => 'grid',
    'dataProvider' => $dp,
    'htmlOptions' => array('id' => 'inbox-table'),
    'summaryText' => '<h3 style="' . $header_style .'">'.$viewing_label.'<small> {start}-{end} of {count} </small></h3>',
    'columns' => $cols,
));
?>
