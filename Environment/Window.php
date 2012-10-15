<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * Class \Hoa\Console\Environment\Window.
 *
 * Get some informations about the window (i.e. the terminal).
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Window {

    /**
     * Obtain data from the window environment.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   \Hoa\Console\Environment\Exception
     */
    public static function get ( $data ) {

        switch($data) {

            case 'columns':
                return self::getColumns();
              break;

            case 'lines':
                return self::getLines();
              break;

            default:
                throw new Exception(
                    'Given an unidentified data : %s.', 0, $data);
        }
    }

    /**
     * Get columns number, i.e. the window width.
     *
     * @access  public
     * @return  int
     * @throw   \Hoa\Console\System\Exception
     */
    public static function getColumns ( ) {

        if(OS_WIN) {

            $modecon = \Hoa\Console\System::execute('mode con');
            $modecon = explode("\n", trim($modecon));
            $width   = trim($modecon[3]);
            preg_match('#.*?\s*:\s*([0-9]+)#', $width, $matches);
            $out     = (int) $matches[1];
        }
        else {

            $out     = (int) trim(\Hoa\Console\System::execute('echo $COLUMNS'));

            if(empty($out)) {

                try {

                    $out   = trim(\Hoa\Console\System::execute('stty -a'));
                    $regex = '#(?:;\s+(?P<c>[0-9]+)\s+columns;)|(?:;\s+columns\s+(?P<d>[0-9]+);)#';

                    if(false !== preg_match($regex, $out, $match))
                        $out = isset($match['c']) ? $match['c'] : $match['d'];
                }
                catch ( \Hoa\Console\Exception $e ) {

                    $out = null;
                }
            }
        }

        if(empty($out))
            $out = 80;

        return $out;
    }

    /**
     * Get lines number, i.e. the window height.
     *
     * @access  public
     * @return  int
     * @throw   \Hoa\Console\System\Exception
     */
    public static function getLines ( ) {

        if(OS_WIN) {

            $modecon = \Hoa\Console\System::execute('mode con');
            $modecon = explode("\n", trim($modecon));
            $width   = trim($modecon[2]);
            preg_match('#.*?\s*:\s*([0-9]+)#', $width, $matches);
            $out     = (int) $matches[1];
        }
        else {

            $out     = (int) trim(\Hoa\Console\System::execute('echo $LINES'));

            if(empty($out)) {

                $out   = trim(\Hoa\Console\System::execute('stty -a'));
                $regex = '#(?:;\s+(?P<r>[0-9]+)\s+rows;)|(?:;\s+rows\s+(?P<s>[0-9]+);)#';

                if(false !== preg_match($regex, $out, $match))
                    $out = isset($match['r']) ? $match['r'] : $match['s'];
            }
        }

        if(empty($out))
            $out = 120;

        return $out;
    }
}

}
