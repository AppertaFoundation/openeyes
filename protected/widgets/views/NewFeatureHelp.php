<div class="help-trigger-btn">
</div>
<div class="help-popup">
  <span class="help-close"></span>
  <span class="help-title">Help</span>
    <ul class="help-actions">
    <?php if ($this->splash_screen) { ?>
    <li id="help-splash-screen-btn" class="help-action">
      Show Splash Screen
    </li>
    <?php } ?>
    <?php foreach ($this->tours as $key => $value) { ?>
      <li id="help-tour-name-<?= $value['id'] ? : $key; ?>" class="help-action help-action-tour"><?=$value['name']?></li>
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
<script>
    $(document).ready(function() {
        $('header:first').append('<div id="help-header-overlay" hidden="true"></div>');
        new NewFeatureHelpController(
            <?= json_encode($this->splash_screen) ?>,
            <?= json_encode($this->tours) ?>,
            null,
            {autoStart: <?=($this->auto_start === true ? 'true' : 'false');?>}
        );
    });
</script>
