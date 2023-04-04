<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\seeders;

use OE\concerns\InteractsWithApp;
use OE\contracts\ProvidesApplicationContext;
use OE\seeders\contracts\Seedable;
use OE\seeders\traits\HasSeederAttributes;

/**
 * An abstraction to base seeders on to avoid boiler plate repetition for initialisation and attribute
 * handling
 */
abstract class BaseSeeder implements Seedable
{
    use HasSeederAttributes;
    use InteractsWithApp;

    protected ProvidesApplicationContext $app_context;


    public function __construct(ProvidesApplicationContext $app_context)
    {
        $this->app_context = $app_context;
    }
}
