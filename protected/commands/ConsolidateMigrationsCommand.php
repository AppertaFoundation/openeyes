<?php
class ConsolidateMigrationsCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'ConsolidateMigrations';
	}

	public function getHelp()
	{
		return '
Consolidates all the migrations for a module into one.

Usage: ./yiic consolidatemigrations <module name>

';
	}

	public function run($args)
	{
		if (empty($args)) {
			die($this->getHelp());
		}

		$module_path = "modules/{$args[0]}";

		if (!file_exists($module_path)) {
			die("Module does not exist: {$args[0]}\n");
		}

		$lower = strtolower($args[0]);

		$res = `echo yes | ./yiic migrate --migrationPath=application.modules.{$args[0]}.migrations create {$lower}_consolidated`;

		preg_match('/Create new migration \'(.*?)\'/',$res,$m);

		$migration_path = $m[1];

		$ex = explode('/',$migration_path);
		$migration_class = preg_replace('/\.php$/','',array_pop($ex));

		$dh = opendir($module_path."/migrations");

		$migrations = array();

		while ($file = readdir($dh)) {
			if (!preg_match('/^\.\.?$/',$file)) {
				$migrations[] = $file;
			}
		}

		closedir($dh);

		sort($migrations);

		$fp = fopen($migration_path,"w");
		fwrite($fp,'<?php
class '.$migration_class.' extends CDbMigration
{
	public function up() {
		');

		$down = array();

		foreach ($migrations as $migration) {
			if (!stristr($migration, $migration_class)) {
				$data = $this->parse_migration($module_path."/migrations/$migration");
				$down[] = $data['down'];

				fwrite($fp,$data['up']);
			}
		}

		fwrite($fp,'}

	public function down() {
	');

		foreach (array_reverse($down) as $data) {
			fwrite($fp,$data);
		}

		fwrite($fp,'}
}');

		fclose($fp);

		echo "Consolidated to: $migration_path\n";
	}

	public function parse_migration($migration_path) {
		$data = file_get_contents($migration_path);

		if (preg_match('/public function up\(\)[\s\r\t\n]+\{[\s\r\n\t]+(.*)\}[\r\n\s\t]+public function down\(\)[\r\n\t\s]+\{(.*)\}[\s\r\n\t]+\}/s',$data,$m)) {
			return array('up' => "\t\t".$m[1], 'down' => $m[2]);
		}

		die("Failed to parse migration: $migration_path\n");
	}
}
?>
