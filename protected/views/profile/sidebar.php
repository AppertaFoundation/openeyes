    <h3>Your Profile</h3>
    <ul>
        <?php
        $links = array();
        if (Yii::app()->params['profile_user_show_menu']) {
            $links['Basic information'] = '/profile/info';
            $links['Sites'] = '/profile/sites';
            $links['Institutions'] = '/profile/institutions';
            $links[Firm::contextLabel() . 's'] = '/profile/firms';
            $links['User settings'] = '/profile/usersettings';
        }
        if (Yii::app()->params['profile_user_can_change_password']) {
            $links['Change password'] = '/profile/password';
        }
        foreach (array_merge($links, array(
            'Signature' => '/profile/signature',
        )) as $title => $uri) {?>
            <li<?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) {
                ?> class="active"<?php
               }?>>
                <?php if (Yii::app()->getController()->action->id == preg_replace('/^\/admin\//', '', $uri)) {?>
                    <span class="viewing"><?php echo $title?></span>
                <?php } else {?>
                    <?=\CHtml::link($title, array($uri))?>
                <?php }?>
            </li>
        <?php }?>
    </ul>
