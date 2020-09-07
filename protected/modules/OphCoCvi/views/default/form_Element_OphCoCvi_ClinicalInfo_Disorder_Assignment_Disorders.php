<header class="element-header">
    <h3 class="element-title">Disorders</h3>
</header>
<div class="element-fields js-collapse">
    <?php foreach ($this->getDisorderSections() as $disorder_section) {
        $is_open = $element->hasAffectedCviDisorderInSection($disorder_section);
        ?>
        <div class="collapse-group highlight" data-collapse="collapsed">
            <div class="collapse-group-icon">
                <i class="oe-i <?= $is_open ? 'minus' : 'plus' ?>"></i>
            </div>
            <h3 class="collapse-group-header">
                <?= $disorder_section->name; ?>
            </h3>
            <div class="collapse-group-content" style="<?= $is_open ? 'display:block' : 'display:none' ?>">
                <?php if ($disorder_section->disorders) { ?>
                    <div class="element-eyes">
                        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
                            <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>"
                                 data-side="<?= $eye_side ?>">
                                <div class="active-form">
                                    <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                                        'side' => $eye_side,
                                        'element' => $element,
                                        'form' => $form,
                                        'disorder_section' => $disorder_section,
                                    )) ?>

                                    <?php if ($disorder_section->comments_allowed == 1 && $eye_side === 'right') { ?>
                                        <table class="standard">
                                            <tbody>
                                            <tr>
                                                <td><?php echo $disorder_section->comments_label; ?></td>
                                                <td>
                                                    <div class="cols-full">
                                                        <?php
                                                        $section_comment = $element->getDisorderSectionComment($disorder_section);
                                                        $comments = $section_comment ? $section_comment->comments : null; ?>
                                                        <button id="disorders_comment_<?= $disorder_section->name; ?>_button"
                                                                class="button js-add-comments"
                                                                data-comment-container="#disorders_comment_<?= $disorder_section->name; ?>_container"
                                                                data-hide-method="display"
                                                                type="button"
                                                                style="display:<?php if ($comments) echo 'none' ?>">
                                                            <i class="oe-i comments small-icon"></i>
                                                        </button>

                                                        <div id="disorders_comment_<?= $disorder_section->name; ?>_container"
                                                             data-comment-button="#disorders_comment_<?= $disorder_section->name; ?>_button"
                                                             class="flex-layout flex-left js-comment-container"
                                                             style="<?php if ($comments == null) echo 'display:none' ?>">
                                                            <?php
                                                            echo CHtml::textArea(
                                                                CHtml::modelName($element) . "[cvi_disorder_section][" . $disorder_section->id . "][comments]",
                                                                $comments,
                                                                array('rows' => '1',
                                                                    'class' => 'js-input-comments cols-full autosize',
                                                                    'nowrapper' => true ,
                                                                'placeholder' => 'Comments')
                                                            ); ?>
                                                            <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

    <?php } ?>
</div>


<script>
    $(document).ready(function () {
        $('.js-collapse .collapse-group').each(function () {
            new Collapser($(this).find('.collapse-group-icon .oe-i'),
                $(this).find('.collapse-group-content'), $(this).find('.collapse-group-header'), $(this).data('collapse'));
        });

        function Collapser($icon, $content, $header, initialState) {
            var collapsed = initialState == 'collapsed';

            $icon.click(change);
            $header.click(function (e) {
                headerChange(e);
            });

            function change() {
                $icon.toggleClass('minus plus');
                collapsed = !collapsed;
                $content.toggle(!collapsed)
            }

            function headerChange(e) {
                if (collapsed) {
                    e.preventDefault();
                    $content.show();
                    $icon.toggleClass('minus plus');
                    collapsed = !collapsed;
                }
            }
        }
    });

</script>
