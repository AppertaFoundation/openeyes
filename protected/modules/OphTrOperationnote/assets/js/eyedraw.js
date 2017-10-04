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

function ED_Magic() {if (this.init) this.init.apply(this, arguments); }

ED_Magic.prototype = {
	init : function(params) {
		this.eye_left = 1;
		this.eye_right = 2;
		this.surgeonPosition = null;
		this.phakoDoodle = null;
		this.incisionTarget = 0;
		this.incisionDirection = 0;
		this.sidePort1 = null;
		this.sidePort2 = null;
		this.surgeonDoodle = null;
		this.surgeonTarget = 0;
		this.surgeonDirection = 0;
		this.incisionSite = null;
		this.followSurgeon = true;
		this.sidePortRotations = [];
		this.surgeonLoop = 0;
		this.surgeonLoopTarget = 0;
		this.incisionMoving = 0;
		this.surgeonMoving = 0;

		/* configurable */
		this.surgeonYOffset = 300;
		this.surgeonMagicNumber = 42;
		this.surgeonAlternateProbability = 100; // rand(0,n) needs to hit this.surgeonMagicNumber to trigger
		this.surgeonExtraLoopProbability = 1000;
		this.surgeonMoveTableProbability = 2000;
		this.surgeonMoveTable = 0;
		this.movePoints = 10;
		this.timerSpeed = 20;
	},

	/* Event handler for doodle changes, this is fired by the EyeDraw module */

	handleEvent : function(doodle) {
		switch (doodle.className) {
			case 'PhakoIncision':
				setCataractSelectInput('incision_site',doodle.getParameter('incisionSite'));
				setCataractSelectInput('incision_type',doodle.getParameter('incisionType'));
				setCataractInput('length',doodle.getParameter('incisionLength'));
				setCataractInput('meridian',doodle.getParameter('incisionMeridian'));

				if (this.incisionSite == null) {
					this.incisionSite = this.toDegrees(doodle.rotation);
				}

				if (this.toDegrees(doodle.rotation) != this.incisionSite) {
					this.incisionSite = this.toDegrees(doodle.rotation);
					this.followSurgeon = false;
				}
				break;
			case 'SidePort':
				if (this.sidePortRotations[doodle.id] == null) {
					this.sidePortRotations[doodle.id] = this.toDegrees(doodle.rotation);
				}

				if (this.toDegrees(doodle.rotation) != this.sidePortRotations[doodle.id]) {
					this.sidePortRotations[doodle.id] = this.toDegrees(doodle.rotation);
					this.followSurgeon = false;
				}
				break;
			case 'Surgeon':
				if (this.surgeonPosition == null) {
					this.surgeonPosition = this.toDegrees(doodle.rotation);
				}

				if (this.toDegrees(doodle.rotation) != this.surgeonPosition) {
					this.syncIncisionAndSidePortsWithSurgeon(this.surgeonPosition,this.toDegrees(doodle.rotation));
					this.surgeonPosition = this.toDegrees(doodle.rotation);
				}
				break;
		}
	},

	toDegrees : function(radians) {
		return parseInt(radians * 180 / Math.PI);
	},

	toRadians : function(degrees) {
		return (degrees * Math.PI / 180);
	},

	// Grabs the incision site and sideport doodles and binds them to class properties so they can be manouvuered around the eydraw in sync with the surgeon
	// Or rotated in sync when left/right eye is selected

	selectIncisionAndSidePorts : function() {
		this.phakoDoodle = this.getDoodle('PhakoIncision');

		/* Grab the sideport doodles. One sideport will always be 90 degrees counter-clockwise of the incision, the other will be 90 degrees clockwise from it */
		/* sidePort1 needs to be the 90-counterclockwise sideport and sidePort2 the clockwise one */

		var sidePorts = this.getDoodles('SidePort');

		if (!this.sidePort1 || !this.sidePort2) {
			for (var i in sidePorts) {
				if (this.toDegrees(sidePorts[i].rotation) == 360) {
					sidePorts[i].rotation = this.toRadians(0);
				}

				if (this.addDegrees(this.toDegrees(sidePorts[i].rotation), 90) == this.toDegrees(this.phakoDoodle.rotation)) {
					this.sidePort1 = sidePorts[i];
				} else {
					this.sidePort2 = sidePorts[i];
				}
			}
		}
	},

	addDegrees : function(value, add) {
		value += add;
		if (value >= 360) value -= 360;
		return parseInt(value);
	},

	subDegrees : function(value, sub) {
		value -= sub;
		if (value <0) value = 360 - (0-value);
		return parseInt(value);
	},

	checkWhetherIncisionShouldFollow : function() {
		if (!this.followSurgeon) return false;

		this.surgeonDoodle = this.getDoodle('Surgeon');

		if (this.followSurgeon && this.surgeonDoodle.isSelected) {
			return true;
		}

		this.selectIncisionAndSidePorts();

		if (this.toDegrees(this.phakoDoodle.rotation) != this.surgeonPosition) {
			return this.followSurgeon = false;
		}

		if (this.sidePort1 == null || this.sidePort2 == null) {
			return this.followSurgeon = false;
		}

		return this.followSurgeon = true;
	},

	syncIncisionAndSidePortsWithSurgeon : function(oldPos, newPos) {
		if (this.checkWhetherIncisionShouldFollow()) {
			if (oldPos == 0) {
				this.incisionDirection = (newPos == 315) ? 1 : 0;
			} else if (newPos == 0) {
				this.incisionDirection = (oldPos == 315) ? 0 : 1;
			} else {
				this.incisionDirection = (newPos < oldPos) ? 1 : 0;
			}

			this.incisionTarget = newPos;

			this.selectIncisionAndSidePorts();
			this.moveIncisionAndSidePorts();
		}
	},

	/* Calculate the direction for the quickest rotation between two degrees */
	getDirection : function(fromDegrees, toDegrees) {
	},

	getDoodle : function(className) {
		var doodle = this.sanitise(ed_drawing_edit_Position.firstDoodleOfClass(className));

		if (doodle) return doodle;

		return this.sanitise(ed_drawing_edit_Cataract.firstDoodleOfClass(className));
	},

	getDoodles : function(className) {
		var doodles = [];

		var d = ed_drawing_edit_Cataract.allDoodlesOfClass(className);

		for (var i in d) {
			doodles.push(this.sanitise(d[i]));
		}

		return doodles;
	},

	sanitise : function(doodle) {
		if (doodle) {
			/* Negative degrees is just silly */
			if (this.toDegrees(doodle.rotation) <0) {
				doodle.rotation = this.toRadians(360 - (0 - this.toDegrees(doodle.rotation)));
			}
			/* Consistently mandate that 360 = 0 and >360 is not allowed */
			while (this.toDegrees(doodle.rotation) >= 360) {
				doodle.rotation = this.toRadians(this.toDegrees(doodle.rotation) - 360);
			}
		}
		return doodle;
	},

	eye_changed : function(eye_id) {
		if (window.ed_drawing_edit_Cataract !== undefined) {

			this.phakoDoodle = this.getDoodle('PhakoIncision');
			this.surgeonDoodle = this.getDoodle('Surgeon');

			if (eye_id == this.eye_right) {
				if (this.toDegrees(this.phakoDoodle.rotation) == 90) {
					this.checkWhetherIncisionShouldFollow();

					this.incisionTarget = 270;
					this.incisionDirection = 1;

					this.moveIncisionAndSidePorts();
				}

				if (this.toDegrees(this.surgeonDoodle.rotation) == 90) {
					this.surgeonTarget = 270;

					if (Math.floor(Math.random()*this.surgeonAlternateProbability) == this.surgeonMagicNumber) {
						this.surgeonDirection = 2;
					} else {
						this.surgeonDirection = 0;
					}

					if (Math.floor(Math.random()*this.surgeonExtraLoopProbability) == this.surgeonMagicNumber) {
						this.surgeonLoopTarget = 1;
					}

					if (Math.floor(Math.random()*this.surgeonMoveTableProbability) == this.surgeonMagicNumber) {
						this.surgeonMoveTable = 1;
					}

					this.moveSurgeon();
				}

			} else if (eye_id == this.eye_left) {
				if (this.toDegrees(this.phakoDoodle.rotation) == 270) {
					this.checkWhetherIncisionShouldFollow();

					this.incisionTarget = 90;
					this.incisionDirection = 0;

					this.moveIncisionAndSidePorts();
				}

				if (this.toDegrees(this.surgeonDoodle.rotation) == 270) {
					this.surgeonTarget = 90;
					this.surgeonDirection = 1;

					if (Math.floor(Math.random()*this.surgeonExtraLoopProbability) == this.surgeonMagicNumber) {
						this.surgeonLoopTarget = 1;
					}

					if (Math.floor(Math.random()*this.surgeonMoveTableProbability) == this.surgeonMagicNumber) {
						this.surgeonMoveTable = 1;
					}

					this.moveSurgeon();
				}
			}
		}
		
		if (window.ed_drawing_edit_Vitrectomy !== undefined) {
			window.ed_drawing_edit_Vitrectomy.eye = (eye_id == this.eye_right) ? ED.eye.Right : ED.eye.Left;
			window.ed_drawing_edit_Vitrectomy.repaint();
		}
	},

	moveDoodle : function(doodle, rotation) {
		doodle.rotation = this.toRadians(rotation);
		var meridian = 180-rotation;
		if (meridian <0) {
			meridian += 360;
		}
		$('#Element_OphTrOperationnote_Cataract_meridian').val(meridian);
	},

	moveIncisionAndSidePorts : function(initial) {
		if (!initial && this.incisionMoving) return;

		this.incisionMoving = 1;

		if (this.incisionDirection == 0) {
			var pos = this.addDegrees(this.toDegrees(this.phakoDoodle.rotation), this.movePoints);
		} else {
			var pos = this.subDegrees(this.toDegrees(this.phakoDoodle.rotation), this.movePoints);
		}

		if (this.withinRange(pos,this.incisionTarget,this.movePoints)) {
			var newPos = this.incisionTarget;
		} else {
			var newPos = pos;
		}

		this.moveDoodle(this.phakoDoodle, newPos);
		this.moveDoodle(this.sidePort1, this.subDegrees(newPos, 90));
		this.moveDoodle(this.sidePort2, this.addDegrees(newPos, 90));

		this.repaintCataract();

		if (newPos == this.incisionTarget) {
			ed_drawing_edit_Cataract.modified = false;
			this.followSurgeon = true;
			this.incisionMoving = 0;
			$('#Element_OphTrOperationnote_Cataract_eyedraw').val(ed_drawing_edit_Cataract.save());
		} else {
			setTimeout('magic.moveIncisionAndSidePorts(1);', this.timerSpeed);
		}
	},

	withinRange : function(one,two,range) {
		return (one > two) ? ( (one-two) <= range ) : ( (two-one) <= range );
	},

	moveSurgeon : function(initial) {
		if (!initial && this.surgeonMoving) return;

		this.surgeonMoving = 1;

		var pos = this.toDegrees(this.surgeonDoodle.rotation);

		if (this.surgeonDirection == 0 || this.surgeonDirection == 1) {

			if (this.surgeonDirection == 0) {
				pos = this.addDegrees(pos, this.movePoints);
			} else {
				pos = this.subDegrees(pos, this.movePoints);
			}

			if (this.withinRange(pos,this.surgeonTarget,this.movePoints)) {
				var newPos = this.surgeonTarget;
			} else {
				var newPos = pos;
			}

			this.surgeonDoodle.rotation = this.toRadians(newPos);
			this.surgeonDoodle.originY = 0 - (this.surgeonYOffset * Math.sin(this.toRadians(newPos-90)));
			this.surgeonDoodle.originX = this.surgeonYOffset * Math.cos(this.toRadians(newPos-90));

			if (this.surgeonMoveTable) {
				var operatingTable = this.getDoodle('OperatingTable');
				operatingTable.rotation = this.toRadians(((newPos)*2) + 180);
			}

			this.repaintSurgeon();

			if (newPos == this.surgeonTarget) {
				if (this.surgeonLoop < this.surgeonLoopTarget) {
					this.surgeonLoop += 1;

					if (this.surgeonDirection == 0) {
						pos = this.addDegrees(pos, this.movePoints);
					} else {
						pos = this.subDegrees(pos, this.movePoints);
					}

					this.surgeonDoodle.rotation = this.toRadians(pos);

					setTimeout('magic.moveSurgeon(1);', this.timerSpeed);
				} else {
					ed_drawing_edit_Position.modified = false;
					this.surgeonLoopTarget = this.surgeonLoop = 0;
					this.followSurgeon = true;
					this.surgeonMoveTable = 0;
					this.surgeonMoving = 0;
					$('#Element_OphTrOperationnote_Cataract_eyedraw2').val(ed_drawing_edit_Position.save());
				}
			} else {
				setTimeout('magic.moveSurgeon(1);', this.timerSpeed);
			}

		} else if (this.surgeonDirection == 2) {

			pos = this.subDegrees(pos, this.movePoints);

			if (this.withinRange(pos,this.surgeonTarget,this.movePoints)) {
				var newPos = this.surgeonTarget;
				var y_offset = 0;
			} else {
				var newPos = pos;
				var y_offset = (pos == 180) ? 0 : (pos > 180) ? (pos-180) - (pos-180) /2 : (180-pos) - (180-pos) /2;
			}

			this.surgeonDoodle.rotation = this.toRadians(newPos);
			this.surgeonDoodle.originY = y_offset + (this.surgeonYOffset * Math.sin(this.toRadians(newPos+90)));
			this.surgeonDoodle.originX = this.surgeonYOffset * Math.cos(this.toRadians(newPos-90));

			this.repaintSurgeon();

			if (newPos == this.surgeonTarget) {
				if (this.surgeonLoop < this.surgeonLoopTarget) {
					this.surgeonLoop += 1;
					this.surgeonDoodle.rotation = this.toRadians(this.subDegrees(pos, this.movePoints));
					setTimeout('magic.moveSurgeon(1);', this.timerSpeed);
				} else {
					ed_drawing_edit_Position.modified = false;
					this.surgeonLoopTarget = this.surgeonLoop = 0;
					this.surgeonMoving = 0;
				}
			} else {
				setTimeout('magic.moveSurgeon(1);', this.timerSpeed);
			}
		}
	},

	setDoodleParameter : function(className, param, value) {
		ed_drawing_edit_Cataract.setParameterForDoodleOfClass(className, param, value);
	},

	repaintCataract : function() {
		ed_drawing_edit_Cataract.repaint();
	},

	repaintSurgeon : function() {
		ed_drawing_edit_Position.repaint();
	}
}

var magic = new ED_Magic;

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		magic.handleEvent(_drawing.selectedDoodle);
	}
}
