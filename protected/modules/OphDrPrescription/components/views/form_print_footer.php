<?php
    /**
     * @var string $side
     * @var string $form_css_class
     */
?>
<div class="fpten-form-row">
    <div  class="fpten-form-column <?= $form_css_class ?>-date">
        <?= date('d/m/Y') ?>
    </div>
</div>
<div class="fpten-form-row">
    <div class="fpten-form-column <?= $form_css_class ?>-site">
        <?= $this->firm->cost_code ?: $this->getDefaultCostCode()?>
        <br/>
        <?= $this->site->institution->name ?>
        <br/>
        <?= $this->user->getFullNameAndTitle() ?> <?= ($side === 'left') ? str_replace('GMC', '', $this->user->registration_code) : '&nbsp;' ?>
        <br/>
        <?= $this->site->contact->address->address1 ?>
        <?= $this->site->contact->address->address2 ? '<br/>' : null ?>
        <?= $this->site->contact->address->address2 ?>
        <br/>
        <?= $this->site->contact->address->city ?>
        <?= $this->site->contact->address->county ? '<br/>' : null ?>
        <?= $this->site->contact->address->county ?>
        <br/>
        <?= str_replace(' ', '', $this->site->contact->address->postcode) ?> <br/>
        Tel: <?= $this->site->telephone ?>
        <br/>
    </div>

    <div class="fpten-form-column <?= $form_css_class ?>-site-code">
        <br/>
        <br/>
    </div>
    <?php if ($side === 'left') : ?>
        <span class="fpten-form-column fpten-prescriber-code">HP</span>
    <?php endif; ?>
</div>
