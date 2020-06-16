<div class="banner clearfix">
    <?php if (isset($logo['secondaryLogo'])) {?>
        <div class="letter-seal">
            <img src="<?php echo $logo['secondaryLogo'];?>" alt="letterhead_seal" width="80" />
        </div>
    <?php }
    if (isset($logo['primaryLogo'])) {
        ?>
        <div class="ophdrprescription-letter-logo">
            <img src="<?php  echo $logo['primaryLogo']; ?>" alt="letterhead_NHS" width="350" />
        </div>
    <?php } ?>
</div>