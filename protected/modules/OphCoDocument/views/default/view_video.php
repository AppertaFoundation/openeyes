<link href="<?php echo Yii::app()->createURL('/node_modules/video.js/dist/video-js.css') ?>" rel="stylesheet" />
<video
    controls
    autoplay
    id="my-video"
    class="video-js"
    preload="auto"
    controls
    data-setup='{"fluid": true, "playbackRates": [0.5, 1, 1.5, 2], "liveui": true}'
  >
    <source src="/file/view/<?php echo $element->{$index}->id?>/image<?php echo strrchr($element->{$index}->name, '.') ?>">
</video>
<script src="<?php echo Yii::app()->createURL('/node_modules/video.js/dist/video.js') ?>" ></script>
