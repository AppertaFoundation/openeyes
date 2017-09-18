<div class="dashboard-container" id="tour2">
<?php foreach ($items as $box_number => $item) {?>

<?php
    $container_id = isset($item['options']['container-id']) ? $item['options']['container-id'] : "js-toggle-container-$box_number";
    $is_open = isset($item['options']['js-toggle-open']) && $item['options']['js-toggle-open'];
    ?>

<section id="<?php echo $container_id; ?>" class="box dashboard js-toggle-container">
    <h3 class="box-title"><?= $item['title'] ?></h3>
    <?php if ($sortable) { ?><span class="sortable-anchor fa fa-arrows"></span><?php }?>
    <a href="#" class="toggle-trigger <?php echo  $is_open ? 'toggle-hide' : 'toggle-show' ?> js-toggle">
        <span class="icon-showhide">
            Show/hide this section
        </span>
    </a>
    <div class="js-toggle-body" style="<?php echo  $is_open ? 'display:block' : 'display:none' ?>">
    <?= $item['content']; ?>
    </div>
</section>
<?php } ?>
</div>
<?php if ($sortable) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.dashboard-container').sortable({handle: '.sortable-anchor'});
    });
</script>
<?php }?>


<script>
$(document).ready(function(){
  // Instance the tour


var tour = new Tour({
  backdrop: true,
  steps: [
  {
    element: ".oe-user-info:first",
    backdropContainer: 'header',
    title: "User Info Panel",
    content: "This is where user info"
  },
  {
    element: ".oe-user-home:first",
    backdropContainer: 'header',
    title: "Home button",
    content: "This button takes you to the homepage"
  },
  {
    element: ".oe-user-navigation:first",
    backdropContainer: 'header',
    title: "Navigation button",
    content: "Use this to ...."
  },
  {
    element: ".oe-user-logout:first",
    backdropContainer: 'header',
    title: "Logout button",
    content: "This where ...."
  },
  {
    element: ".oe-find-patient:first",
    title: "Paitent Search",
    content: "Search for patients here"
  },
  {
    element: "#"+getMessage(),
    title: "Paitent Search",
    content: "Search for patients here"
  }
]});

function getMessage(){
  return $('section .box-title:contains(Messages):not(:contains(Sent Messages))').parent().prop('id');
}

// Initialize the tour
tour.init();

// Start the tour
tour.restart(true);
});

</script>
