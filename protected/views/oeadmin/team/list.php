<?php if (\Yii::app()->user->hasFlash('team-not-found')) : ?>
    <div class='alert-box error'>
        <?= \Yii::app()->user->getFlash('team-not-found'); ?>
    </div>
<?php endif; ?>
<?php if (!$teams) :?>
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
                'focus' => '#js-team-search',
                'action' => Yii::app()->createUrl($search_uri),
                'method' => 'get'
            ]
        );
        ?>
    <input type="text"
        autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete') ?>"
        name="search" id="js-team-search" placeholder="Search Teams..." data-test="search-team-name" 
        value="<?php echo !empty($search) ? strip_tags($search) : ''; ?>"/>
    <?php $this->endWidget() ?>
</div>
<form id="admin_Teams">
    <input
        type="hidden"
        name="YII_CSRF_TOKEN"
        value="<?php echo Yii::app()->request->csrfToken ?>"
    />
    <table class="standard">
        <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Team Name</th>
                <th>Team Email</th>
                <th>Institution</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teams as $team) {?>
                <tr class="clickable js-clickable" data-id="<?php echo $team->id ?>"
                    data-uri="oeadmin/team/edit/<?php echo $team->id ?>">
                    <td><input type="checkbox"
                                name="Team[]"
                                value="<?php echo $team->id ?>"/></td>
                    <td data-test="list-team-name"><?=$team->name ?></td>
                    <td><?=$team->contact ? $team->contact->email : ''; ?></td>
                    <td><?=$team->institution ? $team->institution->name : ''; ?></td>
                    <td><i class="oe-i <?=($team->active ? 'tick' : 'remove');?> small"></i></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add Team',
                        [
                            'class' => 'button large',
                            'data-uri' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?=\CHtml::button(
                        'Deactivate Teams',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'Teams',
                            'data-uri' => $delete_uri,
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
