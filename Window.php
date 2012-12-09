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
 * \Hoa\Console\Processus
 */
-> import('Console.Processus');

}

namespace Hoa\Console {

/**
 * Class \Hoa\Console\Window.
 *
 * Allow to manipulate the window:
 *     • setSize;
 *     • getSize;
 *     • moveTo;
 *     • getPosition;
 *     • minimize;
 *     • setTitle;
 *     • getTitle;
 *     • getLabel;
 *     • refresh.
 * Please, see C0 and C1 control codes.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Window {

    /**
     * Set size to X lines and Y columns.
     *
     * @access  public
     * @param   int  $x    X coordinate.
     * @param   int  $y    Y coordinate.
     * @return  void
     */
    public static function setSize ( $x, $y ) {

        // DECSLPP.
        echo "\033[8;" . $x . ";" . $y . "t";
    }

    /**
     * Get current size (x and y) of the window.
     *
     * @access  public
     * @return  array
     */
    public static function getSize ( ) {

        if(OS_WIN) {

            $modecon = explode("\n", ltrim(Processus::execute('mode con')));

            $_y      = trim($modecon[2]);
            preg_match('#[^:]+:\s*([0-9]+)#', $_y, $matches);
            $y       = (int) $matches[1];

            $_x      = trim($modecon[3]);
            preg_match('#[^:]+:\s*([0-9]+)#', $_x, $matches);
            $x       = (int) $matches[1];

            return array(
                'x' => $x,
                'y' => $y
            );
        }

        // DECSLPP.
        echo "\033[18t";

        // Read \033[8;y;xt.
        fread(STDIN, 4); // skip \033, [, 8 and ;.

        $x      = null;
        $y      = null;
        $handle = &$y;

        do {

            $char = fread(STDIN, 1);

            switch($char) {

                case ';':
                    $handle = &$x;
                  break;

                case 't':
                    break 2;

                default:
                    $handle .= $char;
            }

        } while(true);

        return array(
            'x' => (int) $x,
            'y' => (int) $y
        );
    }

    /**
     * Move to X and Y (in pixels).
     *
     * @access  public
     * @param   int  $x    X coordinate.
     * @param   int  $y    Y coordinate.
     * @return  void
     */
    public static function moveTo ( $x, $y ) {

        // DECSLPP.
        echo "\033[3;" . $x . ";" . $y . "t";
    }

    /**
     * Get current position (x and y) of the window (in pixels).
     *
     * @access  public
     * @return  array
     */
    public static function getPosition ( ) {

        // DECSLPP.
        echo "\033[13t";

        // Read \033[3;x;yt.
        fread(STDIN, 4); // skip \033, [, 3 and ;.

        $x      = null;
        $y      = null;
        $handle = &$x;

        do {

            $char = fread(STDIN, 1);

            switch($char) {

                case ';':
                    $handle = &$y;
                  break;

                case 't':
                    break 2;

                default:
                    $handle .= $char;
            }

        } while(true);

        return array(
            'x' => (int) $x,
            'y' => (int) $y
        );
    }

    /**
     * Minimize the window.
     *
     * @access  public
     * @return  void
     */
    public static function minimize ( ) {

        echo "\033[2t";

        return;
    }

    /**
     * Set title.
     *
     * @access  public
     * @param   string  $title    Title.
     * @return  void
     */
    public static function setTitle ( $title ) {

        echo "\033]0;" . $title . "\033\\";

        return;
    }

    /**
     * Get title.
     *
     * @access  public
     * @return  string
     */
    public static function getTitle ( ) {

        // DECSLPP.
        echo "\033[21t";

        // Read \033]l<title>\033\
        fread(STDIN, 3); // skip \033, ] and l.
        $out = null;

        do {

            $char = fread(STDIN, 1);

            if("\033" === $char) {

                $chaar = fread(STDIN, 1);

                if('\\' === $chaar)
                    break;

                $char .= $chaar;
            }

            $out .= $char;

        } while(true);

        return $out;
    }

    /**
     * Get label.
     *
     * @access  public
     * @return  string
     */
    public static function getLabel ( ) {

        // DECSLPP.
        echo "\033[20t";

        // Read \033]L<label>\033\
        fread(STDIN, 3); // skip \033, ] and L.
        $out = null;

        do {

            $char = fread(STDIN, 1);

            if("\033" === $char) {

                $chaar = fread(STDIN, 1);

                if('\\' === $chaar)
                    break;

                $char .= $chaar;
            }

            $out .= $char;

        } while(true);

        return $out;
    }

    /**
     * Refresh the window.
     *
     * @access  public
     * @return  void
     */
    public static function refresh ( ) {

        // DECSLPP.
        echo "\033[7t";
    }
}

}
