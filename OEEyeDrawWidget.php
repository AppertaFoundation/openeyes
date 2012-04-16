<?php
/**
 * Contains a Yii widget for EyeDraw
 * @package EyeDraw
 * @author Bill Aylward <bill.aylward@openeyes.org>
 * @version 0.9
 * @link http://www.openeyes.org.uk/
 * @copyright Copyright (c) 2012 OpenEyes Foundation
 * @license http://www.yiiframework.com/license/
 * 
 * This file is part of OpenEyes.
 * 
 * OpenEyes is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * OpenEyes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with OpenEyes.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * EyeDraw widget for the Yii framework 
 * This widget inserts a canvas element into a view, and registers all required javascript and css files.
 * - When in edit mode, toolbars are displayed with control buttons and the doodle buttons specified in the optional 'doodleToolBarArray'.
 * - Data is stored and loaded from a hidden input element the name of which corresponds to the attribute of the data model
 * - The attribute should be stored as a TEXT data type in the database
 * - If the input element is empty, a template can be produced by placing EyeDraw commands in the optional 'onLoadedCommandArray.'
 *
 * Usage:
 * <code>
 * <?php
 * $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
 *	'identifier'=> 'PS',
 *	'side'=>'R',
 *	'mode'=>'edit',
 *	'size'=>300,
 *	'model'=>$model,
 *	'attribute'=>'eyeDraw1',
 *	'doodleToolBarArray'=>array('RRD', 'UTear'),
 *	'onLoadedCommandArray'=>array(
 *		array('addDoodle', array('Fundus')),
 *		array('deselectDoodles', array()),
 *		),
 *	));
 * ?>
 * </code>
 * The ED.Drawing object can be accessed from Javascript in two ways;
 * 1. Using the variable name derived from the side and identifier, for example 'ed_drawing_edit_RPS'
 * 2. Passed as a parameter to the eDparameterListener function as in the following code snippet;
 * <code>
 * function eDparameterListener(_drawing)
 * {
 *	if (_drawing.selectedDoodle != null)
 *	{
 *		console.log(_drawing.IDSuffix);
 *	}
 * }
 * </code> 
 * @package EyeDraw
 * @author Bill Aylward <bill.aylward@openeyes.org>
 * @version 0.9
 */
class OEEyeDrawWidget extends CWidget
{
	/**
	 * Template to use to render the eyeDraw, this allows multiple different layouts
	 * @var string
	 */

	 public $template = 'default';

	/**
	 * Unique identifier (eg PS for posterior segment drawing)
	 * @var string
	 */
		public $identifier = 'PS';

	/**
	 * Side (R or L)
	 * @var string
	 */
		public $side = "R";

	/**
	 * Mode ('edit' or 'display')
	 * @var string
	 */
	public $mode = 'edit';

	/**
	 * Size of canvas element in pixels
	 * @var int
	 */
		public $size = 400;

	/**
	 * The model possessing an attribute to store JSON data
	 * @var CActiveRecord
	 */
		public $model;

	/**
	 * Name of the attribute, and also of the corresponding hidden input field
	 * @var string
	 */
		public $attribute;

	/**
	 * Array of doodles to appear in doodle selection toolbar
	 * @var array
	 */
		public $doodleToolBarArray = array();

	/**
	 * Array of commands to apply to the drawing object once images have loaded
	 * @var array
	 */
		public $onLoadedCommandArray = array();
	
	/**
	 * URL of the widget directory
	 * @var string
	 */
	private $jsPath;
	private $cssPath;
	private $imgPath;

	/**
	 * Array of EyeDraw script files required
	 * @todo Seach model attribute and contents of doodleToolBarArray to determine subset of files to register
	 * @var array
	 */
		private $scriptArray = array('Adnexal', 'AntSeg', 'Glaucoma', 'MedicalRetina', 'Strabismus', 'VitreoRetinal');
		
	/**
	 * Concatenation of side and idSuffix used to identify control buttons and other related elements
	 * @var string
	 */	
	private $idSuffix;

	/**
	 * Unique name for the EyeDraw drawing object ('ed_drawing_'.$mode.'_'.$side.$identifier).
	 * For example, ed_drawing_edit_RPS
	 * @var string
	 */	
	private $drawingName;

	/**
	 * Unique id of the canvas element
	 * @var string
	 */		
	private $canvasId;

	/**
	 * Unique name for the input element containing EyeDraw data
	 * @var string
	 */	
	private $inputName;

	/**
	 * Unique id for the input element containing EyeDraw data
	 * @var string
	 */	
	private $inputId;

	/**
	 * Represents the eye using the ED object enumeration (0 = right, 1 = left)
	 * @var int
	 */	
	private $eye;

	/**
	 * Flag indicating whether drawing can be edited
	 * @var bool
	 */
	private $isEditable;
	
	/**
	 * Optional inline styling for the canvas element
	 * @var string
	 */

	public $canvasStyle;

	/**
	 * Optional boolean that determines whether to show the toolbar or not
	 * @var boolean
	 */
	
	public $toolbar = true;

	/**
	 * Whether to focus the canvas element after rendering it on the page
	 * @var boolean
	 */
	
	public $focus = false;

	/**
	 * x offset
	 * @var integer
	 */
	
	public $offset_x = 0;

	/**
	 * y offset
	 * @var integer
	 */
	
	public $offset_y = 0;

	/**
	 * Initializes the widget.
	 * This method registers all needed client scripts and renders the EyeDraw content
	 */
		public function init() {
				// Set values of derived properties
				$this->cssPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.eyedraw.css'));
				$this->jsPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.eyedraw.js'));
				$this->imgPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.eyedraw.graphics')).'/';
				$this->idSuffix = $this->identifier;
				$this->drawingName = 'ed_drawing_'.$this->mode.'_'.$this->idSuffix;
				$this->canvasId = 'ed_canvas_'.$this->mode.'_'.$this->idSuffix;
				if (isset($this->model) && isset($this->attribute))
				{
					$this->inputName = get_class($this->model).'['. $this->attribute.']';
					$this->inputId = get_class($this->model).'_'. $this->attribute;

					if (isset($_POST[get_class($this->model)][$this->attribute])) {
						$this->model->{$this->attribute} = $_POST[get_class($this->model)][$this->attribute];
					}
				}
 
				$this->eye = $this->side == "R"?0:1;		// ***TODO*** may require additional options
				$this->isEditable = $this->mode == 'edit'?true:false;
				
				// Register the chosen scripts and CSS file
				$this->registerScripts();
				$this->registerCss();
 
				// Iterate through button array
				foreach($this->doodleToolBarArray as $i => $doodle) {
					// Get title attribute from language specific array
					if (array_key_exists($doodle, DoodleInfo::$titles)) {
						$title = DoodleInfo::$titles[$doodle];
					} else {
						$title = "No description available for this doodle";
					}
					$this->doodleToolBarArray[$i] = array(
						'title' => $title,
						'classname' => $doodle
					);
				}

				$this->render(get_class($this),get_object_vars($this));
	}

	/**
	 * Runs after init and can be used to capture content
	 */
	public function run()
	{		
	}

	/**
	 * Registers all necessary javascript files
	 * @todo Seach model attribute and contents of doodleToolBarArray to determine subset of files to register
	 */			
	protected function registerScripts()
	{
				// Get client script object
		$cs = Yii::app()->getClientScript();
 
				// Register the EyeDraw mandatory scripts
		$cs->registerScriptFile($this->jsPath.'/OEEyeDraw.js', CClientScript::POS_HEAD);
		$cs->registerScriptFile($this->jsPath.'/ED_Drawing.js', CClientScript::POS_HEAD);
		$cs->registerScriptFile($this->jsPath.'/ED_General.js', CClientScript::POS_HEAD);
				
				// Register the specified optional sub-specialty scripts
				for ($i = 0; $i < count($this->scriptArray); $i++)
				{
						$cs->registerScriptFile($this->jsPath.'/ED_'.$this->scriptArray[$i].'.js', CClientScript::POS_HEAD);
				}
		
				// Make drawing var global so can be accessed by tool bar
				//$cs->registerScript('ano-id', 'var ed_drawing_edit_PS;', CClientScript::POS_HEAD);
				
		// Create array of parameters to pass to the javascript function which runs on page load
		$properties = array(
						'drawingName'=>$this->drawingName,
			'canvasId'=>$this->canvasId,
						'eye'=>$this->eye,
						'idSuffix'=>$this->idSuffix,
						'isEditable'=>$this->isEditable,
						'graphicsPath'=>$this->imgPath,
						'inputId'=>$this->inputId,
						'onLoadedCommandArray'=>$this->onLoadedCommandArray,
						'offset_x'=>$this->offset_x,
						'offset_y'=>$this->offset_y,
		);
		
		// Encode parameters and pass to a javascript function to set up canvas
		$properties = CJavaScript::encode($properties);
		$cs->registerScript('scr_'.$this->canvasId, "eyeDrawInit($properties)", CClientScript::POS_LOAD);
	}

	/**
	 * Registers all necessary css files
	 */	
		protected function registerCss()
		{
				$cssFile = $this->cssPath.'/OEEyeDraw.css';
				Yii::app()->getClientScript()->registerCssFile($cssFile);				 
		}
}

/**
 * Language specific doodle descriptions (used for title attributes of doodle selection buttons)
 * 
 * @package EyeDraw
 * @author Bill Aylward <bill.aylward@openeyes.org>
 * @version 0.9
 */
class DoodleInfo
{
	/**
	 * Static array containing descriptions of each doodle (Generated from spreadsheet Doodles.numbers)
	 * @static array
	 */
	public static $titles = array (
		"AdnexalEye" => "Adnexal eye template",
		"AntSeg" => "Anterior segment",
		"NuclearCataract" => "Nuclear cataract",
		"CorticalCataract" => "Cortical cataract",
		"PostSubcapCataract" => "Posterior subcapsular cataract",
		"PCIOL" => "Posterior chamber IOL",
		"ACIOL" => "Anterior chamber IOL",
		"Bleb" => "Trabeculectomy bleb",
		"PI" => "Peripheral iridectomy",
		"RK" => "Radial keratotomy",
		"LasikFlap" => "LASIK flap",
		"Fuchs" => "Fuchs endothelial dystrophy",
		"CornealScar" => "Corneal scar",
		"PhakoIncision" => "Phako incision",
		"SidePort" => "Side port",
		"IrisHook" => "Iris hook",
		"MattressSuture" => "Mattress suture",
		"Freehand" => "Freehand drawing",
		"Lable" => "Label",
		"Arrow" => "Arrow",
		"Slider" => "Slider",
		"PointInLine" => "Point in line",
		"Iris" => "Iris",
		"Pupil" => "Pupil",
		"Gonioscopy" => "Gonioscopy",
		"AntSynech" => "Anterior synechiae",
		"AngleNV" => "Angle new vessels",
		"AngleRecession" => "Angle recession",
		"AngleGrade" => "Angle grade",
		"OpticDisk" => "Optic disk",
		"OpticCup" => "Optic cup",
		"NerveFibreDefect" => "Nerve fibre derect",
		"DiskHaemorrhage" => "Disk haemorrhage",
		"Papilloedema" => "Papilloedema",
		"Supramid" => "Supramid suture",
		"Vicryl" => "Vicryl suture",
		"Molteno" => "Molteno tube",
		"Baerveldt" => "Baerveld tube",
		"Ahmed" => "Ahmed tube",
		"Patch" => "Tube patch",
		"OpticDiskPit" => "Optic disk pit",
		"PostPole" => "Posterior pole",
		"PRP" => "Panretinal photocoagulation",
		"Geographic" => "Geographic atrophy",
		"CNV" => "Choroidal new vessels",
		"VitreousOpacity" => "Vitreous opacity",
		"DiabeticNV" => "Diabetic new vessels",
		"Circinate" => "Circinate retinopathy",
		"OrthopticEye" => "Orthoptic eye",
		"Shading" => "Shading",
		"UpShoot" => "Up shoot",
		"UpDrift" => "Up drift",
		"APattern" => "A pattern",
		"VPattern" => "V pattern",
		"Fundus" => "Fundus",
		"UTear" => "Traction ‘U’ tear",
		"RoundHole" => "Round hole",
		"Rrd" => "Rhegmatogenous retinal detachment",
		"Buckle" => "Buckle",
		"Dialysis" => "Dialysis",
		"GRT" => "Giant retinal tear",
		"MacularHole" => "Macular hole",
		"StarFold" => "Star fold",
		"Lattice" => "Lattice",
		"Cryo" => "Cryotherapy scar",
		"LaserCircle" => "Circle of laser photocoagulation",
		"AntPVR" => "Anterior PVR",
		"Retinoschisis" => "Retinoschisis",
		"OuterLeafBreak" => "Outer leaf break",
		"InnerLeafBreak" => "Inner leaf break",
		"BuckleOperation" => "Buckle operation",
		"CircumferentialBuckle" => "Circumferential buckle",
		"BuckleSuture" => "Buckle suture",
		"EncirclingBand" => "Encircling band",
		"DrainageSite" => "Drainage site",
		"RadialSponge" => "Radial sponge",
	);
}
