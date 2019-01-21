<?php if ($this->patient->diagnoses || $this->patient->systemic_diagnoses): ?>
    <div class="row data-row">
        <div class="large-12 column">
            <div>Diagnoses:
                <a href="javascript:void(0)"
                   data-show-label="show" data-hide-label="hide"
                   onclick="toggleSection(this, '#collapse-section_<?php echo $this->patient->id . '_diagnosis'; ?>');">show
                </a>
            </div>
            <div id="collapse-section_<?php echo $this->patient->id . '_diagnosis'; ?>" style="display:none">
                <div class="diagnoses detail row data-row">
                    <div class="large-12 column">
                        <table>
                            <thead>
                            <tr>
                                <th>Diagnosis</th>
                                <th><?php echo Firm::contextLabel(); ?></th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->patient->diagnoses as $diagnosis):
                                // If the event doesn't exist, it means the event has been deleted.
                                if ($diagnosis->element_diagnoses->event):?>
                                <tr>
                                    <td><?php echo CHtml::encode($diagnosis) . ' (' . ($diagnosis->principal == 1 ? 'Principal' : 'Secondary') . ')'; ?></td>
                                    <td><?php echo CHtml::encode($diagnosis->element_diagnoses->event->episode->firm->getNameAndSubspecialty()); ?></td>
                                    <td><?php echo CHtml::encode(Helper::convertDate2NHS($diagnosis->element_diagnoses->event->event_date)); ?></td>
                                </tr>
                                <?php endif;
                            endforeach; ?>
                            <?php foreach ($this->patient->systemic_diagnoses as $diagnosis):
                                // If the event doesn't exist, it means the event has been deleted.
                                if ($diagnosis->element->event):?>
                                <tr>
                                    <td><?php echo CHtml::encode($diagnosis->getDisplayDisorder()) . ' (Secondary)'; ?></td>
                                    <td><?php echo CHtml::encode($diagnosis->element->event->episode->firm->getNameAndSubspecialty()); ?></td>
                                    <td><?php echo CHtml::encode(Helper::convertDate2NHS($diagnosis->element->event->event_date)); ?></td>
                                </tr>
                                <?php endif;
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->patient->medications): ?>
    <div class="row data-row">
        <div class="large-12 column">
            <div>Medications:
                <a href="javascript:void(0)"
                   data-show-label="show" data-hide-label="hide"
                   onclick="toggleSection(this, '#collapse-section_<?php echo $this->patient->id . '_medication'; ?>');">show
                </a>
            </div>

            <div id="collapse-section_<?php echo $this->patient->id . '_medication'; ?>" style="display:none">
                <div class="medications detail row data-row">
                    <div class="large-12 column">
                        <table>
                            <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Administration</th>
                                <th>Date From</th>
                                <th>Date To</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->patient->medications as $medication): ?>
                                <tr>
                                    <td><?php echo $medication->getMedicationDisplay(); ?></td>
                                    <td><?php echo $medication->getAdministrationDisplay(); ?></td>
                                    <td><?php echo Helper::formatFuzzyDate($medication->start_date); ?></td>
                                    <td><?php echo isset($medication->end_date) ? Helper::formatFuzzyDate($medication->end_date) : ''; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>