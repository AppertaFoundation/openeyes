<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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
?>

<table class="label-value">
    <colgroup>
        <col class="cols-5">
    </colgroup>
    <tbody>
        <tr>
            <td>
                <div class="data-label">From</div>
            </td>
            <td>
                <?php
                echo $element->user->getFullnameAndTitle();
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('for_the_attention_of_user_id')) ?></div>
            </td>
            <td>
                <div class="data-value "><?php echo $element->for_the_attention_of->formattedName(); ?></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="data-label">Date sent</div>
            </td>
            <td>
                <?php echo Helper::convertDate2NHS($element->event->event_date); ?>
            </td>
        </tr>
        <tr>
            <td>
                <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('message_type_id')) ?></div>
            </td>
            <td>
                <div class="data-value"><?php echo $element->message_type ? $element->message_type->name : 'None' ?></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="data-label">Urgent</div>
            </td>
            <td>
                <div><?php if ($element->urgent) { ?>
                        <span class="highlighter">Yes</span>
                        <?php } else {
                            ?> <span>No</span>
                        <?php }
                        ?>
                </div>
            </td>
        </tr>
    </tbody>

</table>
<table>
    <tbody>
        <tr>
            <th>
                <p><?= Yii::app()->format->Ntext($element->message_text) ?></p>
            </th>
        </tr>
    </tbody>
</table>


<?= $element->comments ? '<hr />' : '' ?>
<?php foreach ($element->comments as $comment) { ?>
    <p>
        <em><?= Yii::app()->format->Ntext($comment->comment_text) ?></em>
    </p>
    <table class="label-value">
        <tbody>
            <tr>
                <td>
                    <div class="data-label">Reply date</div>
                </td>
                <td>
                    <div class="data-value"><?php echo Helper::convertMySQL2NHS($comment->created_date) ?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-label">From</div>
                </td>
                <td>
                    <div class="data-value"><?php echo $comment->user->getFullnameAndTitle(); ?></div>
                </td>
            </tr>           
        </tbody>
    </table>
<?php } ?>
