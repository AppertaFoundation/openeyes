<?php
if (isset($logo['primaryLogo'])) {
    ?>
    <img src="<?php echo $logo['primaryLogo']; ?>" width="auto" height="100px"/>
<?php } ?>
<?php if (isset($logo['secondaryLogo'])) {?>
    <div><img src="<?php echo $logo['secondaryLogo']?>" width="160px" height="auto"/></div>
<?php }