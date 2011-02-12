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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Console\Environment\Exception
 */
-> import('Console.Environment.Exception')

/**
 * \Hoa\Console\Environment\Window
 */
-> import('Console.Environment.Window')

/**
 * \Hoa\Console\Environment\User
 */
-> import('Console.Environment.User')

/**
 * \Hoa\Console\Environment\System
 */
-> import('Console.Environment.System');

}

namespace Hoa\Console\Environment {

/**
 * Class \Hoa\Console\Environment.
 *
 * Dispatch the asked data through the get() method.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Environment {

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
            throw new Exception(
                'No key was found in the data %s. Must precise : group.key, not ' .
                'only group.', 0, $data);

        if(empty($group))
            throw new Exception(
                'The group in data %s must not be empty. Must precise : ' . 
                'group.key, not only key.', 1, $data);

        if(empty($key))
            throw new Exception(
                'The key in data %s must not be empty. Must precise : ' . 
                'group.key, not only group.', 2, $data);

        switch($group) {

            case 'window':
                return \Hoa\Console\Environment\Window::get($key);
              break;

            case 'user':
                return \Hoa\Console\Environment\User::get($key);
              break;

            case 'system':
                return \Hoa\Console\Environment\System::get($key);
              break;

            default:
                throw new Exception(
                    'Unknown group %s.', 3, $group);
        }

        return null;
    }
}

}
