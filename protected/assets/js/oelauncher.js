/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
* Helper function for use with the OpenEyes-DescktopAppLauncher utility. (https://github.com/openeyes/Openeyes-DesktopAppLauncher)
* The utility can be called directly by url using the oelauncher: custom protocol. E.g.,
*     <a href='oelauncher:COMPlog'>click me</a>
* Or use this js helper to call oelauncher(). E.g, oelauncher('COMPlog');
* Any parameters needed for the commandline should be separated by '/'. e.g. 'oelauncher:appName/param1/param2/param3', or oelauncher('appName/param1/param2/param3')
**/
 function oelauncher(launchcommand){

     /**
     * As the patient hospital number is unavailable at time of load in some pages/menu_bar_items
     * this check will automatically append the patient number if it is available, otherwise will just open forum to the main page
     **/
     if (launchcommand==('forum') || launchcommand === 'imagenet'){
        try{ launchcommand+="/"+OE_patient_hosnum;} catch(err) {};
     }
     // Call the oelauncher: url protocol, without leaving the page or causing a pop-up
     setTimeout( function() { window.location.href="oelauncher:" + launchcommand;}, 500);

    return false;
 }
