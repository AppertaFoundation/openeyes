<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class FlotChart extends BaseCWidget
{
	public $chart_id;
	public $legend_id;

	protected $has_data = false;

	protected $series = array();
	protected $options = array(
		'colors' => array('#4daf4a', '#984ea3', '#ff7f00', '#ffff33', '#a65628', '#e41a1c', '#377eb8', '#f781bf'),
		'xaxis' => array('mode' => null),
		'yaxes' => array(),
		'grid' => array('hoverable' => true),
		'zoom' => array('interactive' => true),
		'pan' => array('interactive' => true),
	);

	protected $x_min = null;
	protected $x_max = null;
	protected $yaxis_map = array();

	protected $point_labels = array();

	/**
	 * Whether there are any points in the graph yet
	 *
	 * @return bool
	 */
	public function hasData()
	{
		return $this->has_data;
	}

	/**
	 * Get the lowest X value added to the graph so far (or null if none)
	 *
	 * @return float|null
	 */
	public function getXMin()
	{
		return $this->x_min;
	}

	/**
	 * Get the highest X value added to the graph so far (or null if none)
	 *
	 * @return float
	 */
	public function getXMax()
	{
		return $this->x_max;
	}

	/**
	 * Add a point to the specified data series (creating it if it doesn't exist)
	 *
	 * @param string $series_name
	 * @param float $x
	 * @param float $y
	 * @param string $label
	 * @return FlotChart
	 */
	public function addPoint($series_name, $x, $y, $label = null)
	{
		$this->has_data = true;
		$this->initSeries($series_name);
		$this->series[$series_name]['data'][] = array($x, $y);
		if (is_null($this->x_min) || $x < $this->x_min) $this->x_min = $x;
		if (is_null($this->x_max) || $x > $this->x_max) $this->x_max = $x;
		$this->point_labels[$series_name][] = Yii::app()->format->ntext($label);
		return $this;
	}

	/**
	 * Configure the X axis
	 *
	 * @param array $config
	 * @return FlotChart
	 */
	public function configureXAxis(array $config = array())
	{
		$this->configureChart(array('xaxis' => $config));
		return $this;
	}

	/**
	 * Add and/or configure a Y axis
	 *
	 * @param string $axis_name
	 * @param array $config
	 * @return FlotChart
	 */
	public function configureYAxis($axis_name, array $config = array())
	{
		if (isset($this->yaxis_map[$axis_name])) {
			$n = $this->yaxis_map[$axis_name];
		} else {
			$n = count($this->options['yaxes']);
			$this->yaxis_map[$axis_name] = $n;
			$this->options['yaxes'][$n] = array('panRange' => false, 'zoomRange' => false);
		}

		$this->options['yaxes'][$n] = array_replace_recursive($this->options['yaxes'][$n], $config);

		return $this;
	}

	/**
	 * Configure a data series, creating it if it doesn't exist
	 *
	 * @param string $series_name
	 * @param array $config
	 * @return FlotChart
	 */
	public function configureSeries($series_name, array $config)
	{
		if (isset($config['data'])) {
			throw new Exception("Can't set series data using configureSeries, use addPoint instead");
		}

		$this->initSeries($series_name);
		$this->series[$series_name] = array_replace_recursive($this->series[$series_name], $config);
		return $this;
	}

	/**
	 * Apply configuration to the chart as a whole
	 *
	 * @param array $config
	 * @return FlotChart
	 */
	public function configureChart(array $config)
	{
		$this->options = array_replace_recursive($this->options, $config);
		return $this;
	}

	/**
	 * Render the chart
	 */
	public function run()
	{
		if (isset($this->legend_id)) {
			$this->configureChart(array('legend' => array('container' => "#{$this->legend_id}")));
		}

		foreach ($this->series as &$series) {
			if (isset($series['yaxis'])) {
				$series['yaxis'] = $this->yaxis_map[$series['yaxis']] + 1;
			}
		}

		if (!isset($this->options['xaxis']['min'])) {
			$this->options['xaxis']['min'] = $this->x_min;
		}

		if (!isset($this->options['xaxis']['max'])) {
			$this->options['xaxis']['max'] = $this->x_max;
		}

		if (!isset($this->options['xaxis']['panRange'])) {
			$this->options['xaxis']['panRange'] = array($this->x_min, $this->x_max);
		}

		if (!isset($this->options['xaxis']['zoomRange'])) {
			$max_range = $this->options['xaxis']['panRange'][1] - $this->options['xaxis']['panRange'][0];

			if ($this->options['xaxis']['mode'] == 'time') {
				$min_range = min(604800000, $max_range);  // 1 week
			} else {
				$min_range = $max_range / 10;
			}

			$this->options['xaxis']['zoomRange'] = array($min_range, $max_range);
		}

		$this->render('FlotChart');
	}

	protected function initSeries($series_name)
	{
		if (!isset($this->series[$series_name])) {
			$this->series[$series_name] = array(
				'label' => $series_name,
				'data' => array(),
			);
		}
		if (!isset($this->point_labels[$series_name])) {
			$this->point_labels[$series_name] = array();
		}
	}
}
