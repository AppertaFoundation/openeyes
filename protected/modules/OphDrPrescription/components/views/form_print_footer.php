<?php
    /**
     * @var string $side
     * @var string $form_css_class
     */
    $cost_code = $this->firm->cost_code ?: $this->getDefaultCostCode();
?>
<div class="fpten-form-row">
    <div  class="fpten-form-column <?= $form_css_class ?>-date">
        <?= date('d/m/Y') ?>
    </div>
</div>
<div class="fpten-form-row">
    <div class="fpten-form-column <?= $form_css_class ?>-site" >
        <?= $this->user->getFullNameAndTitle() ?><br/>
        <?= $this->getDepartmentName()?><?= $this->getDepartmentName() ? '<br/>' : null ?>
        <?= $this->site->name ?><br/>
        <?= $this->site->contact->address->address1 ?><?= $this->site->contact->address->address2 ? '<br/>' : null ?>
        <?= $this->site->contact->address->address2 ?><br/>
        <?= $this->site->contact->address->city . ' ' . $this->site->contact->address->county ?><br/>
        Tel: <?= $this->site->telephone ?><br/>
        <?= $this->getInstitutionName() ?: $this->site->institution->name ?>
    </div>
    <div class="fpten-form-column <?= $form_css_class ?>-site-code">
        <?= ($side === 'left') ? str_replace('GMC', '', $this->user->registration_code) : '&nbsp;' ?><br/>
        <?= $this->getDepartmentName() ? '<br/>' : null ?>
        <?= ($side === 'left') ? '<strong>' . $cost_code . '</strong>' : null?><br/>
        <br/>
        <?= $this->site->contact->address->address2 ? '<br/>' : null ?>
        <?= $this->site->contact->address->postcode ?><br/>
        <br/>
        <?= ($side === 'left') ? $this->site->institution->remote_id : null ?>
    </div>
    <?php if ($side === 'left') : ?>
        <span class="fpten-form-column fpten-prescriber-code">HP</span>
    <?php endif; ?>
</div>
