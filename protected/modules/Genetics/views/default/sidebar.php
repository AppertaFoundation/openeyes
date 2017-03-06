<div class="box genetics panel">
    <h2>Menu</h2>
    <ul class="navigation">
        <?php $sidebarLinks = array(
            'Patients' => Yii::app()->createUrl('/Genetics/subject/list'),
            'Families' => Yii::app()->createUrl('/Genetics/pedigree/list'),
            'Genetic test/result' => Yii::app()->createUrl('/OphInGeneticresults/search/geneticResults'),
            'Samples' => Yii::app()->createUrl('/OphInDnasample/search/DnaSample'),
            'Studies' => Yii::app()->createUrl('/Genetics/study/list'),
            'Genes' => Yii::app()->createUrl('/Genetics/gene/list'),
        );

        $current_uri = '/' . $this->module->id . '/' . $this->id . '/' . $this->action->id;

        foreach ($sidebarLinks as $title => $uri) { ?>
            <li<?php if ($current_uri == $uri) { ?> class="selected"<?php } ?>>
                <?php if ($current_uri == $uri) { ?>
                    <?php echo CHtml::link($title, array($uri), array('class' => 'selected')) ?>
                <?php } else { ?>
                    <?php echo CHtml::link($title, array($uri)) ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>
