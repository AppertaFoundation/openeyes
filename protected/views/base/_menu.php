<li class="oe-nav-btn" id="js-nav-shortcuts">
  <a href="" class="nav-js-btn icon-btn" id="js-nav-shortcuts-btn" onclick="returnFalse()">
    <svg viewBox="0 0 80 40" class="icon shortcuts">
      <use xlink:href="<?= $navIconUrl . '#shortcuts-icon' ?>"></use>
    </svg>
  </a>
  <div class="oe-nav-shortcuts" id="js-nav-shortcuts-subnav">
    <ul>
        <?php foreach ($menu as $key => $item) { ?>
            <?php
            $selected = ($uri == $item['uri']) ? 'selected' : '';
            $hasSub = isset($item['sub']) && is_array($item['sub']);
            $subClass = $hasSub ? 'sub-menu-item' : '';
            $menuKey = 'menu-item-' . str_replace(' ', '-', strtolower($item['title']));
            ?>
          <li>
              <?php
                $link = $item['uri'];
                if ($item['uri'] !== '#' && strpos($item['uri'], ':') === false) {
                    $link = Yii::app()->getBaseUrl() . '/' . ltrim($item['uri'], '/');
                }
                $options = array();
                if (array_key_exists('options', $item)) {
                    $options = $item['options'];
                }
                echo CHtml::link($item['title'], $link, $options)
                ?>
              <?php if ($hasSub) : ?>
                <ul class="<?= $subClass ?>" id="<?= $menuKey ?>-sub" class="f-dropdown" data-dropdown-content>
                    <?php foreach ($item['sub'] as $subKey => $subItem) : ?>
                      <li>
                          <?php
                            $subOptions = array();
                            if (array_key_exists('options', $subItem)) {
                                $subOptions = $subItem['options'];
                            }
                            $subLink = ($subItem['uri'] !== '#' && strpos($subItem['uri'], ':') === false) ? Yii::app()->getBaseUrl() . '/' . ltrim($subItem['uri'], '/') : $subItem['uri'];
                            echo CHtml::link($subItem['title'], $subLink, $subOptions) ?>
                      </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
          </li>
        <?php } ?>
    </ul>
  </div>
</li>
