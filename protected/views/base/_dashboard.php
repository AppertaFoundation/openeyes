<?php foreach ($items as $item) { ?>
<section class="box dashboard js-toggle-container">
    <h3 class="box-title"><?= $item['title'] ?></h3>
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
