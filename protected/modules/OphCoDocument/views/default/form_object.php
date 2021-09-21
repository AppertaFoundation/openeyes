<?php if ($element->{$index}->mimetype !== 'application/pdf') { ?>
    <div id="ophco-image-container-'+sideID+'" class="ophco-image-container">
        <object width="100%" height="500px" data="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr($element->{$index}->name, '.') ?>" type="application/pdf">
            <embed src="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr($element->{$index}->name, '.') ?>" type="application/pdf" />
        </object>
    </div>
<?php } else { ?>
    <?php if ($element->single_document_id || $element->hasSidedAttributesSet("OR")) { ?>
        <script>
            $(document).ready(function () {
                let controller = $('.js-document-upload-wrapper').data('controller');
                const side = '<?= $side?>';
                let value = '<?= $element->{$index}->id; ?>';
                const fileName = '<?= $element->{$index}->name; ?>';
                const extension = fileName.substr(fileName.lastIndexOf('.') + 1);

                const $td = $(document.querySelector(`.js-document-upload-wrapper td[data-side=${side}]`));
                let $div = document.createElement('div');
                $div.id = 'ophco-image-container-' + value
                $div.classList.add('ophco-image-container');
                $div.setAttribute('data-file-format', 'jpeg');

                controller.generatePDF($td, $div, value, extension, side);
            });
        </script>
    <?php } ?>

<?php } ?>
