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
  function Steps() {
    this.elements = [];
    this.elementsContent = [];
    this.toggles = {overlay: 0b0, tour: 0b0};
    this.buttons = {help: $('#help-trigger'), menu: $('#help-trigger').parent(),overlay: $('#help-overlay'), tour: $('#help-tour')};
    this._addListeners();
    this._addOverlays();
    this.tour = new Tour({backdrop: true, onEnd: this._tourEnd.bind(this), onStart: this._tourStart.bind(this)});
  }
  Steps.prototype._tourStart = function(tour) {
    this.buttons.tour.html('End Tour');
  }
  Steps.prototype._tourEnd = function(tour) {
    this.buttons.tour.html('Take a Tour');
    this.toggles.tour = 0b0;
  }
  Steps.prototype._addListeners = function () {
    this.buttons.help.click(() => {this.toggleHelpMenu();});
    this.buttons.overlay.click(() => {this.toggleOverlay(true);});
    this.buttons.tour.click(() => {this.toggleTour(true);});
  }
  Steps.prototype.toggleTour = function (force) {
    if (!force && this.toggles.overlay) {
      return;
    } else if (this.toggles.overlay) {
      this.toggleOverlay();
    }
    if (this.toggles.tour = ~this.toggles.tour) {
      this._startTour();
    } else {
      this._endTour();
    }
  }
  Steps.prototype._startTour = function () {
    this._showLongContent();
    localStorage.clear();
    this.tour.start(true);
    this.tour.next();
    this.tour.next();
    this.tour.next();
    this.tour.next();
    this.tour.goTo(0);
  }
  Steps.prototype._endTour = function () {
    this.tour.end();
    $('.popover').remove();
  }
  Steps.prototype._addOverlays = function () {
    $('body').append('<div id="help-body-overlay" hidden="true"></div>');
    $('header').append('<div id="help-header-overlay" hidden="true"></div>');
    this.header_overlay = $('#help-body-overlay');
    this.body_overlay = $('#help-header-overlay');
  }
  Steps.prototype.toggleHelpMenu = function () {
    if (this.buttons.menu.hasClass('help-active')) {
      this.buttons.menu.removeClass('help-active');
    }
    else {
      this.buttons.menu.addClass('help-active');
    }
    if (this.toggles.overlay) {
      this.toggleOverlay(true);
    }
    if (this.toggles.tour) {
      this.toggleTour(true);
    }
  }
  Steps.prototype.addStep = function (params) {
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
    this.elementsContent.push({element: params.element, shortContent: params.contentLess, longContent: params.content});
  };
  Steps.prototype.addSteps = function (params) {
    params.forEach((el) => {
      this.addStep(el);
    });
  }
  Steps.prototype._showShortContent = function () {
    this.elementsContent.forEach((el) => {
      $(el.element).attr('data-content',el.shortContent);
    });
  }
  Steps.prototype._showLongContent = function () {
    this.elementsContent.forEach((el) => {
      $(el.element).attr('data-content',el.longContent);
    });
  }
  Steps.prototype.toggleOverlay = function (force) {
    if (!force && this.toggles.tour) {
      return;
    } else if (this.toggles.tour) {
      this.toggleTour();
    }
    if (this.toggles.overlay = ~this.toggles.overlay) {
      this._showShortContent();
      window.scrollTo(0,0);
      this.buttons.overlay.html("Hide Help Overlay");
      this.elements.forEach(function(elem){
        elem.css('z-index', 160);
      });
      this.header_overlay.show();
      this.body_overlay.show();
      $('[data-toggle="popover"]').popover('show');
    } else {
      this.buttons.overlay.html("Show Help Overlay");
      this.elements.forEach(function(elem){
        elem.css('z-index', '');
      });
      $('[data-toggle="popover"]').popover('hide');
      this.header_overlay.hide();
      this.body_overlay.hide();
    }
  }
$(document).ready(function(){
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
