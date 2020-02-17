<?php
if (!empty($logo['headerLogo'])) { ?>
<div class="letter-logo">
    <img src="<?php echo Yii::app()->assetManager->getPublishedUrl($logo['headerLogo'], true); ?>"
         alt="letterhead_logo" style="height:<?= $size ?>px"/>
</div>
    <?php
} if (!empty($logo['secondaryLogo'])) {?>
<div class="seal">
    <img src="<?php echo Yii::app()->assetManager->getPublishedUrl($logo['secondaryLogo'], true); ?>"
         alt="letterhead_seal" height="83" />
</div>
<?php }?>

