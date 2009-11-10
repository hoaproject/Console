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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Console_Environment_User
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
 * Hoa_Console_System
 */
import('Console.System.~');

/**
 * Class Hoa_Console_Environment_User.
 *
 * Get some informations about the user.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Environment_User
 */

class Hoa_Console_Environment_User implements Hoa_Console_Environment_Interface {

    /**
     * Obtain data from the user environment.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   Hoa_Console_Environment_Exception
     */
    public static function get ( $data ) {

        switch($data) {

            case 'uid':
                return self::getUid();
              break;

            case 'user':
                return self::getUser();
              break;

            case 'gid':
                return self::getGid();
              break;

            case 'group':
                return self::getGroup();
              break;

            case 'effectiveGid':
                return self::getEffectiveGid();
              break;

            case 'effectiveGroup':
                return self::getEffectiveGroup();
              break;

            default:
                throw new Hoa_Console_Environment_Exception(
                    'Given an unidentified data : %s.', 0, $data);
        }
    }

    /**
     * Get the user ID.
     *
     * @access  public
     * @return  int
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUid ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = (int) trim(Hoa_Console_System::execute('id -u'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the user name.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUser ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('id -un'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get groups ID.
     *
     * @access  public
     * @param   mixed   $user    Specify a username or user ID. If set to null,
     *                           the current user ID will be selected.
     * @return  array
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getGid ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = explode(' ', trim(Hoa_Console_System::execute('id -G ' . $user)));

        if(empty($out))
            $out = array();

        return $out;
    }

    /**
     * Get groups name.
     *
     * @access  public
     * @param   mixed   $user    Specify a username or user ID. If set to null,
     *                           the current user ID will be selected.
     * @return  array
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getGroup ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = explode(' ', Hoa_Console_System::execute('id -Gn ' . $user));

        if(empty($out))
            $out = array();

        return $out;
    }

    /**
     * Get the effective group ID.
     *
     * @access  public
     * @param   mixed   $user    Specify a username or user ID. If set to null,
     *                           the current user ID will be selected.
     * @return  int
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getEffectiveGid ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = (int) Hoa_Console_System::execute('id -g ' . $user);

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the effective group name.
     *
     * @access  public
     * @param   mixed   $user    Specify a username or user ID. If set to null,
     *                           the current user ID will be selected.
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getEffectiveGroup ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('id -gn ' . $user));

        if(empty($out))
            $out = null;

        return $out;
    }
}
