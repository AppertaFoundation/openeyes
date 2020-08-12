<?php


/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class DevSetupCommand extends CConsoleCommand
{
    // this should be changed once we've worked out where we want to store the configs for different clients
    protected $config_path;
    protected $mode;
    protected $username;
    protected $password;
    protected $mysqlp;

    protected function getConfigPath()
    {
        if (!$this->config_path) {
            return Yii::app()->basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'local.sample'.DIRECTORY_SEPARATOR;
        } else {
            return Yii::app()->basePath.DIRECTORY_SEPARATOR.$this->config_path;
        }
    }

    public function getName()
    {
        return 'Uses local sample config files to checkout the right module repositories';
    }

    public $gitowners = array('across-health', 'openeyes');

    public function getHelp()
    {
        $default_git = implode(', ', $this->gitowners);

        return <<<EOH
yiic devsetup --label=<configlabel> --gitowner=<username> --username=<gituser --branch=<branchname> --reset --resetfile=<filename> --mysqlp=<password>
    --label: defaults to sample
    --gitowner: can be multiple. Currently set to $default_git
    --username: github username for authenticating your requests
    --mode: ssh or http for cloning
    --branch: defaults to master, but is the branch to checkout on repositories
    --reset: flag, if set the script will drop the database and recreate it (if a password is set on the mysql root user, provide it with mysqlp option
    --resetfile:

    The purpose of this command is to provide a convenience wrapper for checking out the modules for a configuration,
    and setting them up on a particular branch. It will then perform the appropriate migrations.
EOH;
    }

    /**
     * Iterate through configured git users to find the module repository.
     *
     * @param $module
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getGitAddress($module)
    {
        $u = null;
        $clone_key = null;

        switch ($this->mode) {
            case 'ssh':
                $clone_key = 'ssh_url';
                break;
            case 'http':
                $clone_key = 'clone_url';
                break;
            default:
                throw new \Exception("Unrecognised clone mode {$this->mode}");
        }

        foreach ($this->gitowners as $user) {
            $url = "https://api.github.com/repos/{$user}/{$module}";
            $check = curl_init($url);
            if (!empty(Yii::app()->params['curl_proxy'])) curl_setopt($check, CURLOPT_PROXY, Yii::app()->params['curl_proxy']);
            curl_setopt($check, CURLOPT_USERAGENT, 'OpenEyes-Cloner');
            curl_setopt($check, CURLOPT_RETURNTRANSFER, 1);
            if ($this->username) {
                curl_setopt($check, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
            }
            //curl_setopt($check, CURLOPT_NOBODY, true);
            $content = curl_exec($check);
            if ($content !== false) {
                $status = curl_getinfo($check, CURLINFO_HTTP_CODE);
                curl_close($check);
                if ($status == 200) {
                    $resp = json_decode($content);
                    if (!property_exists($resp, $clone_key)) {
                        throw new \Exception('Unexpected github response, you may need to authenticate with the username option.');
                    }

                    return $resp->$clone_key;
                }
            }
        }
    }

    /**
     * @param $module
     *
     * @return string
     */
    protected function getModulePath($module)
    {
        return Yii::app()->basePath.DIRECTORY_SEPARATOR."modules/$module";
    }

    /**
     * Clone the module from the appropriate repository.
     *
     * @param $module
     *
     * @throws Exception
     */
    protected function cloneModule($module)
    {
        if ($address = $this->getGitAddress($module)) {
            $cmd = "git clone {$address} ".$this->getModulePath($module);
            if (!file_exists($this->getModulePath($module))) {
                //echo "{$cmd}\n";
                echo `$cmd`;
            }
        } else {
            error_log(str_repeat('*', 30));
            error_log("WARNING: could not find repository for {$module}");
            error_log(str_repeat('*', 30));
        }
    }

    /**
     * Clone missing modules from the given list, and switch to specified branch.
     *
     * @param array  $modules
     * @param string $branch
     */
    protected function setupModules(array $modules, $branch = 'master')
    {
        echo "processing modules ...\n";

        foreach ($modules as $id => $defn) {
            $module = is_int($id) ? $defn : $id;
            if (!file_exists($this->getModulePath($module))) {
                $this->cloneModule($module);
            }
            if (file_exists($this->getModulePath($module))) {
                $cmd = 'cd '.$this->getModulePath($module)."; git checkout {$branch};";
                echo `$cmd`;
            }
        }
    }

    /**
     * Backup and replace the current local common config file.
     *
     * @param $config_file
     */
    protected function copyConfig($config_file)
    {
        $target_config = Yii::app()->basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.'common.php';
        if (file_exists($target_config)) {
            copy($target_config, $target_config.'.bak');
            echo "Backed up previous configuration to {$target_config}.bak\n";
        }
        copy($config_file, $target_config);
    }

    /**
     * Drop the current database and re-create.
     *
     * @TODO: support same for test database?
     * @TODO: use the config file to determine the database, rather than making assumptions.
     * @TODO: use config to login and drop all tables, rather than relying on root user?
     */
    protected function resetDatabase($dumpfile)
    {
        $cmd = 'mysql -u root';
        if ($this->mysqlp) {
            $cmd .= " -p{$this->mysqlp}";
        }

        $cmd .= " -e 'drop database openeyes; create database openeyes;'";

        echo `$cmd`;

        if ($dumpfile) {
            if (file_exists($dumpfile)) {
                $cmd = "cat $dumpfile | mysql -u root openeyes";
                `$cmd`;
            } else {
                error_log("WARN: could not find {$dumpfile} to import.");
            }
        }
    }

    /**
     * Run the core and module migrations.
     */
    protected function runMigrations()
    {
        // run as separate shell command calls to ensure changes to configuration are loaded
        $cmd_base = Yii::app()->getBasePath().DIRECTORY_SEPARATOR.'yiic ';
        $migrate = $cmd_base.'migrate';
        $migratemodules = $cmd_base.'migratemodules';

        echo `$migrate`;
        echo `$migratemodules`;
    }

    /**
     * Default Action.
     *
     * @param string $label
     * @param string $mode
     * @param null   $username
     * @param string $branch
     * @param array  $gitowner
     * @param bool   $reset
     * @param null   $mysqlp
     */
    public function actionIndex($label = 'sample',
                                $mode = 'http',
                                $username = null,
                                $branch = 'master',
                                array $gitowner = array(),
                                $reset = false,
                                $resetfile = null,
                                $mysqlp = null)
    {
        $this->mode = $mode;
        $this->username = $username;

        if ($this->username) {
            $this->password = $this->prompt("Please provide the password for github user {$this->username}:");
        }

        $this->gitowners = array_merge($gitowner, $this->gitowners);
        $config_file = $this->getConfigPath()."common.{$label}.php";

        echo "Retrieving config from {$config_file}\n";
        $config = array();

        if (!file_exists($config_file)) {
            $this->usageError("Cannot find config file for {$label}");
            exit();
        }
        include $config_file;

        if (!@$config['modules']) {
            $this->usageError('No modules parameter found in config file');
        }

        $this->setUpModules($config['modules'], $branch);

        $this->copyConfig($config_file);

        if ($reset || $resetfile) {
            $this->resetDatabase($resetfile);
        }

        $this->runMigrations();
    }
}
