/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

function appendText(selector, appendToItem) {
	var textValue = selector[0].options[selector[0].selectedIndex].text;
	// ignore the empty selector
	if ('-' != textValue && '' != textValue) {
		// grab the text
		appendValue = appendToItem.val();
		// if we're adding onto existing text, add a comma
		if (appendValue && '' != appendValue) {
			textValue = appendValue + ', ' + textValue;
		// otherwise just make sure the first letter is capitalized
		} else {
			textValue = textValue.charAt(0).toUpperCase() + textValue.slice(1);
		}
		// add it to the textarea
		appendToItem.val(textValue);
	}
}
