<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2013
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">
    <table class="generic-admin standard sortable">
        <thead>
        <tr>
            <th>Content</th>
            <th>Site</th>
            <th>Subspecialty</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($instructions as $index => $instruction) : ?>
            <?php $this->renderPartial(
                '/admin/postOpInstructions/entry',
                [
                    'instruction' => $instruction,
                    'index' => $index,
                ]
            ); ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4">
                <?=\CHtml::button(
                    'Add',
                    [
                        'class' => 'button large',
                        'type' => 'button',
                        'name' => 'admin-add',
                        'data-uri' => '/OphCiExamination/admin/addInvoiceStatus',
                        'id' => 'add_new'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" class="loader" alt="loading..."
         style="display: none;"/>

    <script type="text/template" id="post-op-instructions-template" class="entry-template hidden">
        <?php $this->renderPartial('/admin/postOpInstructions/entry', array(
            'instruction' => new OphTrOperationnote_PostopInstruction,
            'index' => '{{index}}',
        )) ?>
    </script>

    <script>
        $(document).ready(function () {

            $('#add_new').on('click', function () {
                var $tr = $('table.generic-admin tbody tr');
                var output = Mustache.render($('#post-op-instructions-template').text(), {
                    "index": OpenEyes.Util.getNextDataKey($tr, 'row'),
                });

                $('table.generic-admin tbody').append(output);
            });

            $('table.generic-admin tbody').on('click', 'a.save', function () {
                var $tr = $(this).closest('tr');
                var model = '#OphTrOperationnote_PostopInstruction_' + $tr.data('row') + '_';
                var url = '/OphTrOperationnote/admin/postOpInstructions';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'id': $tr.find(model + 'id').val(),
                        'content': $tr.find(model + 'content').val(),
                        'site_id': $tr.find(model + 'site_id').val(),
                        'subspecialty_id': $tr.find(model + 'subspecialty_id').val(),
                        'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
                        'action': 'save'
                    },
                    beforeSend: function () {
                        $tr.find('td.actions .wrapper').hide();
                        $('.loader').first().clone().appendTo($tr.find('td.actions')).show();
                    },
                    success: function (data) {
                        var $actions = $tr.find('td.actions');
                        //$actions.find('.wrapper').show();
                        $tr.find('td.actions .loader').remove();
                        if (data.success === 1) {
                            $actions.append($('<span>', {'style': 'color:green;font-weight:bold'}).text('saved'));
                            $actions.find('a.delete').removeClass('hidden');
                            $actions.find('span').fadeOut(1000, function () {
                                $actions.find('.wrapper').show();
                                $actions.find('span').remove();
                            });
                        }
                    },
                    dataType: 'json'
                });
            });

            $('table.generic-admin tbody').on('click', 'a.delete', function () {
                var $tr = $(this).closest('tr');
                var model = '#OphTrOperationnote_PostopInstruction_' + $tr.data('row') + '_';
                var url = '/OphTrOperationnote/admin/postOpInstructions';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'id': $tr.find(model + 'id').val(),
                        'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
                        'action': 'delete'
                    },
                    beforeSend: function () {
                        $tr.find('td.actions .wrapper').hide();
                        $('.loader').first().clone().appendTo($tr.find('td.actions')).show();
                    },
                    success: function (data) {
                        var $actions = $tr.find('td.actions');
                        $tr.find('td.actions .loader').remove();
                        if (data.success === 1) {
                            $actions.append($('<span>', {'style': 'color:red;font-weight:bold'}).text('deleted'));

                            $tr.fadeOut(1000, function () {
                                $tr.remove();
                            });
                        }
                    },
                    dataType: 'json'
                });
            });


        });
    </script>
</div>