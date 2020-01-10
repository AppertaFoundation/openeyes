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
    <div class="fpten-form-column <?= $form_css_class ?>-site">
        <table style="">
            <tbody>
            <tr>
                <td>
                    <?= $this->getDepartmentName()?><?= $this->getDepartmentName() ? '<br/>' : null ?>
                    <?= $this->site->name ?><br/>
                    <?= $this->site->contact->address->address1 ?><br/>
                    <?= $this->site->contact->address->address2 ?>
                </td>
                <?php if ($side === 'left') : ?>
                <td class="fpten-site-code">
                    <?= $cost_code ?>
                </td>
                <?php else : ?>
                <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>
                    <?= $this->site->contact->address->city ?><br/>
                    <?= $this->site->contact->address->county ?><?= $this->site->contact->address->county ? '<br/>' : null ?>
                    Tel: <?= $this->site->telephone ?><br/>
                    <?= $this->getInstitutionName() ?: $this->site->institution->name ?>
                </td>
                <td <?= $form_css_class === 'wpten' ? 'class="wpten-site-postcode"' : null ?>>
                    <?= $this->site->contact->address->county ? '<br/>' : null?>
                    <?= $this->site->contact->address->postcode ?><br/>
                    <br/>
                    <?= ($side === 'left') ? $this->site->institution->remote_id : null ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php if ($side === 'left') : ?>
        <span class="fpten-form-column fpten-prescriber-code">HP</span>
    <?php endif; ?>
</div>
