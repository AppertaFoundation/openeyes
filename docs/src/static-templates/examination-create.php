<? include 'components/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<? include 'components/head.php'; ?>
<script src="<?php echo $assets_root_path;?>assets/modules/OphCiExamination/assets/js/module.js"></script>
<link rel="stylesheet" href="<?php echo $assets_root_path?>assets/modules/OphCiExamination/assets/css/module.css" />
<link rel="stylesheet" href="<?php echo $assets_root_path?>assets/modules/eyedraw/css/oe-eyedraw.css" />
</head>
<body class="open-eyes">
	<div class="container main" role="main">

		<? include 'components/header-logged-in.php'; ?>

		<div class="container content">
			<h1 class="badge">Episodes and events</h1>

			<div class="box content">
				<div class="row">
					<? include 'components/events/sidebar.php'; ?>
					<? include 'components/events/examination-create.php'; ?>
				</div>
			</div>
		</div>
		<? include 'components/footer.php'; ?>
		<script>
		OphCiExamination_DRGrading_init();
		function getSplitElementSide() {
			return 'right';
		}
		</script>
	</div>
</body>
</html>