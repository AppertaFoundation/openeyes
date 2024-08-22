<?php if (\Yii::app()->user->hasFlash('pgdpsd-not-found')) : ?>
    <div class='alert-box error'>
        <?= \Yii::app()->user->getFlash('pgdpsd-not-found'); ?>
    </div>
<?php endif; ?>
<?php if (!$pgdpsds) :?>
<div class="row divider">
    <div class="alert-box issue"><b>No results found</b></div>
</div>
<?php endif; ?>
<div class="row divider">
    <?php
        $form = $this->beginWidget(
            'BaseEventTypeCActiveForm',
            [
                'id' => 'searchform',
                'enableAjaxValidation' => false,
                'focus' => '#js-pgd-search',
                'action' => Yii::app()->createUrl($search_url),
                'method' => 'get'
            ]
        );
        ?>
    <input type="text"
        autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete') ?>"
        name="search" id="js-pgd-search" placeholder="Search PGD/PSDs..."
        value="<?php echo !empty($search) ? strip_tags($search) : ''; ?>"
    />
    <?php $this->endWidget() ?>
</div>
<form id="admin_PGDPSDs">
    <input
        type="hidden"
        name="YII_CSRF_TOKEN"
        value="<?php echo Yii::app()->request->csrfToken ?>"
    />
    <table class="standard">
        <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Type</th>
                <th>Name</th>
                <th>Description</th>
                <th>Institution</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pgdpsds as $pgdpsd) {?>
                <tr class="clickable js-clickable" data-id="<?php echo $pgdpsd->id ?>"
                    data-uri="OphDrPGDPSD/admin/editPGDPSD/<?php echo $pgdpsd->id ?>">
                    <td><input type="checkbox"
                                name="PGDPSDs[]"
                                value="<?php echo $pgdpsd->id ?>"/></td>
                    <td><?=$pgdpsd->type?></td>
                    <td><?=$pgdpsd->name ?></td>
                    <td><?=$pgdpsd->description ?></td>
                    <td><?=$pgdpsd->institution ? $pgdpsd->institution->name : '' ?></td>
                    <td><i class="oe-i <?=($pgdpsd->active ? 'tick' : 'remove');?> small"></i></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add PGD/PSD',
                        [
                            'class' => 'button large',
                            'data-uri' => $add_url,
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?=\CHtml::button(
                        'Deactivate PGD/PSDs',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'PGDPSDs',
                            'data-uri' => $delete_url,
                            'id' => 'et_delete'
                        ]
                    ); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', [ 'pages' => $pagination ]); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
