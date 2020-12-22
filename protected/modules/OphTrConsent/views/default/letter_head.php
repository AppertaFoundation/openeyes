<?php if (isset($logo['secondaryLogo'])) {?>
<div class="seal">
    <img src="<?php echo $logo['secondaryLogo']?>" alt="letterhead_seal" />
</div>
<?php }
if (isset($logo['primaryLogo'])) {
    ?>
<div class="logo">
    <img src="<?php echo $logo['primaryLogo']; ?>" alt="logo" style="height:<?= $size ?>px" />
</div>
<?php } ?>
