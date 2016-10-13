<style type='text/css'>
table { border:1px solid black; cell-spacing:0; cell-padding:0; }
td { border:1px solid black; border-collapse: 1; vertical-align:top; }
tr { background-color: #eee; color: black; padding:2px 5px; }
select.addr { width:200px !important; max-width:200px; }
div#docman_block select.macro { max-width:220px; }
table.docman tbody tr td img { vertical-align: text-top; height:13px; width:13px; }
table.docman > tbody > tr > td:first-child { width:200px; max-width:200px; }
button.docman { width:80px; background: none; font-size:13px; line-height:20px; height:20px; margin:5px 0; padding:0; text-align:center; }
button.red { background-color:red; color: white; }
button.green { background-color:green; color: white; }
</style>
<script type="text/javascript" src="<?= Yii::app()->assetManager->createUrl('js/docman.js')?>"></script>
<script>
function docman_add_new()
{
	// insert new edit row after the dm_table last TR
	var lastrow = $('#dm_table tr:last');
	docman2.createNewEntry(lastrow);
}

$(document).ready(function()
{
	docman2 = docman;
	docman2.baseUrl = location.protocol + '//' + location.host + '/docman/'; // TODO add this to the config!
	docman2.setDOMid('docman_block','dm_');
	macro_id = 0;
	event_id = 0;

	<?php
		if($module == 'Correspondence')
		{?>
			docman2.module_correspondence = 1;
	<?php
		}
		if(isset($data['macro']))
		{?>
			macro_id =  <?php echo $data['macro']?>;
	<?php
		}
		if(Yii::app()->request->getQuery('id'))
		{?>
			event_id = <?php echo Yii::app()->request->getQuery('id')?>;
	<?php
		}else if(isset($data["event_id"]))
		{?>
			event_id = <?php echo $data["event_id"]?>;
	<?php
		}
	?>

	docman2.getDocTable(event_id, macro_id);
});

</script>

<div id='docman_block'>
</div>
