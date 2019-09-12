<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    ));?>
</header>
<main class="oe-whiteboard">
    <div class="wb3">
        <div class="oe-wb-widget data-list">
            <h3>Patient</h3>
            <div class="wb-data">
                <ul>
                    <li><?=$data->patient_name?></li>
                    <li><?=date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y')?></li>
                    <li><?=$data->hos_num?></li>
                </ul>
            </div>
        </div>
        <div class="oe-wb-widget data-single-extra green">
            <h3>Procedure</h3>
            <div class="wb-data">
                <?= $data->eye->name ?>
                <div class="extra-data">
                    <?=$data->procedure?>
                </div>
            </div>
        </div>
        <div class="oe-wb-widget data-single-extra">
            <h3>Lens</h3>
            <div class="wb-data">
                <?=$data->iol_power?>
                <div class="extra-data">
                    <?=$data->iol_model?>
                </div>
            </div>
        </div>
        <div class="oe-wb-widget data-single">
            <h3>Anaesthesia</h3>
            <div class="wb-data">
                <?php foreach ($data->booking->anaesthetic_type as $type) {
                    if ($type->name === 'LA') {
                        echo 'Local';
                    } elseif ($type->name === 'GA') {
                        echo 'General';
                    } else {
                        echo $type;
                    }
                }?>
            </div>
        </div>
        <div class="oe-wb-widget data-double-extra">
            <h3>Biometry</h3>
            <div class="wb-data">
                <!-- Add biometry readings here.-->
            </div>
        </div>
        <div class="oe-wb-widget data-single-extra">
            <h3>Predicted Outcome</h3>
            <div class="wb-data">
                <?=$data->predicted_refractive_outcome?>
                <div class="extra-data">
                    <!--Add model here.-->
                </div>
            </div>
        </div>
        <div class="oe-wb-widget data-list">
            <h3>
                Equipment
                <?php if ($this->getWhiteboard()->isEditable()) :?>
                <div class="edit-widget-btn">
                    <i class="oe-i pencil medium pro-theme"></i>
                </div>
                <?php endif; ?>
            </h3>
            <div class="wb-data">
                <ul>
                    <?php if ($data->predicted_additional_equipment) : ?>
                        <li><?=nl2br($data->predicted_additional_equipment)?></li>
                    <?php else :?>
                        <li>None</li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
        <div class="oe-wb-widget data-list">
            <h3>
                Comments
                <?php if ($this->getWhiteboard()->isEditable()) :?>
                    <div class="edit-widget-btn">
                        <i class="oe-i pencil medium pro-theme"></i>
                    </div>
                <?php endif; ?>
            </h3>
            <div class="wb-data">
                <ul>
                    <li><?=nl2br($data->comments)?></li>
                </ul>
            </div>
        </div>
        <div class="oe-wb-widget data-image">
            <h3>Axis</h3>
            <div class="wb-data image-fill">
                <!--Add image here.-->
            </div>
        </div>
        <div class="oe-wb-special risks">
            <h3>Allergies</h3>
            <?php if ($data->allergies === 'None') :?>
                <div class="alert-box success">No Allergies</div>
            <?php else :?>
                <?php foreach (explode(', ', $data->allergies) as $allergy) :?>
                    <div class="alert-box warning">
                        <?= $allergy ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <hr class="divider"/>
            <h3>Risks</h3>
            <?php if ($data->alpha_blocker_name !== 'Not checked') : ?>
                <div class="alert-box warning">
                    Alphablocker - <?=$data->alpha_blocker_name?>
                </div>
            <?php endif; ?>
            <?php if ($data->anticoagulant_name !== 'Not checked') : ?>
                <div class="alert-box warning">
                    Anticoagulants - <?=$data->anticoagulant_name?>
                </div>
            <?php endif; ?>
            <?php if ($data->inr !== 'None') :?>
            <div class="alert-box warning">INR: <?=$data->inr?></div>
            <?php endif; ?>
            <?php echo $this->getWhiteboard()->getPatientRisksDisplay(); ?>
            <?php if ($data->anticoagulant_name === 'No') : ?>
                <div class="alert-box success">
                    No Anticoagulants
                </div>
            <?php endif; ?>
            <?php if ($data->alpha_blocker_name === 'No') : ?>
                <div class="alert-box success">
                    No Alpha Blockers
                </div>
            <?php endif; ?>
        </div>
    </div>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer'); ?>
    </footer>
</main>
