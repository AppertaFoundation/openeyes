<div class="help-trigger-btn">
</div>
<div class="help-popup">
  <span class="help-close"></span>
  <span class="help-title">Help</span>
    <ul class="help-actions">
    <li id="help-splash-screen-btn" class="help-action">
      Show Splash Screen
    </li>
    <?php foreach ($tours as $key => $value) { ?>
      <li id="help-tour-name-<?=$key?>" class="help-action help-action-tour">
        Start <?=$key?>
      </li>
    <?php } ?>
    <?php foreach ($download_links as $key => $value) { ?>
      <a href="<?=$value?>" download="<?=$value?>.pdf">
      <li id="help-<?=$key?>" class="help-action">
        Download <?=$key?>
      </li>
      </a>
    <?php } ?>
  </ul>
</div>

<div id="help-body-overlay" hidden="true"></div>
<script>$('header:first').append('<div id="help-header-overlay" hidden="true"></div>')</script>
