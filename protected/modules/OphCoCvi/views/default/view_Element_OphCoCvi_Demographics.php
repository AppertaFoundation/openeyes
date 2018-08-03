<div class="element-data full-width flex-layout flex-top col-gap">
  <div class="cols-6">
    <table class="label-value">
      <tbody>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('title_surname') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->title_surname) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('other_names') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->other_names) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('date_of_birth') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->NHSDate('date_of_birth')) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('nhs_number') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->nhs_number) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('address') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= nl2br(CHtml::encode($element->address)) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('postcode') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->postcode) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('email') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->email) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('telephone') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->telephone) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('gender') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->gender ? $element->gender->name : '') ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('ethnic_group') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->ethnic_group ? $element->ethnic_group->name : '') ?></div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
  <div class="cols-6">
    <table class="label-value">
      <tbody>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('gp_name') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->gp_name) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('gp_address') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= nl2br(CHtml::encode($element->gp_address)) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('gp_telephone') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->gp_telephone) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('la_name') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->la_name) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('la_address') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= nl2br(CHtml::encode($element->la_address)) ?></div>
        </td>
      </tr>
      <tr>
        <td>
          <div class="data-label"><?= $element->getAttributeLabel('la_telephone') ?>:</div>
        </td>
        <td>
          <div class="data-value"><?= CHtml::encode($element->la_telephone) ?></div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</div>

