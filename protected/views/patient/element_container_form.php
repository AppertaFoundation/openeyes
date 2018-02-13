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
<?php
  $eye_divider_list = ['Visual Function','Visual Acuity', 'Near Visual Acuity', 'Colour Vision','Anterior Segment','Intraocular Pressure',
      'Refraction','Gonioscopy','Adnexal', 'Dilation', 'Pupillary Abnormalities', 'CCT','Keratoconus Monitoring',
      'Posterior Pole'];
$section_classes = array('edit-'.CHtml::modelName($element->elementType->name));
$section_classes[] =  'element full';
if ($this->isRequired($element)) {
    $section_classes[] = 'required';
}
if ($this->isHiddenInUI($element)) {
    $section_classes[] = 'hide';
}
if (is_subclass_of($element, 'SplitEventTypeElement')) {
    $section_classes[] = 'eye-divider';
}

$element_Type = $element->getElementType();
?>

<?php if (!preg_match('/\[\-(.*)\-\]/', $element->elementType->name)) { ?>

<section
	class="<?php echo implode(' ', $section_classes); ?> <?php echo CHtml::modelName($element->elementType->class_name) ?> "
	data-element-type-id="<?php echo $element->elementType->id?>"
  data-element-type-class="<?php echo CHtml::modelName($element->elementType->class_name) ?>"
  data-element-type-name="<?php echo $element->elementType->name?>"
	data-element-display-order="<?php echo $element->elementType->display_order?>"
  data-element-parent-id = "<?php $et = $element->getElementType();
  if ($et->isChild()){
    echo $et->parent_element_type_id;
  } ?>">

      <?php if (!property_exists($element, 'hide_form_header') || !$element->hide_form_header) { ?>
        <header class="element-header">
		<!-- Element title -->
          <h3 class="element-title"><?php echo $element->getFormTitle() ?></h3>
        </header>
    <!-- Additional element title information -->
          <?php if (isset($this->clips['element-title-additional'])) { ?>
          <div class="element-title-additional">
              <?php
              $this->renderClip('element-title-additional');
              // don't want the header clip to repeat in child elements
              unset($this->clips['element-title-additional']);
              ?>
          </div>
          <?php } ?>
    <!-- Element actions -->
        <div class="element-actions">
          <!-- order is important for layout because of Flex -->
            <?php if ($this->canViewPrevious($element) || $this->canCopy($element)) { ?>
              <span class="js-duplicate-element">
            <i class="oe-i duplicate"></i>
          </span>
            <?php } ?>
      <!-- remove MUST be last element -->
          <span class="<?= ($this->isRequiredInUI($element)) ? 'disabled' : 'js-remove-element' ?>"
                title="<?= ($this->isRequiredInUI($element)) ? 'Mandatory Field' : '' ?>">
            <i class="oe-i trash-blue"></i>
          </span>
        </div>
      <?php } ?>


      <?php echo $content; ?>

  </section>
<?php } else { ?>
  <section
      class="<?php echo implode(' ', $section_classes); ?>"
      data-element-type-id="<?php echo $element->elementType->id ?>"
      data-element-type-class="<?php echo CHtml::modelName($element->elementType->class_name) ?>"
      data-element-type-name="<?php echo $element->elementType->name ?>"
      data-element-display-order="<?php echo $element->elementType->display_order ?>">

      <?php echo $content; ?>

	</section>
    <?php $this->renderChildOpenElements($element, $this->action->id, $form, $data)?>
<?php } ?>

<script>
  $('.js-remove-element').on('click',function (e) {
    e.preventDefault();
    var parent = $(this).parent().parent();
    removeElement(parent);
  });

  $('.js-add-select-search').on('click',function (e) {
    e.preventDefault();
    $('.oe-add-select-search').hide();
    $(e.target).parent().find('.oe-add-select-search').show();
  });

  $('.oe-add-select-search').find('.add-icon-btn').on('click', function(e){
    e.preventDefault();
    $(e.target).parent('.oe-add-select-search').hide();
  });

  //Set the option selecting function
  $('.oe-add-select-search').find('.add-options').find('li').each(function () {
    if ($(this).text() !== "") {
      $(this).on('click', function () {
        if ($(this).hasClass('selected')) {
          $(this).removeClass('selected');
        } else {
          if($(this).parent('.add-options').attr('data-multi') === "false"){
            $(this).parent('.add-options').find('li').removeClass('selected');
          }
          $(this).addClass('selected');
        }
      });
    }
  });

  $(".oe-add-select-search .close-icon-btn").click(function(e) {
      $(e.target).closest('.oe-add-select-search').hide();
  });

  $('.js-add-comments').on('click', function (e) {
    e.preventDefault();
    var container = $(e.target).siblings($(e.target).attr('data-input'));
    $(container).show();
    $(e.target).hide();
  });
</script>
