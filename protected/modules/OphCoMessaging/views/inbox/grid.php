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
$user = Yii::app()->session['user'];
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#grid_header_form .datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
    });
</script>
<div class="home-messages flex-layout flex-top">
  <div class="message-actions">
    <div class="user"><?= $user->first_name . ' ' . $user->last_name; ?></div>
    <button class="green hint cols-full send-message">Send New Message</button>
    <ul class="filter-messages">
      <li>
        <?php echo CHtml::link('Inbox', '#', array('class' => 'selected')); ?>
      </li>
      <li>
          <?php echo CHtml::link('Sent', '#'); ?>
      </li>
    </ul>
    <div class="search-messages">
      <form>
        <h3>Filter by Date</h3>
        <div class="flex-layout">
          <input type="text" id="OphCoMessaging_from" name="OphCoMessaging_from" placeholder="from" class="cols-5" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_from', '')?>" />
          <input type="text" id="OphCoMessaging_to" name="OphCoMessaging_to" placeholder="to" class="cols-5" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_to', '')?>" />
            </div>
      </form>
            </div>
        </div>
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
        'htmlOptions' => array('class' => 'message'),
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

    $items_per_page = $dp->getPagination()->getPageSize();
    $page_num = $dp->getPagination()->getCurrentPage();
    $from = ($page_num * $items_per_page) + 1;
    $to = min(($page_num + 1) * $items_per_page, $dp->totalItemCount);

?>

  <table class="messages">
    <colgroup>
      <col style="width: 80px;">
      <col style="width: 70px;">
      <col style="width: 50px;">
      <col>
      <col style="width: 20px;">
      <col style="width: 90px;">
    </colgroup>
    <thead>
    <tr>
      <th>No.</th>
      <th>Gender</th>
      <th>Age</th>
      <th>Patient</th>
      <th>Messages</th>
      <th></th>
      <th colspan="3">
        <?php echo $from . ' - ' . $to . ' of ' . $dp->totalItemCount; ?>
        <i class="oe-i arrow-left-bold medium pad"></i>
        <i class="oe-i arrow-right-bold medium pad"></i>
      </th>
    </tr>
    </thead>
    <tbody>
      <?php foreach ($dp->getData() as $message): ?>
        <tr class="read">
          <td>
            <?php echo CHtml::encode($message->event->episode->patient->hos_num); ?>
          </td>
          <td>
              <?php echo CHtml::encode($message->event->episode->patient->gender); ?>
          </td>
          <td>
              <?php echo CHtml::encode(Helper::getAge($message->event->episode->patient->dob)); ?>
          </td>
          <td class="nowrap patient">
            <?php echo CHtml::encode($data->event->episode->patient->getHSCICName()); ?>
          </td>
          <td>
            <?php if ($data->urgent): ?>
              <svg class="urgent-message" viewBox="0 0 8 8" height="8" width="8">
                <circle cx="4" cy="4" r="4"></circle>
              </svg>
            <?php endif; ?>
          </td>
          <td>
            <?php echo CHtml::encode(Helper::convertMySQL2NHS($data->created_date)); ?>
          </td>
          <td class="nowrap sender">
              <?php echo CHtml::encode(\User::model()->findByPk($data->created_user_id)->getFullNameAndTitle()); ?>
          </td>
          <td>
            <div class="message">
              <?php echo CHtml::encode(strlen($data->message_text) > 50 ? \Yii::app()->format->Ntext(substr($data->message_text, 0, 50).' ...') : \Yii::app()->format->Ntext($data->message_text)); ?>
            </div>
          </td>
          <td>
            <i class="oe-i expand small js-expand-message"></i>
          </td>
          <td>
            <a href="#">reply</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
