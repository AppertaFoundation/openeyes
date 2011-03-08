<?php
class GenerateElementTableSqlCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Generate Element Table SQL Command.';
	}
	public function getHelp()
	{
		return 'A quick and dirty script to generate the SQL to create tables for a list of element tables in the database.';
	}

	public function run($args)
	{
		$elementTables = ElementType::Model()->findAll();
		echo "-- --------------------------------------------------------\n";
		foreach ($elementTables as $table) {
			$className = $this->fromCamelCase($table->class_name);
echo "

--
-- Table structure for table `$className`
--

CREATE TABLE IF NOT EXISTS `$className` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------
";
		}
	}

	public function fromCamelCase($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}
}
?>
