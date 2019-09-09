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
            <div class="fpten-form-row fpten-prescriber">
                HOSPITAL PRESCRIBER
            </div>
        <?php endif; ?>
        <div class="fpten-form-row">
            <div class="fpten-form-column <?= $form_css_class ?>-prescription-list">
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
                        // If the lines used is greater than the maximum, split the printout between multiple pages, starting on its own page.
                        if ($item->fpTenLinesUsed() + 1 > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used
                            && !$this->isSplitPrinting()) {
                            if ($item->fpTenLinesUsed() + 1 > PrescriptionFormPrinter::MAX_FPTEN_LINES) {
                                // Single item will not fit on a single script.
                                $this->enableSplitPrint();
                            }

                            if ($side === 'right') {
                                // We only want to change the page counter and the base item index once the right side of the page has been rendered.
                                // Otherwise the right side will not print correctly.
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
                        foreach (array('drug', 'dose', 'frequency') as $attr) {
                            if ($item->getAttrLength("item_$attr") + 1 > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->getCurrentItemAttr()) {
                                    $this->current_item_index = $j;
                                    $this->current_taper_index = 0;
                                    $this->setCurrentAttr($attr);
                                }
                                $end_of_page = true;
                                break;
                            }
                            if (!$this->getCurrentItemAttr() || $this->getCurrentItemAttr() === "item_$attr") {
                                switch ($attr) {
                                    case 'drug':
                                        echo wordwrap($drug_label, $this->getLineLength(), '<br/>') . '<br/>';
                                        break;
                                    case 'dose':
                                        echo wordwrap($item->fpTenDose(), $this->getLineLength(), '<br/>') . '<br/>';
                                        break;
                                    case 'frequency':
                                        echo wordwrap($item->fpTenFrequency(), $this->getLineLength(), '<br/>');
                                        break;
                                }
                                $this->setCurrentAttr();
                                $current_item_index = 0;
                                $current_taper_index = 0;
                                $prescription_lines_used += $item->getAttrLength("item_$attr");
                            }
                        }
                        for ($index = $this->current_taper_index; $index < $total_tapers; $index++) {
                            $taper = $item->tapers[$index];
                            foreach (array('label', 'dose', 'frequency') as $attr) {
                                $lines_remaining = PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used;
                                if ($item->getAttrLength("taper{$index}_$attr") + 1 > $lines_remaining) {
                                    if ($side === 'right' && !$this->getCurrentItemAttr()) {
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
                                            echo wordwrap($taper->fpTenDose(), $this->getLineLength(), '<br/>') . '<br/>';
                                            break;
                                        case 'frequency':
                                            echo wordwrap($taper->fpTenFrequency(), $this->getLineLength(), '<br/>');
                                            break;
                                    }

                                    $this->setCurrentAttr();
                                    $current_item_index = 0;
                                    $current_taper_index = 0;
                                    $prescription_lines_used += $item->getAttrLength("taper{$index}_$attr");
                                }
                            }
                            if ($end_of_page) {
                                break;
                            }
                        }
                        if (!$end_of_page) {
                            if ($item->getAttrLength('item_comment') > PrescriptionFormPrinter::MAX_FPTEN_LINES - $prescription_lines_used) {
                                if ($side === 'right' && !$this->getCurrentItemAttr()) {
                                    $this->current_item_index = $j;
                                    $this->current_taper_index = 0;
                                    $this->setCurrentAttr('comment');
                                }
                                $end_of_page = true;
                            }
                            if ((!$this->getCurrentItemAttr() || $this->getCurrentItemAttr() === 'item_comment') && $item->comments) {
                                echo '<br/>' . wordwrap("Comment: $item->comments", $this->getLineLength(), '<br/>');
                                $this->setCurrentAttr();
                                $this->current_item_index = 0;
                                $this->current_taper_index = 0;
                                $prescription_lines_used += $item->getAttrLength('item_comment');
                            }
                        }

                        $prescription_lines_used++; // Add 1 line for the horizontal rule.
                        ?>
                        </div>
                        <?php
                    }
                    if ($this->isSplitPrinting()) {
                        if ($side === 'left' && $page_number > 0) {
                            // This will never apply for the first page, and should only apply for the left hand side.
                            $this->disableSplitPrint();
                            $split_print_end = true;
                        } elseif ($side === 'right') {
                            $this->disableSplitPrint();
                            $split_print_end = false;
                        }
                    }

                    if ($end_of_page) {
                        $end_of_page = false;
                        break;
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
                        if ($split_print_end && $side === 'left') {
                            $this->setCurrentAttrStr($current_attr_copy);
                            $this->current_item_index = $current_item_copy;
                            $this->current_taper_index = $current_taper_copy;
                            $current_item_copy = 0;
                            $current_attr_copy = null;
                            $current_taper_copy = 0;
                        }
                        if (!$this->isSplitPrinting()) {
                            // Only run this code if the item is a split-print item.
                            $this->enableSplitPrint();
                            $split_print_end = false;
                        } ?>
                </div>
                <p class="fpten-form-row fpten-page-counter">
                    <?php
                    echo "Page {$this->getPageNumber()} of {$this->getTotalPages()}";
                    if ($side === 'right') {
                        $this->addPages();
                    }?>
                </p>
            </div>
        </div>
    </div>
<?php if ($side === 'left') : ?>
    <span class="fpten-form-column fpten-prescriber-code">HP</span>
<?php endif; ?>
</div>
