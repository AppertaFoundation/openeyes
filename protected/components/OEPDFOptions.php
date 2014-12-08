<?php
class OEPDFOptions
{
	public $file;
	public $version;
	public $raw;
	public $objects = array();
	public $root = null;
	public $info = null;
	public $highest = 0;
	public $catalog = array();
	public $js = null;
	public $print_scaling = true;

	function __construct($file)
	{
		if (!file_exists($file)) {
			throw new Exception("File not found: $file");
		}

		$this->file = $file;
		$this->raw = file_get_contents($file);

		$this->parse();
	}

	function parse()
	{
		foreach (explode(chr(10),trim($this->raw)) as $line) {
			if (preg_match('/^%PDF-(.*?)$/',$line,$m)) {
				$this->version = $m[1];
			} else if (preg_match('/^([0-9]+) [0-9]+ obj$/',$line,$m)) {
				if (!$this->version) {
					throw new Exception("Invalid file or not a PDF.");
				}

				$current_object = $m[1];
				$this->objects[$m[1]] = array('raw' => '');

				if ($m[1] > $this->highest) {
					$this->highest = $m[1];
				}
			} else if ($line == 'endobj') {
				$current_object = null;
			} else if ($line == "xref") {
				break;
			} else {
				if (!$this->version) {
					throw new Exception("Invalid file or not a PDF.");
				}

				$this->objects[$current_object]['raw'] .= "$line\n";
			}
		}

		foreach ($this->objects as $n => $object) {
			if (preg_match('/\/Type \/Catalog/',$object['raw'])) {
				$this->root = $n;
			}
			if (preg_match('/\/Title/',$object['raw'])) {
				$this->info = $n;
			}
		}

		if ($this->root === null) {
			throw new Exception("Root element not found in pdf document.");
		}
	}

	function injectJS($js)
	{
		$this->js = $js;
	}

	function disablePrintScaling()
	{
		$this->print_scaling = false;
	}

	function enablePrintScaling()
	{
		$this->print_scaling = true;
	}

	function write()
	{
		$output = "%PDF-$this->version\n";

		foreach ($this->objects as $n => $object) {
			if ($n == $this->root) {
				if ($this->js) {
					$object['raw'] = $this->addJSToCatalog($object['raw']);
				}

				if (!$this->print_scaling) {
					$object['raw'] = $this->disablePrintScalingOption($object['raw']);
				}
			}

			$this->catalog[$n] = strlen($output);

			$output .= "$n 0 obj\n{$object['raw']}endobj\n";
		}

		if ($this->js) {
			$this->catalog[$this->highest+1] = strlen($output);

			$output .= $this->getJSBlock1();

			$this->catalog[$this->highest+2] = strlen($output);
			$output .= $this->getJSBlock2($this->js);
		}

		$output .= $this->getXref(strlen($output));

		if (!@file_put_contents($this->file,$output)) {
			return false;
		}

		return true;
	}

	protected function getJSBlock1()
	{
		$js_obj1 = $this->highest+1;
		$js_obj2 = $this->highest+2;

		return "$js_obj1 0 obj
<< /Names [ (EmbeddedJS) $js_obj2 0 R ] >>
endobj\n";
	}

	protected function getJSBlock2($js)
	{
		$js_obj2 = $this->highest+2;
		$js_inject = '';

		for ($i=0; $i<strlen($js); $i++) {
			$js_inject .= "\0{$js[$i]}";
		}

		return "$js_obj2 0 obj
<< /S /JavaScript /JS (\xfe\xff$js_inject) >>
endobj\n";
	}

	protected function addJSToCatalog($catalog)
	{
		$js_obj1 = $this->highest+1;

		if (preg_match('/^<<.*?'.'>>[\r\n]*$/',$catalog)) {
			if (preg_match('/\/Names <</',$catalog)) {
				$catalog = preg_replace('/\/Names <</',"/Names << /JavaScript $js_obj1 0 R",$catalog);
			} else {
				$catalog = preg_replace('/>>[\r\n]*$/',"/Names << /JavaScript $js_obj1 0 R >> >>\n",$catalog);
			}
		} else {
			if (preg_match('/\/Names <</',$catalog)) {
				$catalog = preg_replace('/\/Names <</',"/Names << /JavaScript $js_obj1 0 R",$catalog);
			} else {
				$catalog = preg_replace('/>>[\r\n]*$/',"/Names << /JavaScript $js_obj1 0 R >>\n>>\n",$catalog);
			}
		}

		return $catalog;
	}

	protected function disablePrintScalingOption($catalog)
	{
		if (preg_match('/^<<.*?>>[\r\n]$/',$catalog)) {
			return preg_replace('/>>[\r\n]$/',"/ViewerPreferences << /Direction/L2R/PrintScaling/None >> >>\n",$catalog);
		} else {
			$_catalog = '';

			foreach (explode(chr(10),trim($catalog)) as $line) {
				if ($line == '>>') {
					$_catalog .= "/ViewerPreferences << /Direction/L2R/PrintScaling/None >>\n";
				}
				$_catalog .= $line."\n";
			}

			return $_catalog;
		}
	}

	protected function getXref($xref_offset)
	{
		$count = (count($this->objects) + ($this->js ? 2 : 0));
		$xref = "xref\n0 ".$count."\n0000000000 65535 f\n";

		ksort($this->catalog);

		foreach ($this->catalog as $n => $offset) {
			$xref .= str_pad($offset,10,"0",STR_PAD_LEFT)." 00000 n\n";
		}

		return $xref . "trailer\n<< /Size ".(count($this->objects) + ($this->js ? 1 : 0))." /Root ".$this->root." 0 R ".($this->info ? "/Info $this->info 0 R " : "").">>\nstartxref\n$xref_offset\n%%EOF\n";
	}
}
