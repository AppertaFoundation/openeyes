<?php echo "<?php\n"; ?>

class DefaultController extends BaseEventTypeController
{
	public function actionIndex()
	{
		$this->render('index');
	}
}
