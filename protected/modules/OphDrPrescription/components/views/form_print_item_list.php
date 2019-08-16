<?php
    /**
     * @var string $side
     * @var int $page_number
     * @var string $form_css_class
     */
?>

<div class="fpten-form-row">
    <div class="fpten-form-column">
        <?php if ($this->print_mode === 'FP10') : ?>
            <div id="fpten-prescriber" class="fpten-form-row">
                HOSPITAL PRESCRIBER
            </div>
        <?php endif; ?>
        <div class="fpten-form-row">
            <div id="<?= $form_css_class ?>-prescription-list" class="fpten-form-column">
                <?php
                    $prescription_lines_used = 0;
                    for ($j = $this->current_item_index; $j < $this->total_items; $j++) {
                    $item = $this->items[$j];
                    $group_name = $item->dispense_condition->name;
                    if (str_replace('{form_type}', $this->print_mode, $group_name) === 'Print to ' . $this->print_mode) {
                    $drug_label = $item->drug->label;
                    $dose = (is_numeric($item->dose) ? "{$item->dose} {$item->drug->dose_unit}" : $item->dose) . ', ' . $item->route->name . ($item->route_option ? ' (' . $item->route_option->name . ')' : null);
                    $frequency = "{$item->frequency->long_name} for {$item->duration->name}";
                    $total_tapers = count($item->tapers);

                    $this->current_item_copy = $this->current_item_index ?: $this->current_item_copy;
                    $this->current_attr_copy = $this->current_item_attr ?: $this->current_attr_copy;
                    $this->current_taper_copy = $this->current_taper_index ?: $this->current_taper_copy;

                    // Work out how many lines are being used for this prescription item. If it exceeds the maximum lines - currently used lines, separate it onto its own script.
                    // If the lines used is greater than the maximum, split the printout between multiple pages.
                    if ($item->fpTenLinesUsed() + 1 > MAX_FPTEN_LINES - $prescription_lines_used && !$this->split_print) {
                        if ($item->fpTenLinesUsed() + 1 > MAX_FPTEN_LINES) {
                            // Single item will not fit on a single script.
                            $this->split_print = true;
                        }

                        if ($side === 'right') {
                            // We only want to change the page counter and the base item index once the right side of the page has been rendered.
                            $this->page_count++;
                            $this->current_item_index = $j;
                        }
                        if ($page_number === 0 && $j === 0 && $this->split_print && $side === 'left') {
                            // Do nothing as we want the LHS of the split item to render immediately.
                        } else {
                            break;
                        }
                    }
                ?>
                <div class="fpten-prescription-item fpten-form-row">
                    <?php
                        if ($item->getAttrLength('item_drug') > MAX_FPTEN_LINES - $prescription_lines_used) {
                            if ($side === 'right' && !$this->current_item_attr) {
                                $this->page_count++;
                                $this->current_item_index = $j;
                                $this->current_item_attr = 'item_drug';
                            }
                            $this->end_of_page = true;
                        }
                        if (!$this->current_item_attr || $this->current_item_attr === 'item_drug') {
                            echo "$drug_label<br/>";
                            $this->current_item_attr = null;
                            $prescription_lines_used += $item->getAttrLength('item_drug');
                        }
                        if ($item->getAttrLength('item_dose') > MAX_FPTEN_LINES - $prescription_lines_used) {
                            if ($side === 'right' && !$this->current_item_attr) {
                                $this->page_count++;
                                $this->current_item_index = $j;
                                $this->current_item_attr = 'item_dose';
                            }
                            $this->end_of_page = true;
                        }
                        if (!$this->current_item_attr || $this->current_item_attr === 'item_dose') {
                            echo "Dose: $dose<br/>";
                            $this->current_item_attr = null;
                            $prescription_lines_used += $item->getAttrLength('item_dose');
                        }
                        if ($item->getAttrLength('item_frequency') > MAX_FPTEN_LINES - $prescription_lines_used) {
                            if ($side === 'right' && !$this->current_item_attr) {
                                $this->page_count++;
                                $this->current_item_index = $j;
                                $this->current_item_attr = 'item_frequency';
                            }
                            $this->end_of_page = true;
                        }
                        if (!$this->current_item_attr || $this->current_item_attr === 'item_frequency') {
                            echo "Frequency: $frequency";
                            $this->current_item_attr = null;
                            $prescription_lines_used += $item->getAttrLength('item_frequency');
                        }
                        for ($index = $this->current_taper_index; $index < $total_tapers; $index++) {
                            $taper = $item->tapers[$index];
                            if ($item->getAttrLength("taper{$index}_label") > MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->current_item_attr) {
                                    $this->page_count++;
                                    $this->current_item_index = $j;
                                    $this->current_taper_index = $index;
                                    $this->current_item_attr = "taper{$index}_label";
                                }
                                $this->end_of_page = true;
                                break;
                            }
                            if (!$this->current_item_attr || $this->current_item_attr === "taper{$index}_label") {
                                echo '<br/>then<br/>';
                                $this->current_item_attr = null;
                                $prescription_lines_used += $item->getAttrLength("taper{$index}_label");
                            }
                            if ($item->getAttrLength("taper{$index}_dose") > MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->current_item_attr) {
                                    $this->page_count++;
                                    $this->current_item_index = $j;
                                    $this->current_taper_index = $index;
                                    $this->current_item_attr = "taper{$index}_dose";
                                }
                                $this->end_of_page = true;
                                break;
                            }
                            if (!$this->current_item_attr || $this->current_item_attr === "taper{$index}_dose") {
                                echo 'Dose: ' . (is_numeric($taper->dose) ? ($taper->dose . ' ' . $item->drug->dose_unit) : $taper->dose) . ', ' . $item->route->name . ($item->route_option ? ' (' . $item->route_option->name . ')' : null) . '<br/>';
                                $this->current_item_attr = null;
                                $prescription_lines_used += $item->getAttrLength("taper{$index}_dose");
                            }
                            if ($item->getAttrLength("taper{$index}_frequency") > MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->current_item_attr) {
                                    $this->page_count++;
                                    $this->current_item_index = $j;
                                    $this->current_taper_index = $index;
                                    $this->current_item_attr = "taper{$index}_frequency";
                                }
                                $this->end_of_page = true;
                                break;
                            }
                            if (!$this->current_item_attr || $this->current_item_attr === "taper{$index}_frequency") {
                                echo "Frequency: {$taper->frequency->long_name} for {$taper->duration->name}";
                                $this->current_item_attr = null;
                                $prescription_lines_used += $item->getAttrLength("taper{$index}_frequency");
                            }
                        }
                        $prescription_lines_used++; // Add 1 line for the horizontal rule.
                        echo '</div>';

                        if ($this->end_of_page) {
                            $this->end_of_page = false;
                            break;
                        }
                        if ($this->split_print && $side === 'right') {
                            $this->split_print = false;
                            $this->split_print_end = false;
                        } else if ($this->split_print && $side === 'left' && $page_number !== 0) {
                            // This will never apply for the first page, and should only apply for the left hand side.
                            $this->split_print = false;
                            $this->split_print_end = true;
                        }
                        }
                        }
                    ?>
                    <div class="fpten-prescription-list-filler fpten-form-row">
                        <?php for ($line = 0; $line < MAX_FPTEN_LINES - $prescription_lines_used; $line++) {
                            if ($line !== 0) {
                                echo '<br/>';
                            }
                            echo ($side === 'left') ? 'x' : 'GP COPY';
                        }
                            if (!$this->split_print && $this->split_print_end && $side === 'left') {
                                $this->current_item_attr = $this->current_attr_copy;
                                $this->current_item_index = $this->current_item_copy;
                                $this->split_print = true;
                                $this->split_print_end = false;
                            }?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($side === 'left') : ?>
            <span class="fpten-form-column fpten-prescriber-code">HP</span>
        <?php endif; ?>
    </div>
