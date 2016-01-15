<div class="dashboard-container">
<?php foreach ($items as $box_number => $item) { ?>
<section id="js-toggle-container-<?php echo $box_number; ?>" class="box dashboard js-toggle-container">
    <h3 class="box-title"><?= $item['title'] ?></h3>
    <span class="sortable-anchor fa fa-arrows"></span>
    <?php $isOpen = isset($item['options']['js-toggle-open']) && $item['options']['js-toggle-open']; ?>
    
    <a href="#" class="toggle-trigger <?php echo ( $isOpen ? 'toggle-hide' : 'toggle-show') ?> js-toggle">
        <span class="icon-showhide">
            Show/hide this section
        </span>
    </a>
    <div class="js-toggle-body" style="<?php echo ( $isOpen ? 'display:block' : 'display:none') ?>">
    <?= $item['content']; ?>
    </div>
</section>
<?php } ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.dashboard-container').sortable({handle: '.sortable-anchor'});
    })
</script>