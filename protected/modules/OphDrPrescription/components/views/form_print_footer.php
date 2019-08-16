<?php
    /**
     * @var string $side
     * @var string $form_css_class
     */
?>

<div class="fpten-form-row">
    <div id="<?= $form_css_class ?>-date" class="fpten-form-column">
        <?= date('d/m/Y') ?>
    </div>
</div>
<div class="fpten-form-row">
    <div id="<?= $form_css_class ?>-site" class="fpten-form-column">
        <?= $this->user->getFullNameAndTitle() ?>
        <br/>
        <?= $this->site->name ?>
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
        Tel: <?= $this->site->contact->primary_phone ?>
        <br/>
        <?= $this->site->institution->name ?>
    </div>
    <?php if ($side === 'left') : ?>
        <div id="<?= $form_css_class ?>-site-code" class="fpten-form-column">
            <span id="fpten-registration-code"><?= str_replace('GMC', '', $this->user->registration_code) ?></span>
            <br/>
            <?= $this->site->contact->address->address2 ? '<br/>' : null ?>
            <br/>
            <?= $this->site->contact->address->county ? '<br/>' : null ?>
            <br/>
            <br/>
            <br/>
            <br/>
            <?= $this->firm->cost_code ?: $this->default_cost_code ?>
        </div>
        <span class="fpten-form-column fpten-prescriber-code">HP</span>
    <?php endif; ?>
</div>
