<div class="dashboard-container" id="tour2">
  <?php foreach ($items as $box_number => $item) {?>
        <?php
        $container_id = isset($item['options']['container-id']) ? $item['options']['container-id'] : "js-toggle-container-$box_number";
        $is_open = isset($item['options']['js-toggle-open']) && $item['options']['js-toggle-open'];
        ?>

        <?= $item['content']; ?>
    <?php } ?>
<?php if ($sortable) { ?>
  <script type="text/javascript">
  $(document).ready(function() {
    $('.dashboard-container').sortable({handle: '.sortable-anchor'});
  });
  </script>

<?php }