<?php
    /**
     * @var string $side
     * @var int $page_number
     * @var string $form_css_class
     */

    $current_item_copy = 0;
    $current_taper_copy = 0;
    $current_attr_copy = null; // If a single item is greater than 30 lines, this will capture the attribute that overflows.

    $end_of_page = false;
    $split_print_end = false;
    $prescription_lines_used = 0;
?>

<div class="fpten-form-row">
    <div class="fpten-form-column">
        <?php if ($this->getPrintMode() === 'FP10') : ?>
            <div id="fpten-prescriber" class="fpten-form-row">
                HOSPITAL PRESCRIBER
            </div>
        <?php endif; ?>
        <div class="fpten-form-row">
            <div id="<?= $form_css_class ?>-prescription-list" class="fpten-form-column">
                <?php
                for ($j = $this->current_item_index; $j < $this->getTotalItems(); $j++) {
                    $item = $this->items[$j];
                    if ($this->isPrintable($item)) {
                        $drug_label = $item->drug->label;
                        $total_tapers = count($item->tapers);

                        $current_item_copy = $this->current_item_index ?: $current_item_copy;
                        $current_attr_copy = $this->getCurrentItemAttr() ?: $current_attr_copy;
                        $current_taper_copy = $this->current_taper_index ?: $current_taper_copy;

                        // Work out how many lines are being used for this prescription item. If it exceeds the maximum lines - currently used lines, separate it onto its own script.
                        // If the lines used is greater than the maximum, split the printout between multiple pages.
                        if ($item->fpTenLinesUsed() + 1 > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used && !$this->isSplitPrinting()) {
                            if ($item->fpTenLinesUsed() + 1 > PrescriptionFormPrinter::MAX_FPTEN_LINES) {
                                // Single item will not fit on a single script.
                                $this->enableSplitPrint();
                            }

                            if ($side === 'right') {
                                // We only want to change the page counter and the base item index once the right side of the page has been rendered.
                                $this->addPages();
                                $this->current_item_index = $j;
                            }
                            // We want to do nothing if all conditions below are true as we want the LHS of the split item to render immediately.
                            // Otherwise, break the loop.
                            if (!($page_number === 0 && $j === 0 && $side === 'left' && $this->isSplitPrinting())) {
                                break;
                            }
                        }
                        ?>
                <div class="fpten-prescription-item fpten-form-row">
                        <?php
                        foreach (array('drug', 'dose', 'frequency', 'comment') as $attr) {
                            if ($item->getAttrLength("item_$attr") > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->getCurrentItemAttr()) {
                                    $this->addPages();
                                    $this->current_item_index = $j;
                                    $this->setCurrentAttr($attr);
                                }
                                $end_of_page = true;
                            }
                            if (!$this->getCurrentItemAttr() || $this->getCurrentItemAttr() === "item_$attr") {
                                switch ($attr) {
                                    case 'drug':
                                        echo "$drug_label<br/>";
                                        break;
                                    case 'dose':
                                        echo "{$item->fpTenDose()}<br/>";
                                        break;
                                    case 'frequency':
                                        echo $item->fpTenFrequency();
                                        break;
                                    case 'comment':
                                        if ($item->comments) {
                                            echo "<br/>Comment: $item->comments";
                                        }
                                        break;
                                }
                                $this->setCurrentAttr();
                                $prescription_lines_used += $item->getAttrLength("item_$attr");
                            }
                        }
                        for ($index = $this->current_taper_index; $index < $total_tapers; $index++) {
                            $taper = $item->tapers[$index];
                            foreach (array('label', 'dose', 'frequency') as $attr) {
                                if ($item->getAttrLength("taper{$index}_$attr") > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used) {
                                    if ($side === 'right' && !$this->getCurrentItemAttr()) {
                                        $this->addPages();
                                        $this->current_item_index = $j;
                                        $this->current_taper_index = $index;
                                        $this->setCurrentAttr($attr, $index);
                                    }
                                    $end_of_page = true;
                                    break;
                                }
                                if (!$this->getCurrentItemAttr() || $this->getCurrentItemAttr() === "taper{$index}_$attr") {
                                    switch ($attr) {
                                        case 'label':
                                            echo '<br/>then<br/>';
                                            break;
                                        case 'dose':
                                            echo "{$taper->fpTenDose()}<br/>";
                                            break;
                                        case 'frequency':
                                            echo $taper->fpTenFrequency();
                                            break;
                                    }

                                    $this->setCurrentAttr();
                                    $prescription_lines_used += $item->getAttrLength("taper{$index}_$attr");
                                }
                            }
                        }
                        $prescription_lines_used++; // Add 1 line for the horizontal rule.
                        ?>
                        </div>
                        <?php if ($end_of_page) {
                            $end_of_page = false;
                            break;
                        }
                        if ($this->isSplitPrinting()) {
                            if ($side === 'right') {
                                $this->disableSplitPrint();
                                $split_print_end = false;
                            } else if ($side === 'left' && $page_number !== 0) {
                                // This will never apply for the first page, and should only apply for the left hand side.
                                $this->disableSplitPrint();
                                $split_print_end = true;
                            }
                        }
                    }
                }
                ?>
                    <div class="fpten-prescription-list-filler fpten-form-row">
                        <?php for ($line = 0; $line < PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used; $line++) {
                            if ($line !== 0) {
                                echo '<br/>';
                            }
                            echo ($side === 'left') ? PrescriptionFormPrinter::LHS_LINE_FILLER_TEXT : PrescriptionFormPrinter::RHS_LINE_FILLER_TEXT;
                        }
                        if ($split_print_end && $side === 'left' && !$this->isSplitPrinting()) {
                            $this->setCurrentAttrStr($current_attr_copy);
                            $this->current_item_index = $current_item_copy;
                            $this->current_taper_index = $current_taper_copy;
                            $this->enableSplitPrint();
                            $split_print_end = false;
                        } ?>
                </div>
            </div>
        </div>
    </div>
<?php if ($side === 'left') : ?>
    <span class="fpten-form-column fpten-prescriber-code">HP</span>
<?php endif; ?>
</div>
