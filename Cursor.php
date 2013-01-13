<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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

namespace Hoa\Console {

/**
 * Class \Hoa\Console\Cursor.
 *
 * Allow to manipulate the cursor:
 *     • move;
 *     • moveTo;
 *     • save;
 *     • restore;
 *     • clear;
 *     • scroll;
 *     • hide;
 *     • show;
 *     • getPosition;
 *     • setStyle;
 *     • colorize;
 *     • changeColor;
 *     • bip.
 * Please, see C0 and C1 control codes.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Cursor {

    /**
     * Move the cursor.
     * Steps can be:
     *     • u, up,    ↑ : move to the previous line;
     *     • U, UP       : move to the first line;
     *     • r, right, → : move to the next column;
     *     • R, RIGHT    : move to the last column;
     *     • d, down,  ↓ : move to the next line;
     *     • D, DOWN     : move to the last line;
     *     • l, left,  ← : move to the previous column;
     *     • L, LEFT     : move to the first column.
     * Steps can be concatened by a single space if $repeat is equal to 1.
     *
     * @access  public
     * @param   string  $steps     Steps.
     * @param   int     $repeat    How many times do we move?
     * @return  void
     */
    public static function move ( $steps, $repeat = 1 ) {

        if(OS_WIN)
            return;

        if(1 > $repeat)
            return;
        elseif(1 === $repeat)
            $handle = explode(' ', $steps);
        else
            $handle = explode(' ', $steps, 1);

        foreach($handle as $step)
            switch($step) {

                // CUU.
                case 'u':
                case 'up':
                case '↑':
                    echo "\033[" . $repeat . 'A';
                  break;

                case 'U':
                case 'UP':
                    static::moveTo(null, 1);
                  break;

                // CUF.
                case 'r':
                case 'right':
                case '→':
                    echo "\033[" . $repeat . 'C';
                  break;

                case 'R':
                case 'RIGHT':
                    static::moveTo(9999);
                  break;

                // CUD.
                case 'd':
                case 'down':
                case '↓':
                    echo "\033[" . $repeat . 'B';
                  break;

                case 'D':
                case 'DOWN':
                    static::moveTo(null, 9999);
                  break;

                // CUB.
                case 'l':
                case 'left':
                case '←':
                    echo "\033[" . $repeat . 'D';
                  break;

                case 'L':
                case 'LEFT':
                    static::moveTo(1);
                  break;
            }

        return;
    }

    /**
     * Move to the line X and the column Y.
     * If null, use the current coordinate.
     *
     * @access  public
     * @param   int  $x    X coordinate.
     * @param   int  $y    Y coordinate.
     * @return  void
     */
    public static function moveTo ( $x = null, $y = null ) {

        if(OS_WIN)
            return;

        if(null === $x || null === $y) {

            $position = static::getPosition();

            if(null === $x)
                $x = $position['x'];

            if(null === $y)
                $y = $position['y'];
        }

        // CUP.
        echo "\033[" . $y . ";" . $x . "H";

        return;
    }

    /**
     * Save current position.
     *
     * @access  public
     * @return  void
     */
    public static function save ( ) {

        if(OS_WIN)
            return;

        // SCP.
        echo "\033[s";

        return;
    }

    /**
     * Restore cursor to the last saved position.
     *
     * @access  public
     * @return  void
     */
    public static function restore ( ) {

        if(OS_WIN)
            return;

        // RCP.
        echo "\033[u";

        return;
    }

    /**
     * Clear the screen.
     * Part can be:
     *     • a, all,   ↕ : clear entire screen and static::move(1, 1);
     *     • u, up,    ↑ : clear from cursor to beginning of the screen;
     *     • r, right, → : clear from cursor to the end of the line;
     *     • d, down,  ↓ : clear from cursor to end of the screen;
     *     • l, left,  ← : clear from cursor to beginning of the screen;
     *     •    line,  ↔ : clear all the line and static::move(1).
     * Parts can be concatenated by a single space.
     *
     * @access  public
     * @param   string  $parts    Parts to clean.
     * @return  void
     */
    public static function clear ( $parts = 'all' ) {

        if(OS_WIN)
            return;

        foreach(explode(' ', $parts) as $part)
            switch($part) {

                // ED.
                case 'a':
                case 'all':
                case '↕':
                    echo "\033[2J";
                    static::moveTo(1, 1);
                  break;

                // ED.
                case 'u':
                case 'up':
                case '↑':
                    echo "\033[1J";
                  break;

                // EL.
                case 'r':
                case 'right':
                case '→':
                    echo "\033[0K";
                  break;

                // ED.
                case 'd':
                case 'down':
                case '↓':
                    echo "\033[0J";
                  break;

                // EL.
                case 'l':
                case 'left':
                case '←':
                    echo "\033[1K";
                  break;

                // EL.
                case 'line':
                case '↔':
                    echo "\r\033[K";
                  break;
            }

        return;
    }

    /**
     * Scroll whole page.
     * Directions can be:
     *     • u, up,    ↑ : scroll whole page up;
     *     • d, down,  ↓ : scroll whole page down.
     * Directions can be concatenated by a single space.
     *
     * @access  public
     * @param   string  $directions    Directions.
     * @reutrn  void
     */
    public static function scroll ( $directions ) {

        if(OS_WIN)
            return;

        $handle = array('up' => 0, 'down' => 0);

        foreach(explode(' ', $directions) as $direction)
            switch($direction) {

                case 'u':
                case 'up':
                case '↑':
                    ++$handle['up'];
                  break;

                case 'd':
                case 'down':
                case '↓':
                    ++$handle['down'];
                  break;
            }

        if(0 < $handle['up'])
            // SU.
            echo "\033[" . $handle['up'] . "S";

        if(0 < $handle['down'])
            // SD.
            echo "\033[" . $handle['up'] . "T";

        return;
    }

    /**
     * Hide the cursor.
     *
     * @access  public
     * @return  void
     */
    public static function hide ( ) {

        if(OS_WIN)
            return;

        // DECTCEM.
        echo "\033[?25l";

        return;
    }

    /**
     * Show the cursor.
     *
     * @access  public
     * @return  void
     */
    public static function show ( ) {

        if(OS_WIN)
            return;

        // DECTCEM.
        echo "\033[?25h";

        return;
    }

    /**
     * Get current position (x and y) of the cursor.
     *
     * @access  public
     * @return  array
     */
    public static function getPosition ( ) {

        if(OS_WIN)
            return;

        // DSR.
        echo "\033[6n";

        // Read \033[y;xR.
        fread(STDIN, 2); // skip \033 and [.

        $x      = null;
        $y      = null;
        $handle = &$y;

        do {

            $char = fread(STDIN, 1);

            switch($char) {

                case ';':
                    $handle = &$x;
                  break;

                case 'R':
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
     * Colorize cursor.
     * Attributes can be:
     *     •  n,         normal           : normal;
     *     •  b,         bold             : bold;
     *     •  u,         underlined       : underlined;
     *     •  bl,        blink            : blink;
     *     •  i,         inverse          : inverse;
     *     • !b,        !bold             : normal weight;
     *     • !u,        !underlined       : not underlined;
     *     • !bl,       !blink            : steady;
     *     • !i,        !inverse          : positive;
     *     • fg(color), foreground(color) : set foreground to “color”;
     *     • bg(color), background(color) : set background to “color”.
     * “color” can be:
     *     • default;
     *     • black;
     *     • red;
     *     • green;
     *     • yellow;
     *     • blue;
     *     • magenta;
     *     • cyan;
     *     • white;
     *     • 0-256 (classic palette).
     * Attributes can be concatenated by a single space.
     *
     * @access  public
     * @param   string  $attributes    Attributes.
     * @return  void
     */
    public static function colorize ( $attributes ) {

        $handle = array();

        foreach(explode(' ', $attributes) as $attribute) {

            switch($attribute) {

                case 'n':
                case 'normal':
                    $handle[] = 0;
                  break;

                case 'b':
                case 'bold':
                    $handle[] = 1;
                  break;

                case 'u':
                case 'underlined':
                    $handle[] = 4;
                  break;

                case 'bl':
                case 'blink':
                    $handle[] = 5;
                  break;

                case 'i':
                case 'inverse':
                    $handle[] = 7;
                  break;

                case '!b':
                case '!bold':
                    $handle[] = 22;
                  break;

                case '!u':
                case '!underlined':
                    $handle[] = 24;
                  break;

                case '!bl':
                case '!blink':
                    $handle[] = 25;
                  break;

                case '!i':
                case '!inverse':
                    $handle[] = 27;
                  break;

                default:
                    if(0 === preg_match('#^([^\(]+)\(([^\)]+)\)$#', $attribute, $m))
                        break;

                    $shift = 0;

                    switch($m[1]) {

                        case 'fg':
                        case 'foreground':
                            $shift = 0;
                          break;

                        case 'bg':
                        case 'background':
                            $shift = 10;
                          break;

                        default:
                          break 2;
                    }

                    $_handle  = 0;
                    $_keyword = true;

                    switch($m[2]) {

                        case 'black':
                            $_handle = 30;
                          break;

                        case 'red':
                            $_handle = 31;
                          break;

                        case 'green':
                            $_handle = 32;
                          break;

                        case 'yellow':
                            $_handle = 33;
                          break;

                        case 'blue':
                            $_handle = 34;
                          break;

                        case 'magenta':
                            $_handle = 35;
                          break;

                        case 'cyan':
                            $_handle = 36;
                          break;

                        case 'white':
                            $_handle = 37;
                          break;

                        case 'default':
                            $_handle = 39;
                          break;

                        default:
                            $_handle  = intval($m[2]);
                            $_keyword = false;
                    }

                    if(true === $_keyword)
                        $handle[] = $_handle + $shift;
                    else
                        $handle[] = (38 + $shift) . ';5;' . $m[2];
            }
        }

        echo "\033[" . implode($handle, ';') . "m";

        return;
    }

    /**
     * Change color number to a specific RGB color.
     *
     * @access  public
     * @param   int  $fromCode    Color number.
     * @param   int  $toColor     RGB color.
     * @return  void
     */
    public static function changeColor ( $fromCode, $toColor ) {

        if(OS_WIN)
            return;

        $red   = ($toColor >> 16) & 255;
        $green = ($toColor >>  8) & 255;
        $blue  =  $tocolor        & 255;

        echo "\033]4;" . $fromCode . ";#" .
             sprintf('%02x%02x%02x', $red, $green, $blue) .
             "\033\\";

        return;
    }

    /**
     * Set cursor style.
     * Style can be:
     *     • b, block,     ▋: block;
     *     • u, underline, _: underline;
     *     • v, vertical,  |: vertical.
     *
     * @access  public
     * @param   int   $style    Style.
     * @param   bool  $blink    Whether the cursor is blink or steady.
     * @return  void
     */
    public static function setStyle ( $style, $blink = true ) {

        if(OS_WIN)
            return;

        switch($style) {

            case 'b':
            case 'block':
            case '▋':
                $_style = 1;
              break;

            case 'u':
            case 'underline':
            case '_':
                $_style = 2;
              break;

            case 'v':
            case 'vertical':
            case '|':
                $_style = 5;
              break;
        }

        if(false === $blink)
            ++$_style;

        // DECSCUSR.
        echo "\033[" . $_style . " q";

        return;
    }

    /**
     * Make a stupid “bip”.
     *
     * @access  public
     * @return  void
     */
    public static function bip ( ) {

        echo "\007";

        return;
    }
}

}
