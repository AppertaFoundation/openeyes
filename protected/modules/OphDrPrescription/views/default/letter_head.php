<div class="banner clearfix">
    <?php if(isset($logo['secondaryLogo'])){?>
        <div class="letter-seal">
            <img src="<?php echo $logo['secondaryLogo'];?>" alt="letterhead_seal" width="80" />
        </div>
    <?php }
    if(isset($logo['headerLogo'])){
    ?>
        <div class="letter-logo">
            <img src="<?php  echo $logo['headerLogo']; ?>" alt="letterhead_Moorfields_NHS" width="350" />
        </div>
    <?php } ?>
</div>