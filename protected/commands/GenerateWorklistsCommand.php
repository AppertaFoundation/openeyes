<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class GenerateWorklistsCommand extends CConsoleCommand
{
    protected $log_levels = array(
        'FATAL' => 1,
        'ERROR' => 2,
        'WARN' => 3,
        'INFO' => 4,
        'DEBUG' => 5,
    );

    protected static $DEFAULT_LOG_LEVEL = 'WARN';
    protected $log_level;

    /**
     * @var WorklistManager
     */
    protected $manager;

    /**
     * GenerateWorklistsCommand constructor.
     *
     * @param string                $name
     * @param CConsoleCommandRunner $runner
     * @param WorklistManager       $manager
     */
    public function __construct($name, $runner, $manager = null)
    {
        if (is_null($manager)) {
            $manager = new WorklistManager();
        }
        $this->manager = $manager;

        parent::__construct($name, $runner);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Generate Automatic Worklists Command';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        $log_levels = implode('|', array_keys($this->log_levels));

        return <<<EOH
Generates the individual Worklists from the current Worklist Definitions.

--verbosity={$log_levels}
--horizon=Interval String
EOH;
    }

    public $horizon;
    public $defaultAction = 'generate';

    public $verbosity;

    /**
     * @param $verbosity
     */
    protected function setLogLevel($verbosity)
    {
        $level = $this->log_levels[self::$DEFAULT_LOG_LEVEL];

        if ($verbosity) {
            $key = strtoupper($verbosity);
            if (array_key_exists($key, $this->log_levels)) {
                $level = $this->log_levels[$key];
            }
        }

        $this->log_level = $level;
    }

    /**
     * @param $horizon
     *
     * @return DateTime
     */
    public function getDateLimit($horizon)
    {
        if ($horizon) {
            $interval = DateInterval::createFromDateString($horizon);
            $now = new DateTime();
            $limit = clone $now;
            $limit->add($interval);

            if ($limit <= $now) {
                $this->usageError("Invalid horizon string {$horizon}");
            }

            return $limit;
        }

        return $this->manager->getGenerationTimeLimitDate();
    }

    /**
     * @param null $verbosity
     * @param null $horizon
     */
    public function actionGenerate($verbosity = null, $horizon = null)
    {
        $this->setLogLevel($verbosity);
        $date_limit = $this->getDateLimit($horizon);

        $this->info('Starting automatic worklist generation.');
        $this->debug('Date limit is '.$date_limit->format(Helper::NHS_DATE_FORMAT));

        try {
            $result = $this->manager->generateAllAutomaticWorklists($date_limit);
            if ($result === false) {
                foreach ($this->manager->getErrors() as $err) {
                    $this->error($err);
                }
                $this->finish(2);
            } else {
                $this->info('generation complete');
                $this->debug("{$result} new worklists generated.");
            }
        } catch (Exception $e) {
            $this->fatal($e->getMessage());
        }
    }

    public function output($level, $msg)
    {
        if ($this->log_level >= $this->log_levels[$level]) {
            echo "{$level}: {$msg}\n";
        }
    }

    public function debug($msg)
    {
        $this->output('DEBUG', $msg);
    }

    public function info($msg)
    {
        $this->output('INFO', $msg);
    }

    public function warn($msg)
    {
        $this->output('WARN', $msg);
    }

    public function error($msg)
    {
        $this->output('ERROR', $msg);
    }

    public function fatal($msg, $exit_code = 1)
    {
        $this->output('FATAL', $msg);
        $this->finish($exit_code);
    }

    /**
     * Simple wrapper to abstract termination.
     *
     * @param int $exit_code
     */
    protected function finish($exit_code = 1)
    {
        exit($exit_code);
    }
}
