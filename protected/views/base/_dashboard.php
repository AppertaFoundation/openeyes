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

<div id="help-container" style="z-index: 100000;">
  <div id="help-trigger">
  </div>
  <div id="help-overlay" class="action">
    Show Help Overlay
  </div>
  <div id="help-tour" class="action">
    Take a Tour
  </div>
  <div id="help-download" class="action">
    Download a PDF
  </div>
</div>


<script>
$(document).ready(function(){

  function Steps(){ //init tour
    this._addListeners();
    this._addOverlays();
    this.tour = new Tour({backdrop: true});
    this.elements = []; //for z-index
    this.contentType = []; //selector short long content
  }
  Steps.prototype._addListeners = function () {
    let self = this;
    $('#help-trigger').click(function(){
      self._helpTrigger($(this).parent());
    });
    $('#help-overlay').click(function(){
      self._toggleOverlay($(this));
    });
    $('#help-tour').click(function(){
      self._showLongContent();
      self.tour.init(); // TODO: see why cannot who all first
      self.tour.restart(); //sometimes invalid could keep steps array and pass it in so create tour each time and remove all attributes first
    });
  }
  Steps.prototype._addOverlays = function () {
    this.header_overlay = $('body').append('<div id="help-body-overlay" hidden="true"></div>');
    this.body_overlay = $('header').append('<div id="help-header-overlay" hidden="true"></div>');
    this.overlay_toggle = 0b0;

  }
  Steps.prototype._helpTrigger = function ($this) {
    if ($this.hasClass('help-active')) {
      $this.removeClass('help-active');
    }
    else {
      $this.addClass('help-active');
    }
    if (this.overlay_toggle) {
      this._toggleOverlay($('#help-overlay'));
    }
  }
  Steps.prototype._addStep = function (params) {
    this.tour.addStep(params);
    const $this = $(params.element);
    $this.attr('data-toggle','popover');
    $this.attr('title',params.title);
    $this.attr('data-content',params.contentLess);
    $this.attr('data-trigger',"manual");
    $this.attr('data-placement',params.placement ? params.placement : "auto right");
    if (params.showParent) {
     this.elements.push($this.parent());
   } else {
     this.elements.push($this);
   }
   this.contentType.push({element: params.element, shortContent: params.contentLess, longContent: params.content});
  };
  Steps.prototype.addSteps = function (params) {
    params.forEach((el) => {
      this._addStep(el);
    });
  }
  Steps.prototype._showShortContent = function () {
    this.contentType.forEach((el) => {
      $(el.element).attr('data-content',el.shortContent);
    });
  }
  Steps.prototype._showLongContent = function () {
    this.contentType.forEach((el) => {
      $(el.element).attr('data-content',el.longContent);
    });
  }
  Steps.prototype._toggleOverlay = function ($this) {
    if (this.overlay_toggle = ~this.overlay_toggle) {
      this._showShortContent();
      window.scrollTo(0,0);
      $this.html("Hide Help Overlay");
      this.elements.forEach(function(elem){
        elem.css('z-index', 160);
      });
      $("#help-body-overlay").show();
      $("#help-header-overlay").show();
      $('[data-toggle="popover"]').popover('show');
    } else {
      $this.html("Show Help Overlay");
      this.elements.forEach(function(elem){
        elem.css('z-index', 'auto');
      });
      $('[data-toggle="popover"]').popover('hide');
      $("#help-body-overlay").hide();
      $("#help-header-overlay").hide();
    }
  }

  const steps = new Steps();
  steps.addSteps([{
                  element: ".large-6.medium-7.column",
                  title: "User Panel",
                  content: "This is where....",
                  contentLess: "This is....",
                  backdropContainer: "header"
                 },
                 {
                  element: ".oe-find-patient:first",
                  title: "Paitent Search",
                  content: "This is where....",
                  contentLess: "This is....",
                  showParent: "true"
                 },
                 {
                  element: "#"+$('section .box-title:contains(Messages):not(:contains(Sent Messages))').parent().prop('id'),
                  title: "Messages",
                  content: "This is where....",
                  contentLess: "This is....",
                  placement: "bottom"
                }]);
});

</script>
