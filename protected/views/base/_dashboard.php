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

  $('body').append('<div id="feature-help-overlay" hidden="true"></div>');
  let button_state = 0b0;
  const step_1_selc = ".large-6.medium-7.column";
  const step_1_title = "User Panel";
  const step_1_content = "This is where....";
  $(step_1_selc).attr('data-toggle','popover');
  $(step_1_selc).attr('title',step_1_title);
  $(step_1_selc).attr('data-content',step_1_content);
  $(step_1_selc).attr('data-trigger',"manual");


  const step_2_selc = ".oe-find-patient:first";
  const step_2_title = "Patient Search";
  const step_2_content = "This is where....";
  $(step_2_selc).attr('data-toggle','popover');
  $(step_2_selc).attr('title',step_2_title);
  $(step_2_selc).attr('data-content',step_2_content);
  $(step_2_selc).attr('data-trigger',"manual");

  const step_3_selc = "#"+$('section .box-title:contains(Messages):not(:contains(Sent Messages))').parent().prop('id');
  const step_3_title = "Messages";
  const step_3_content = "This is where....";


  //add popovers to elements
  $(step_1_selc);
  $(step_2_selc);
  $(step_3_selc);
  //show popovers
  //show download
  //show take a tour
  //show cancel

  // Instance the tour


var tour = new Tour({
  backdrop: true,
  steps: [
  {
    element: ".large-6.medium-7.column",
    backdropContainer: 'header',
    title: "User Info Panel",
    content: "This is where user info"
  },
  {
    element: ".oe-find-patient:first",
    title: "Paitent Search",
    content: "Search for patients here"
  },
  {
    element: step_3_selc,
    title: "Paitent Search",
    content: "Search for patients here"
  }
]});

// Initialize the tour
tour.init();

// Start the tour
$('#feature-help-button').click(function(){
  if (button_state = ~button_state) { //add different class?
    $("#feature-help-overlay").show();
    //think about header stack context cannot append to non static positioned elements think of solution maybe jsut append to body?
    $('[data-toggle="popover"]').popover('show');
  } else {
    $('[data-toggle="popover"]').popover('hide');
    $("#feature-help-overlay").hide();
  }

  //tour.restart(true);
});
});

</script>
