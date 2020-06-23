<?php
/**
 * @var Trial $trial
 * @var TrialPermission $permission
 */

$editing = in_array($this->action->id, ['update', 'create'], true);
?>

<!-- CERA-491 The css is hard-coded to solve the issue that the long title name break the layout. -->
<nav class="oe-full-header flex-layout">
  <?php if (strlen($title) <= 124) {?>
      <div class="title wordcaps" ><?= $title ?></div>
  <?php } else {?>
      <div class="title wordcaps" style="font-size: 0.8em; line-height: 1.3; max-width: 1000px "><?= $title ?></div>
  <?php } ?>

  <div>
        <?php if ($editing) { ?>
        <button class="button header-tab green" name="save" type="submit" form="trial-form">
            <?= $trial->getIsNewRecord() ? 'Create' : 'Save' ?>
        </button>

            <?= CHtml::link(
                'Cancel',
                $trial->getIsNewRecord() ? $this->createUrl('index') : $this->createUrl(
                    'view',
                    array('id' => $trial->id)
                ),
                array('class' => 'button header-tab red')
            ) ?>
        <?php } else { ?>
            <?php if ($permission && $permission->can_view && !$trial->getIsNewRecord()) { ?>
                <?= CHtml::link(
                    'View',
                    $this->createUrl('view', array('id' => $trial->id)),
                    array('class' => 'button header-tab ' . ($this->action->id === 'view' ? 'selected' : ''))
                ) ?>
            <?php } ?>
            <?php if ($permission && $permission->can_edit) { ?>
                <?= CHtml::link(
                    'Edit',
                    $this->createUrl('update', array('id' => $trial->id)),
                    array('class' => 'button header-tab ' . ($editing ? 'selected' : ''))
                ) ?>
            <?php } ?>
            <?php if ($permission && $permission->can_manage) { ?>
                <?php if ($trial->is_open) { ?>
                    <?= CHtml::link(
                        'Close Trial',
                        $trial->getIsNewRecord() ? $this->createUrl('index') : $this->createUrl(
                            'close',
                            array('id' => $trial->id)
                        ),
                        array('class' => 'button header-tab red')
                    ) ?>
                <?php } else { ?>
                    <?= CHtml::link(
                        'Re-open Trial',
                        $trial->getIsNewRecord() ? $this->createUrl('index') : $this->createUrl(
                            'reopen',
                            array('id' => $trial->id)
                        ),
                        array('class' => 'button header-tab green')
                    ) ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
  </div>
</nav>