<li class="oe-nav-btn" id="js-nav-shortcuts" data-test="oe-menu">
        <a class="nav-js-btn icon-btn" id="js-nav-shortcuts-btn">
            <svg viewBox="0 0 80 40" class="icon shortcuts">
                <use xlink:href="<?= $navIconUrl . '#shortcuts-icon' ?>"></use>
            </svg>
        </a>
    <div class="oe-nav-shortcuts" id="js-nav-shortcuts-subnav">
        <ul>
            <?php foreach ($menu as $key => $item) { ?>
                <?php
                $selected = ($uri == $item['uri']) ? 'selected' : '';
                $has_sub = isset($item['sub']) && is_array($item['sub']);
                $sub_class = $has_sub ? 'sub-menu-item' : '';
                $menu_key = 'menu-item-' . str_replace(' ', '-', strtolower($item['title']));
                $options = [
                    "data-test" => $item["title"]
                ];
                if (array_key_exists('options', $item)) {
                    $options = $item['options'];
                }
                // skip over the item if it requires a patient and we are not in a patient context
                if ((!isset($options['requires_patient'])) || ($options['requires_patient'] == true && isset($this->patient))) :
                    ?>
                    <li>
                        <?php
                        $link = $item['uri'];

                        if (!$link) {
                            $param = $item['requires_setting']['setting_key'];
                            $base_url = \SettingMetadata::model()->getSetting($param);

                            switch ($param) {
                                case 'imagenet_url':
                                    $patient_identifier = null;
                                    if (isset($this->patient)) {
                                        $patient_identifier = PatientIdentifier::model()->find(
                                            'patient_id=:patient_id AND patient_identifier_type_id=:patient_identifier_type_id',
                                            [':patient_id' => $this->patient->id,
                                                ':patient_identifier_type_id' => \SettingMetadata::model()->getSetting('imagenet_patient_identifier_type')]
                                        );
                                    }
                                    $link = $patient_identifier ? $base_url . 'IMAGEnet/?patientID=' . $patient_identifier->value . '&lastName=' . $this->patient->last_name . '&firstName=' . $this->patient->first_name : $base_url;
                                    break;
                                case 'hie_remote_url':
                                    if (isset($this->patient)) {
                                        $link = Yii::app()->createUrl('/patient/getHieSource/' . $this->patient->id);
                                    }
                                    break;
                                case 'cito_access_token_url':
                                    Yii::app()->clientScript->registerScript('gii.model',"
                                        $('#js-get-cito-url').click(function(e) {
                                            e.preventDefault();
                                            $.ajax({
                                                'type': 'GET',
                                                'url': baseUrl+'/Patient/getCitoUrl',
                                                'data': $.param({hos_num: OE_patient_hosnum})+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
                                                'success': function(data) {
                                                    if (data.success) {
                                                        window.open(data.url, 'newwindow', 'width=1200,height=800');
                                                    } else {
                                                        new OpenEyes.UI.Dialog.Alert({
                                                            content: data.message
                                                        }).open();
                                                    }
                                                }
                                            });
                                        });
                                    ");
                            }
                        } elseif ($item['uri'] !== '#' && strpos($item['uri'], ':') === false) {
                            $link = Yii::app()->createURL('site/index') . ltrim($item['uri'], '/');
                        }

                        if ($item['title'] === 'Track patients in FORUM' && Yii::app()->user->getState('forum_enabled') === 'on') {
                            $item['title'] = $item['alt_title'];
                        }

                        if ($item['title'] === 'Track patients in IMAGEnet' && Yii::app()->user->getState('imagenet_enabled') === 'on') {
                            $item['title'] = $item['alt_title'];
                        }

                        echo CHtml::link($item['title'], $link, $options)
                        ?>
                        <?php if ($has_sub) : ?>
                            <ul class="<?= $sub_class ?>" id="<?= $menu_key ?>-sub" class="f-dropdown" data-dropdown-content>
                                <?php foreach ($item['sub'] as $sub_key => $sub_item) : ?>
                                    <li>
                                        <?php
                                        $sub_options = array();
                                        if (array_key_exists('options', $sub_item)) {
                                            $sub_options = $sub_item['options'];
                                        }
                                        $sub_link = ($sub_item['uri'] !== '#' && strpos($sub_item['uri'], ':') === false) ? Yii::app()->createURL('site/index') . ltrim($sub_item['uri'], '/') : $sub_item['uri'];
                                        echo CHtml::link($sub_item['title'], $sub_link, $sub_options) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php } ?>
        </ul>
    </div>
</li>
