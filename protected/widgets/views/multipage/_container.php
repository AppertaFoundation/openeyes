<?php
/**
 * @var $total_pages int
 */
?>
<<?= $this->element?>
<?= $this->container_class ? "id=\"{$this->container_class}\"" : null ?>
<?= $this->container_id ? "id=\"{$this->container_id}\"" : null ?>>
    <?php
        $this->render('multipage/_nav', array(
            'total_pages' => $total_pages
        ));
        $this->render('multipage/_stack');
        ?>
</<?= $this->element?>>
