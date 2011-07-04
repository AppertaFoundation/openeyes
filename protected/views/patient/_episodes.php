<h3>Episodes</h3>
<div id="episodes_sidebar">
<?php
	$this->renderPartial('/clinical/_episodeList', 
		array('episodes' => $episodes)
	); ?>
</div>
<div id="episodes_details">
	<?php 
	foreach ($episodes as $episode) {
		$this->renderPartial('/clinical/episodeSummary', 
			array('episode' => $episode)
		);
	} ?>
</div>