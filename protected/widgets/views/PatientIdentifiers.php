<?php if (!empty($this->identifiers)) { ?>
    <i class="oe-i info <?= $this->tooltip_size ?> small-icon pro-theme  js-has-tooltip" data-tooltip-content="
    <?php foreach ($this->identifiers as $identifier) { ?>
        <?php $this->render('PatientIdentifiers_entry', ['identifier' => $identifier]); ?>
    <?php } ?>
    <?php if (!empty($this->deleted_identifiers)) { ?>
    <hr>
    Previous Numbers
    <br>
        <?php foreach ($this->deleted_identifiers as $identifier) { ?>
            <?php $this->render('PatientIdentifiers_entry', ['identifier' => $identifier]); ?>
        <?php } ?>
    <?php } ?>
"></i>

<?php } ?>