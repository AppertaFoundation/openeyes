<?php
class GenerateSiteElementTypeDataCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Generate Site Element Type Data Command';
	}
	public function getHelp()
	{
		return 'site_element_type needs to contain all entries which are possible. this command generates those based on other tables';
	}

	public function run($args)
	{
		$this->add_new($args);
		// $this->remove_old($args);
	}

	public function add_new($args) {
		$specialties = Specialty::Model()->findAll();
		$possible_element_types = PossibleElementType::Model()->with('eventType')->findAll();

		foreach ($specialties as $specialty) {
			echo $specialty->name . "\n";
			foreach ($possible_element_types as $pet) {
				// if there's not an existing record, where first_in_episode is false, make one
				if (!$existing_site_element_type = SiteElementType::Model()->findAllByAttributes(array('possible_element_type_id' => $pet->id, 'specialty_id' => $specialty->id, 'first_in_episode' => false))) {
					echo "\tCreating non-first-in-episode site_element_type entry for: possible_element_type id: " . $pet->id . " specialty: " . $specialty->name . " (event type is: " . $pet->eventType->name . ")\n";
					$new_site_element_type = new SiteElementType;
					$new_site_element_type->possible_element_type_id = $pet->id;	
					$new_site_element_type->specialty_id = $specialty->id;
					$new_site_element_type->view_number = 1;	
					$new_site_element_type->required = 0;
					$new_site_element_type->first_in_episode = 0;
					$new_site_element_type->save();	
				}

				// if it's possible for the first in episode to be different:
				// echo "FIEP: " . $pet->eventType->name ."=".$pet->eventType->first_in_episode_possible."\n";
				if ($pet->eventType->first_in_episode_possible == 1) {
					// if there's not an existing record, where first_in_episode is true, make one
					if (!$existing_site_element_type = SiteElementType::Model()->findAllByAttributes(array('possible_element_type_id' => $pet->id, 'specialty_id' => $specialty->id, 'first_in_episode' => true))) {
					echo "\tCreating first-in-episode site_element_type entry for: possible_element_type id: " . $pet->id . " specialty: " . $specialty->name . " (event type is: " . $pet->eventType->name . ")\n";
						$new_site_element_type = new SiteElementType;
						$new_site_element_type->possible_element_type_id = $pet->id;	
						$new_site_element_type->specialty_id = $specialty->id;
						$new_site_element_type->view_number = 1;	
						$new_site_element_type->required = 0;
						$new_site_element_type->first_in_episode = 1;
						$new_site_element_type->save();	
					}
				}
			}
		}
		return true;
	}
}
?>
