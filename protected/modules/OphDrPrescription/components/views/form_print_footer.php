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
        Tel: <?= $this->site->telephone ?>
        <br/>
        <?= $this->site->institution->name ?>
    </div>

    <div class="fpten-form-column <?= $form_css_class ?>-site-code">
        <span class="fpten-registration-code">
            <?= ($side === 'left') ? str_replace('GMC', '', $this->user->registration_code) : '&nbsp;' ?>
        </span>
        <br/>
        <?= $this->site->contact->address->address2 ? '<br/>' : null ?>
        <br/>
        <?= $this->site->contact->address->county ? '<br/>' : null ?>
        <br/>
        <br/>
        <br/>
        <br/>
        <?php if ($side === 'left') {
            echo $this->firm->cost_code ?: $this->getDefaultCostCode();
        } elseif ($side === 'right') {
            echo "Page {$this->getSplitPageNumber()} of {$this->getTotalSplitPages()}";
            if ($this->isSplitPrinting()) {
                if ($this->getSplitPageNumber() === $this->getTotalSplitPages()) {
                    $this->resetSplitPageCount($this->getCurrentItem()->fpTenLinesUsed());
                } else {
                    $this->addSplitPage();
                }
            } else {
                $this->resetSplitPageCount($this->getCurrentItem()->fpTenLinesUsed());
            }
        }?>
    </div>
    <?php if ($side === 'left') : ?>
        <span class="fpten-form-column fpten-prescriber-code">HP</span>
    <?php endif; ?>
</div>
