<div class="box search panel">
    <h2>Core</h2>
    <ul class="navigation search">
    </ul>
</div>
<?php foreach (Yii::app()->params['advanced_search'] as $module => $pages) {
    if ($et = EventType::model()->find('class_name=?', array($module))) {
        $name = $et->name;
    } else {
        $name = $module;
    } ?>
    <div class="box search panel">
        <h2><?php echo $name ?></h2>
        <ul class="navigation search">
            <?php foreach ($pages as $title => $uri) {
                $class = '';
                if(Yii::app()->getController()->action->id ==  $uri){
                    $class = 'selected';
                }?>
                <li class="<?=$class?>">
                    <?=\CHtml::link($title, Yii::app()->createUrl('/' . $module . '/search/' . $uri), array('class' => $class)) ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
