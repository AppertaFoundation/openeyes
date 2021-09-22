<?php
class UpdateInstitutionCodeCommand extends CConsoleCommand
{
    public function run($args)
    {
        $warning_list = array(
            "\e[91mWARNING:\n",
            "* Please only execute this script under directed instructions, as any maloperations can lead to FATAL errors and inaccessible web server!\n",
            "* It is highly recommended that you take a backup of the database before proceeding with this process.\n",
            "* Before you run this script, please make sure the docker-compose is run to update the server institution code to your desired value\n",
        );
        foreach ($warning_list as $warning) {
            echo "$warning";
        }
        $action_list = array(
            "\n\e[93mThis script is used to update the institution code (remote_id) in the database only, the following are the expected execution steps:\n",
            "RUN docker-compose with an updated institution code \e[91m(THIS IS NOT AUTOMATIC)\e[93m\n",
            "Then run this script\n",
            "Choose an institution from the list\n",
            "Input a new remote_id for the chosen instituion\n",
            "Confirm the change\n",
            "Confirm the site is working on the front-end\n",
            "Process completed\e[0m\n"
        );
        foreach ($action_list as $index => $value) {
            $msg = $index === 0 ? '' : "$index. ";
            echo "$msg$value";
        }

        $this->listInstitutions();
    }

    protected function listInstitutions()
    {
        $institutions = Institution::model()->findAll(array('order' => 'id'));
        $server_ins = getenv('OE_INSTITUTION_CODE');
        $cur_ins = Institution::model()->find('remote_id = :remote_id', array(':remote_id' => $server_ins));
        $ins_id_list = array();
        echo "\e[91mPlease make sure that at least one institution code (remote_id) in the database matches the application envrionment variable OE_INSTITUTION_CODE.\e[93m\nThe current application envrionment variable OE_INSTITUTION_CODE is set to $server_ins. \e[0m\n";
        foreach ($institutions as $institution) {
            $color = "\e[39m"; //default color
            $append = "";
            if (isset($cur_ins) && $institution->id === $cur_ins->id) {
                $color = "\e[32m"; // green
                $append = "<<<<<<<<<< (Current Primary Institution)\e[0m";
            }
            echo "$color [$institution->id] $institution->name (remote_id: $institution->remote_id) $append\n";
            $ins_id_list[$institution->id] = $institution;
        }
        $this->updateInstitutionCode($ins_id_list, $server_ins);
    }
    protected function updateInstitutionCode($ins_id_list, $server_ins)
    {
        $selected_id = readline("Please enter the number in '[]' to update: ");
        if (!isset($ins_id_list[$selected_id])) {
            echo "\e[91mCan not find institution with id $selected_id in the list!\e[0m\n\n";
            $this->listInstitutions();
        }
        $selected_ins = $ins_id_list[$selected_id];
        $remote_id = readline("Please enter a new remote_id: ");
        while (empty($remote_id)) {
            $remote_id = readline("Please enter a new remote_id: ");
        }
        if ($remote_id !== $server_ins) {
            echo "\e[93mWARNING: The institution code (remote_id) you inputed does not match the one on the application server!\e[0m\n";
            // echo "\e[93mIf you are not intending to update the primary institution code or are going to run docker-compose after this script, please continue.\e[0m\n";
        }
        $confirm = readline("Please confirm you want to change $selected_ins->name's current remote_id '$selected_ins->remote_id' to '$remote_id'? Y/N or any other key to exit: ");
        switch ($confirm) {
            case 'Y':
            case 'y':
            case 'yes':
            case 'Yes':
            case 'YES':
                $selected_ins->remote_id = $remote_id;
                $selected_ins->save();
                $this->listInstitutions();
                break;
            case 'N':
            case 'n':
            case 'no':
            case 'No':
            case 'NO':
                $this->listInstitutions();
                break;
            default:
                exit;
            break;
        }
    }
}
