<?php
    /**
     * @var int $page_number
     * @var string $form_css_class
     */
    ?>
<?php if ($page_number !== 0) : ?>
    <p style="page-break-after: always;">
        <!--PAGE BREAK-->
    </p>
<?php endif; ?>
<div class="fpten-form-row">
    <?php foreach (array('left', 'right') as $side): ?>
    <div class="<?= $form_css_class ?>-container fpten-form-column">
        <?php
            $this->render('form_print_header', array(
                'form_css_class' => $form_css_class,
                'side' => $side
            ));
            $this->render('form_print_item_list', array(
                'form_css_class' => $form_css_class,
                'side' => $side,
                'page_number' => $page_number
            ));
            $this->render('form_print_footer', array(
                'form_css_class' => $form_css_class,
                'side' => $side,
            ));
        ?>
    </div>
    <?php endforeach; ?>
</div>
