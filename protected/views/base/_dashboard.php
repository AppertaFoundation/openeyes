<div class="dashboard-container">
<?php foreach ($items as $item) { ?>
<section class="box dashboard js-toggle-container">
    <h3 class="box-title"><?= $item['title'] ?></h3>
        <span class="sortable-anchor fa fa-arrows"></span>
    <a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
    </a>
    <div class="js-toggle-body">
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