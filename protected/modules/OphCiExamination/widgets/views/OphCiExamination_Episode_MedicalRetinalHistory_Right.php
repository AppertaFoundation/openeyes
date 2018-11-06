<div class="oes-stack-select">
    <button id="js-oct-btn" class="selected">OCT</button>
    <button id="js-ffa-btn">FFA</button>
    <button id="js-img-btn">Image</button>
</div>
<!-- image stacks for OCT, FFA and Photos, all loaded in DOM to avoid load delay. JS handles the image stacks generically using the childNodes length  -->
<div id="oct-stack" class="oes-image-stack">
    <!-- OCT image stack, 1st is shown by default -->
    <?php
    $doc_list = $this->getDocument();
    foreach (['left','right'] as $side) { ?>
        <div id="oct_stack_<?= $side ?>" class="stack-<?= $side ?>" style="display: <?= $side=='left'?'none': ''?>">
            <?php foreach ($doc_list[$side] as $k=>$doc) {?>
                <img
                    id="oct_img_<?=$side.'_'.$doc['doc_id'] ?>"
                    class="oct-img"
                    src="/file/view/<?= $doc['doc_id'] ?>/image<?= strrchr($doc['doc_name'], '.') ?>"
                    style="display: <?= $k==0&&$side=='right'? '': 'none'?> ;">
            <?php } ?>
        </div>
    <?php } ?>
</div>
<div id="ffa-stack" class="oes-image-stack" style="display: none;"></div>
<div id="img-stack" class="oes-image-stack" style="display: none;"></div>