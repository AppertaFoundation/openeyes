<div class="box genetics panel">
    <h2>Menu</h2>
    <ul class="navigation">
        <?php $sidebarLinks = array(
            'Pedigrees' => Yii::app()->createUrl('/Genetics/pedigree/list'),
            'Genes' => Yii::app()->createUrl('/Genetics/gene/list'),
            'Subjects' => Yii::app()->createUrl('/Genetics/subject/list'),
        );
        foreach ($sidebarLinks as $title => $uri) { ?>
            <li<?php if (Yii::app()->getController()->action->id == $uri) { ?> class="selected"<?php } ?>>
                <?php if (Yii::app()->getController()->action->id == $uri) { ?>
                    <?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
                <?php } else { ?>
                    <?php echo CHtml::link($title, array($uri)) ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>
