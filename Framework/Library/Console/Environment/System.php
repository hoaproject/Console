<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

/**
 * \Hoa\Console\Environment\Exception
 */
import('Console.Environment.Exception')

/**
 * \Hoa\Console\System
 */
-> import('Console.System.~');

}

namespace Hoa\Console\Environment {

/**
 * Class \Hoa\Console\Environment\System.
 *
 * Get some informations about the system.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class System {

    /**
     * Obtain data from the system environment.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   \Hoa\Console\Environment\Exception
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
                throw new Exception(
                    'Given an unidentified data : %s.', 0, $data);
        }
    }

    /**
     * Get the uptime.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUptime ( ) {

        if(OS_WIN) {

            $out     = null;
        }
        else {

            $handle  = trim(\Hoa\Console\System::execute('uptime'));
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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUname ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('uname -s'));

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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getNodename ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('uname -n'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the system version.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getVersion ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('uname -v'));

        if(empty($out))
            $out = null;

        return $out;
    }
    /**
     * Get the system release.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getRelease ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('uname -r'));

        if(empty($out))
            $out = null;

        return $out;
    }
}

}
