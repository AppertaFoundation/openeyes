<?php
if (!empty($logo['headerLogo'])) { ?>
    
<div class="letter-logo">
        <img src="<?php echo $logo['headerLogo']; ?>" alt="letterhead_logo" style="height:<?= $size ?>px" />
    </div>
<?php 
} if (!empty($logo['secondaryLogo'])) {?>
<div class="seal">
        <img src="<?php echo $logo['secondaryLogo'];
    ?>" alt="letterhead_seal" height="83" />
    </div>
<?php }?>

