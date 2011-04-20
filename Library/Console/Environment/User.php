<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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

from('Hoa')

/**
 * \Hoa\Console\Environment\Exception
 */
-> import('Console.Environment.Exception')

/**
 * \Hoa\Console\System
 */
-> import('Console.System.~');

}

namespace Hoa\Console\Environment {

/**
 * Class \Hoa\Console\Environment\User.
 *
 * Get some informations about the user.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class User {

    /**
     * Obtain data from the user environment.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   \Hoa\Console\Environment\Exception
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
                throw new Exception(
                    'Given an unidentified data : %s.', 0, $data);
        }
    }

    /**
     * Get the user ID.
     *
     * @access  public
     * @return  int
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUid ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = (int) trim(\Hoa\Console\System::execute('id -u'));

        if(empty($out))
            $out = null;

        return $out;
    }

    /**
     * Get the user name.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getUser ( ) {

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('id -un'));

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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getGid ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = explode(' ', trim(\Hoa\Console\System::execute('id -G ' . $user)));

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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getGroup ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = explode(' ', \Hoa\Console\System::execute('id -Gn ' . $user));

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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getEffectiveGid ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = (int) \Hoa\Console\System::execute('id -g ' . $user);

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
     * @throw   \Hoa\Console\System\Exception
     * @todo    Make this for our favorite OS, I called Windows !
     */
    public static function getEffectiveGroup ( $user = null ) {

        if(null === $user)
            $user = self::getUid();

        if(OS_WIN) {

            $out = null;
        }
        else
            $out = trim(\Hoa\Console\System::execute('id -gn ' . $user));

        if(empty($out))
            $out = null;

        return $out;
    }
}

}
