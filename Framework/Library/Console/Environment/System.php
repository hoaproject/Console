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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Console_Environment_System
 *
 */

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
 * Class Hoa_Console_Environment_System.
 *
 * Get some informations about the system.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Environment_System
 */

class Hoa_Console_Environment_System implements Hoa_Console_Environment_Interface {

    /**
     * Obtain data from the system environment.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   Hoa_Console_Environment_Exception
     */
    public static function get ( $data ) {

        switch($data) {

            case 'uptime':
                return self::getUptime();
              break;

            case 'uname':
                return self::getUname();
              break;

            case 'nodename':
                return self::getNodename();
              break;

            case 'version':
                return self::getVersion();
              break;

            case 'release':
                return self::getRelease();
              break;

            default:
                throw new Hoa_Console_Environment_Exception(
                    'Given an unidentified data : %s.', 0, $data);
        }
    }

    /**
     * Get the uptime.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUptime ( ) {

        if(OS_WIN) {

            $out     = null;
        }
        else {

            $handle  = trim(Hoa_Console_System::execute('uptime'));
            if(0   === preg_match('#up ([^,]+),#', $handle, $matches))
                $out = null;
            else
                $out = trim($matches[1]);
        }

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the system name.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUname ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('uname -s'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the system nodename, i.e. may be a name that the system is known by
     * to a communications network.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getNodename ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('uname -n'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the system version.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getVersion ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('uname -v'));

        if(empty($out))
            $out = null;

        return $out;
    }
    /**
     * Get the system release.
     *
     * @access  public
     * @return  string
     * @throw   Hoa_Console_System_Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getRelease ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(Hoa_Console_System::execute('uname -r'));

        if(empty($out))
            $out = null;

        return $out;
    }
}
