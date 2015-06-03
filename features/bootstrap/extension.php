<?php
use Behat\Behat\Extension\Extension;
use Behat\Behat\Console\Processor\LocatorProcessor;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
class OpenEyesExtension extends Extension {
	protected function getServiceDefinitionsName() {
		return 'extension';
	}
}
class OpenEyesLocatorProcessor extends LocatorProcessor {
	private $container;
	public function __construct(ContainerInterface $container) {
		parent::__construct ( $container );
		
		$this->container = $container;
	}
	public function process(InputInterface $input, OutputInterface $output) {
		parent::process ( $input, $output );
		
		if ($input->getArgument ( 'features' )) {
			return;
		}
		
		$modsPath = realpath ( __DIR__ . '/../../protected/modules' );
		$paths = array (
				__DIR__ . '/..' 
		);
		foreach ( Finder::create ()->directories ()->depth ( 0 )->in ( $modsPath ) as $path ) {
			if (file_exists ( $path . '/features' )) {
				$paths [] = $path . '/features';
			}
		}
		
		$this->container->get ( 'behat.console.command' )->setFeaturesPaths ( $paths );
	}
}

return new OpenEyesExtension ();
