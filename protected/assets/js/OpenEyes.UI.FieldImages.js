/** 
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function(exports, Util, EventEmitter) {

	'use strict';
	/**
	 * FieldImages constructor.
	 * @constructor
	 * @name OpenEyes.UI.FieldImages
	 * @memberOf OpenEyes.UI
	 * @extends OpenEyes.Util.EventEmitter
	 * @example
	 * var fieldImages = new OpenEyes.UI.FieldImages({
	 *	images: OpenEyes.Util.FieldImagesList,
	 *	idToImages: array('ElementHTML_id' => OpenEyes.Util.FieldImagesList.property)
	 * });
	 */
	function FieldImages(options) {
		EventEmitter.call(this);
		this.options = $.extend(true, {}, FieldImages._defaultOptions, options);
	}

	Util.inherits(EventEmitter, FieldImages);

	/**
	 * The default dialog options. Custom options will be merged with these.
	 * @name OpenEyes.UI.FieldImages#_defaultOptions
		 * @property {string} [title=null] - Dialog title
		 * @property {array} [images=null] - Images available for fields
	 * @property {array} [idToImages=null] - Html ID to images relation
	 */
	FieldImages._defaultOptions = {
		title: "Field Images",
		images: null,
		idToImages:null,
		dialogInstance:null
	};

	/**
	 * Creates and stores the dialog container, and creates a new jQuery UI
	 * instance on the container.
	 * @name OpenEyes.UI.FieldImages#create
	 * @method
	 * @private
	 */
	FieldImages.prototype.createDiag = function(fieldElId) {
		this.options.dialog = new OpenEyes.UI.Dialog({
			title: this.options.title,
			content: this.createImagesDiv(this.options.idToImages[fieldElId ], fieldElId)
		});
		this.options.dialog.open();
	};

		/**
		 * Creates the Images container
		 * @name OpenEyes.UI.FieldImages#createImagesDiv
		 * @method
		 * @private
		 */
	FieldImages.prototype.createImagesDiv = function(fieldElId, selectId) {
		var wrapper = $('<div/>', {
			'class':  "fieldsWrapper"
		});
		for(var sval in fieldElId['selects']){
			var imgPath = null;
			if(fieldElId['id'] in this.options.images){
/*				if(sval in this.options.images[fieldElId['id']]){
					imgPath = this.options.images[fieldElId['id']][sval];
				}
*/
				imgPath = OE_core_asset_path + '/img/fieldImages/' + fieldElId['id'] + '-' + sval + '.jpg';
			}

			var el = $('<div/>', {
					class: 'ui-field-image'
			}).click({
				selectId: selectId,
				val: sval,
				fieldImgInstance: this
			},function(e) {
				$( "#"+ e.data.selectId).val(e.data.val);
				e.data.fieldImgInstance.options.dialog.close();
			});
			var valPar = $('<p class="ui-field-image-val">' + sval + '</p>');
			if(imgPath){
				$(el).css("background-image", "url("+ imgPath + ")");
				$(valPar).appendTo(el);
			}
			else{
				$(el).css("background-color", "#999");
				$(valPar).appendTo(el);
				$('<p class="ui-field-image-no-preview">No Preview</p>').appendTo(el)
			}

			$(el).appendTo(wrapper);
		}
		return wrapper;
	};

	/**
	 * Sets fields  buttons in dom
	 * @name OpenEyes.UI.FieldImages#setFieldButtons
	 * @method
	 * @private
	 */
	FieldImages.prototype.setFieldButtons = function() {
		for (var selectId in this.options.idToImages) {

			if($('#' + selectId)){
				this.options.idToImages[selectId]
				$('<img/>', {
					id: selectId + "_cog",
					src: OE_core_asset_path + '/img/_elements/icons/event/small/images_photo.png',
					alt: 'Opens ' + selectId + ' field images',
					'class': 'ui-field-images-icon'
				}).insertAfter( '#' + selectId );

				var this_ = this;

				$( '#' + selectId + "_cog").click( function() {
					var sId = this.id.substr(0, (this.id.length - 4) );
					this_.createDiag( sId);
				});
			}
		}
	};

	exports.FieldImages = FieldImages;

}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));
