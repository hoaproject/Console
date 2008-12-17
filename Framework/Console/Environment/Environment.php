<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Environment
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Environment_Exception
 */
import('Console.Environment.Exception');

/**
 * Hoa_Console_Environment_Interface
 */
import('Console.Environment.Interface');

/**
 * Hoa_Console_Environment_Window
 */
import('Console.Environment.Window');

/**
 * Hoa_Console_Environment_User
 */
import('Console.Environment.User');

/**
 * Hoa_Console_Environment_System
 */
import('Console.Environment.System');

/**
 * Class Hoa_Console_Environment.
 *
 * Dispatch the asked data through the get() method.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Environment
 */

class Hoa_Console_Environment implements Hoa_Console_Environment_Interface {

    /**
     * Obtain data from the environment.
     * Data has the form : group.key, where the group is the class, and key
     * depends of the class (should be a method for example).
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     */
    public static function get ( $data ) {

        @list($group, $key) = explode('.', $data);

        if(null === $key)
            throw new Hoa_Console_Environment_Exception(
                'No key was found in the data %s. Must precise : group.key, not ' .
                'only group.', 0, $data);

        if(empty($group))
            throw new Hoa_Console_Environment_Exception(
                'The group in data %s must not be empty. Must precise : ' . 
                'group.key, not only key.', 1, $data);

        if(empty($key))
            throw new Hoa_Console_Environment_Exception(
                'The key in data %s must not be empty. Must precise : ' . 
                'group.key, not only group.', 2, $data);

        switch($group) {

            case 'window':
                return Hoa_Console_Environment_Window::get($key);
              break;

            case 'user':
                return Hoa_Console_Environment_User::get($key);
              break;

            case 'system':
                return Hoa_Console_Environment_System::get($key);
              break;

            default:
                throw new Hoa_Console_Environment_Exception(
                    'Unknown group %s.', 3, $group);
        }

        return null;
    }
}
