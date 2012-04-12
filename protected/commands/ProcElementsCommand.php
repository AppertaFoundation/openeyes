<?php
class ProcElementsCommand extends CConsoleCommand
{
	public function getName()
	{
		return '';
	}

	public function getHelp()
	{
		return '';
	}

	public function run($args)
	{
		$exclude = array(188,168,171,187,186,42,308,135,148,318,45,46,85,86,87,88,106,173,322,323,1);

		$tables = array();
		$lowers = array();
		$segments = array();

		foreach (Procedure::model()->findAll() as $proc) {
			if (!in_array($proc->id,$exclude)) {
				echo $proc->term." ... ";

				$lower = 'opnote_'.$this->get_table_name($proc->short_format);

				if (in_array($lower, $lowers)) {
					$lower .= '2';
				}

				$lowers[] = $lower;

				$segment = '';
				$ex = explode('_',$lower);
				foreach ($ex as $el) {
					if (isset($el[0])) {
						$segment .= $el[0];
					}
				}

				$segment_prefix = $segment;
				$x = 1;

				while (in_array($segment, $segments)) {
					$x++;
					$segment = $segment_prefix.$x;
				}

				$segments[] = $segment;

				$res = `echo 'yes' | ./yiic migrate --migrationPath=application.modules.OphTrOperationnote.migrations create $lower`;

				preg_match('/Create new migration \'(.*?)\'/',$res,$m);

				$migration_target_file = $m[1];

				$ex = explode('/',$m[1]);
				$migration_class = preg_replace('/\.php$/','',array_pop($ex));

				$table_name = 'et_ophtroperationnote_'.$this->get_table_name($proc->short_format);

				if (in_array($table_name,$tables)) {
					$table_name .= '2';
				}

				$tables[] = $table_name;

				$element_classname = 'Element'.$this->get_element_classname($proc->term);

				$migration = file_get_contents("/tmp/migration_stub");

				$migration = str_replace('{{CLASSNAME}}',$migration_class,$migration);
				$migration = str_replace('{{TABLENAME}}',$table_name,$migration);
				$migration = str_replace('{{TERM}}',$proc->term,$migration);
				$migration = str_replace('{{ELEMENT_NAME}}',$element_classname,$migration);
				$migration = str_replace('{{SEGMENT}}',$segment,$migration);

				$migration = str_replace('{{ID}}',$proc->id,$migration);

				$model_path = "/var/www/openeyes/protected/modules/OphTrOperationnote/models/".$element_classname.".php";

				if (file_exists($model_path)) {
					die("Model exists: $model_path\n");
				}

				file_put_contents($migration_target_file, $migration);

				$model = file_get_contents("/tmp/model_stub");

				$model = str_replace('{{TABLENAME}}',$table_name,$model);
				$model = str_replace('{{ELEMENT_NAME}}',$element_classname,$model);

				file_put_contents($model_path, $model);

				echo "done\n";
			}
		}
	}

	public function strip_punctuation($term) {
		$return = '';
		for ($i=0; $i<strlen($term); $i++) {
			if (ctype_alnum($term[$i]) || $term[$i] == ' ') {
				$return .= $term[$i];
			}
		}
		return $return;
	}

	public function get_table_name($term) {
		return str_replace(' ','_',strtolower($this->strip_punctuation($term)));
	}

	public function get_element_classname($term) {
		$ex = explode(' ',$this->strip_punctuation($term));
		for ($i=0; $i<count($ex); $i++) {
			$ex[$i] = ucfirst($ex[$i]);
		}
		return implode('',$ex);
	}
}
?>
