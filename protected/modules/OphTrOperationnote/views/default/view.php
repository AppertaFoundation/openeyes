<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
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

<?php $this->beginContent('//patient/event_container', array('no_face'=>true));

$this->moduleNameCssClass .= ' highlight-fields';
?>

<?php
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);

$template_update = $this->request->getParam('template');
$template = $this->event->template;
?>

<?php
// Event actions
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}
?>

<input type="hidden" id="moduleCSSPath" value="<?php echo $this->assetPath ?>/css"/>
<?php if (\Yii::app()->user->hasFlash('error')) {?>
  <div class="cols-12 column">
    <div class="alert-box issue with-icon">
        <?=\Yii::app()->user->getFlash('error');?>
    </div>
  </div>
<?php }?>

<?php if ($this->event->delete_pending) { ?>
  <div class="alert-box alert with-icon">
    This event is pending deletion and has been locked.
  </div>
<?php } ?>

<?php if ($warnings) { ?>
  <div class="cols-12 column">
    <div class="alert-box patient with-icon">
        <?php foreach ($warnings as $warn) { ?>
          <strong><?php echo $warn['long_msg']; ?></strong>
          - <?php echo $warn['details'];
        } ?>
    </div>
  </div>
<?php } ?>

<?php if ($template_update) : ?>
    <?php if (!$template) : ?>
<div class="auto-alert prefill has-actions">
    Save this Operation Note setup as a pre-fill template?
    <i class="oe-i info small pad js-tooltip" data-tip="{'type':1, 'tip': 'Key input data from this OpNote will be saved and can be used to pre-fill key data fields. The template can be associate to auto-fill depending on the Procedure selected.'}"></i>
    <div class="alert-actions">
        <button type="button" class="blue hint js-open-save-opnote-template-popup" data-test="save-new-template">Create new pre-fill template</button>
    </div>
</div>
    <?php else : ?>
<div class="auto-alert prefill has-actions">
    You have modified an operation note based on an existing template: <?= $template->name ?>
    <i class="oe-i info small pad js-tooltip" data-tip="{'type':1, 'tip': 'Key input data from this OpNote will be saved and can be used to pre-fill key data fields. The template can be associate to auto-fill depending on the Procedure selected.'}"></i>
    <div class="alert-actions">
        <?php if ($template_update === EventTemplate::UPDATE_OR_CREATE) :
            ?><button type="button" class="blue hint js-open-update-opnote-template-popup">Update template</button><?php
        endif; ?>
        <button type="button" class="blue hint js-open-save-opnote-template-popup" data-test="save-new-template">Create new pre-fill template</button>
    </div>
</div>
    <?php endif; ?>
<?php elseif ($template && $template->source_event_id !== $this->event->id) : ?>
<div class="auto-alert prefill">
    Pre-filled using: <?= $template->name ?>
</div>
<?php endif; ?>

<?php if ($template_update) : ?>
<div class="oe-popup-wrap js-save-opnote-template-popup" style="display: none">
  <div class="oe-popup">
    <div class="remove-i-btn"></div>
    <div class="title">Create pre-fill template for: Operation note</div>
    <div class="oe-popup-content wide">
      <form method="POST" action="/OphTrOperationnote/Default/saveTemplate">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <input type="hidden" name="event_id" value="<?= $this->event->id ?>" />
        <div class="flex-t">
          <div class="cols-6">
            <p>Select name for template <small class="fade">(max. 48 characters)</small></p>
            <input name="template_name" class="cols-full js-opnote-template-name" placeholder="Template name" minlength="4" maxlength="48" required="" data-test="template-name">
            <h4>For procedure(s)</h4>
            <ul class="dot-list large-text">
              <?php
                $procedure_list = Element_OphTrOperationnote_ProcedureList::model()->find('event_id = ?', [$this->event->id]);
                foreach ($procedure_list->procedures as $procedure) : ?>
              <li><?= $procedure->term ?></li>
                <?php endforeach; ?>
            </ul>
          </div>
          <div class="cols-5">
            <h4>Your templates</h4>
            <ul class="row-list">
              <?php
                $procedure_set = ProcedureSet::findForProcedureList($procedure_list->id);
                $user_id = Yii::app()->user->id;

                if ($procedure_set) :
                    $user_templates_criteria = new CDbCriteria();

                    $user_templates_criteria->join = 'JOIN ophtroperationnote_template ont ON ont.event_template_id = t.event_template_id';
                    $user_templates_criteria->addCondition('user_id = :user_id');
                    $user_templates_criteria->addCondition('proc_set_id = :procedure_set_id');
                    $user_templates_criteria->params = [':user_id' => $user_id, ':procedure_set_id' => $procedure_set->id];

                    $user_templates = EventTemplateUser::model()->findAll($user_templates_criteria);

                    foreach ($user_templates as $opnote_template) : ?>
              <li><?= $opnote_template->event_template->name ?></li>
                        <?php
                    endforeach;
                endif;
                ?>
            </ul>
          </div>
        </div>
        <div class="popup-actions">
          <button type="submit" class="green hint js-save-opnote-template" data-test="save-template">Save new template</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
   $(document).ready(function() {
     $('.js-open-save-opnote-template-popup').on('click', function() {
       $('.js-save-opnote-template-popup').show();
     });

     $('.js-save-opnote-template-popup > .oe-popup > .remove-i-btn').on('click', function() {
       $('.js-save-opnote-template-popup').hide();
     });
   });
</script>
<?php endif; ?>

<?php if ($template_update === EventTemplate::UPDATE_OR_CREATE && $template) : ?>
    <?php
    $opnote_template = OphTrOperationnote_Template::model()->find('event_template_id = ?', [$template->id]);
    ?>
<div class="oe-popup-wrap js-update-opnote-template-popup" style="display: none">
    <div class="oe-popup">
        <div class="remove-i-btn"></div>
        <div class="title">Update pre-fill template</div>
        <div class="oe-popup-content undefined">
            <form method="POST" action="/OphTrOperationnote/Default/updateTemplate">
                <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
                <input type="hidden" name="event_id" value="<?= $this->event->id ?>" />
                <input type="hidden" name="template_id" value="<?= $template->id ?>" />
                <div class="row">
                    <h4>Update your template with the amends made in this Event</h4>
                    <p><?= $template->name ?></p>
                    <h4>For procedure(s)</h4>
                    <ul class="dot-list large-text">
                        <?php foreach ($opnote_template->procedure_set->procedures as $procedure) : ?>
                        <li><?= $procedure->term ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <hr class="divider">
                <div class="popup-actions">
                    <button type="submit" class="blue hint cols-5 js-update-opnote-template">Update template</button>
                    <button type="button" class="red hint cols-5 js-cancel-update-opnote-template">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
  $(document).ready(function() {
     $('.js-open-update-opnote-template-popup').on('click', function() {
       $('.js-update-opnote-template-popup').show();
     });

     $('.js-update-opnote-template-popup > .oe-popup > .remove-i-btn, .js-update-opnote-template-popup .js-cancel-update-opnote-template').on('click', function() {
       $('.js-update-opnote-template-popup').hide();
     });
   });
</script>
<?php endif; ?>

<?php $this->renderOpenElements($this->action->id); ?>
<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent(); ?>
