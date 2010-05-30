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
 * @subpackage  Hoa_Console_Interface_Text
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Environment_Window
 */
import('Console.Environment.Window');

/**
 * Class Hoa_Console_Interface_Text.
 *
 * This class builts the text layout.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Interface_Text
 */

class Hoa_Console_Interface_Text {

    /**
     * Align the text to left.
     *
     * @const int
     */
    const ALIGN_LEFT   = 0;

    /**
     * Align the text to right.
     *
     * @const int
     */
    const ALIGN_RIGHT  = 1;

    /**
     * Align the text to center.
     *
     * @const int
     */
    const ALIGN_CENTER = 2;



    /**
     * Built column from an array.
     * The array has this structure :
     *   array(
     *       array('Firstname', 'Lastname',   'Love', 'Made'   ),
     *       array('Ivan',      'Enderlin',   'Hoa'            ),
     *       array('Rasmus',    'Lerdorf'                      ),
     *       array(null,        'Berners-Lee', null,  'The Web')
     *   )
     * The cell can have a new-line character (\n).
     * The column can have a global alignement, a horizontal and a vertical
     * padding (this horizontal padding is actually the right padding), and a
     * separator.
     * Separator has this form : 'first-column|second-column|third-column|…'.
     * For example : '|: ', will set a ': ' between the first and second column,
     * and nothing for the other.
     *
     * @access  public
     * @param   Array   $line                 The table represented by an array
     *                                        (see the documentation).
     * @param   int     $alignement           The global alignement of the text
     *                                        in cell.
     * @param   int     $horizontalPadding    The horizontal padding (right
     *                                        padding).
     * @param   int     $verticalPadding      The vertical padding.
     * @param   string  $separator            String where each character is a
     *                                        column separator.
     * @return  string
     */
    public static function columnize ( Array $line,
                                       $alignement         = self::ALIGN_LEFT,
                                       $horizontalPadding  = 2,
                                       $verticalPadding    = 0,
                                       $separator          = null ) {

        if(empty($line))
            return '';

        $separator = explode('|', $separator);
        $nbColumn  = 0;
        $nbLine    = count($line);
        $xtraWidth = 2 * ($verticalPadding + 2); // + separator

        // Get the number of column.
        foreach($line as $key => &$column) {

            if(!is_array($column))
                $column = array(0 => $column);

            $handle = count($column);
            $handle > $nbColumn and $nbColumn = $handle;
        }

        $xtraWidth  += $horizontalPadding * $nbColumn;

        // Get the column width.
        $columnWidth = array_fill(0, $nbColumn, 0);

        for($e = 0; $e < $nbColumn; $e++) {

            for($i = 0; $i < $nbLine; $i++) {

                if(!isset($line[$i][$e]))
                    continue;

                $handle = self::getMaxLineWidth($line[$i][$e]);
                $handle > $columnWidth[$e] and $columnWidth[$e] = $handle;
            }
        }

        // If the sum of each column is greater than the window width, we reduce
        // all greaters columns.
        $envWindow = Hoa_Console_Environment_Window::getColumns();

        while($envWindow <= ($cWidthSum = $xtraWidth + array_sum($columnWidth))) {

            $diff            = $cWidthSum - $envWindow;
            $max             = max($columnWidth) - $xtraWidth;
            $newWidth        = $max - $diff;
            $i               = array_search(max($columnWidth), $columnWidth);
            $columnWidth[$i] = $newWidth;

            foreach($line as $key => &$c)
                if(isset($c[$i]))
                    $c[$i] = self::wordwrap($c[$i], $newWidth);
        }

        // Manage the horizontal right padding.
        $columnWidth     = array_map(
                               create_function(
                                   '$x',
                                   'return $x + ' . (2 * $horizontalPadding) . ';'
                               ),
                               $columnWidth
                           );

        // Prepare the new table, i.e. a new line (\n) must be a new line in the
        // array (structurally meaning).
        $newLine = array();
        foreach($line as $key => $plpl) {

            $i = self::getMaxLineNumber($plpl);
            while($i-- >= 0)
                $newLine[] = array_fill(0, $nbColumn, null);
        }

        $yek = 0;
        foreach($line as $key => $col) {

            foreach($col as $kkey => $value) {

                if(false === strpos($value, "\n")) {

                    $newLine[$yek][$kkey] = $value;
                    continue;
                }

                foreach(explode("\n", $value) as $foo => $oof)
                    $newLine[$yek + $foo][$kkey] = $oof;
            }

            $i = self::getMaxLineNumber($col);
            $i > 0 and $yek += $i;
            $yek++;
        }

        // Place the column separator.
        foreach($newLine as $key => $col)
            foreach($col as $kkey => $value)
                if(isset($separator[$kkey]))
                    $newLine[$key][$kkey] = $separator[$kkey] .
                                            str_replace(
                                                "\n",
                                                "\n" . $separator[$kkey],
                                                $value
                                            );

        $line   = $newLine;
        unset($newLine);
        $nbLine = count($line);

        // Complete the table with empty cells.
        foreach($line as $key => &$column) {

            $handle = count($column);

            if($nbColumn - $handle > 0)
                $column += array_fill($handle, $nbColumn - $handle, null);
        }

        // Built !
        $out  = null;
        $dash = $alignement === self::ALIGN_LEFT ? '-' : '';
        foreach($line as $key => $handle) {

            $format = null;

            foreach($handle as $i => $hand)
                if(preg_match_all('#(\\e\[[0-9]+m)#', $hand, $match)) {

                    $a = $columnWidth[$i];

                    foreach($match as $m)
                        $a += strlen($m[1]);

                    $format .= '%' . $dash . ($a + floor(count($match) / 2)) . 's';
                }
                else
                    $format .= '%' . $dash . $columnWidth[$i] . 's';

            $format .= str_repeat("\n", $verticalPadding + 1);

            array_unshift($handle, $format);
            $out .= call_user_func_array('sprintf', $handle);
        }

        return $out;
    }

    /**
     * Align a text according a “layer”. The layer width is given in arguments.
     *
     * @access  public
     * @param   string  $text          The text.
     * @param   string  $alignement    The text alignement.
     * @param   int     $width         The layer width.
     * @return  string
     */
    public static function align ( $text,
                                   $alignement = self::ALIGN_LEFT,
                                   $width      = null ) {

        if(null === $width)
            $width = Hoa_Console_Environment_Window::getColumns();

        $out = null;

        switch($alignement) {

            case self::ALIGN_LEFT:
                $out .= sprintf('%-' . $width . 's', self::wordwrap($text, $width));
              break;

            case self::ALIGN_CENTER:
                foreach(explode("\n", self::wordwrap($text, $width)) as $key => $value)
                    $out .= str_repeat(' ', ceil(($width - strlen($value)) / 2)) .
                            $value .  "\n";
              break;
            
            case self::ALIGN_RIGHT:
            default:
                foreach(explode("\n", self::wordwrap($text, $width)) as $key => $value)
                    $out .= sprintf('%' . $width . 's' . "\n", $value);
              break;
        }

        return $out;
    }

    /**
     * Get the maximum line width.
     *
     * @access  protected
     * @param   mixed      $lines    The line (or group of lines).
     * @return  int
     */
    protected static function getMaxLineWidth ( $lines ) {

        if(!is_array($lines))
            $lines = array(0 => $lines);

        $width = 0;

        foreach($lines as $foo => $line)
            foreach(explode("\n", $line) as $fooo => $lin) {

                $lin = preg_replace('#\\e\[[0-9]+m#', '', $lin);
                strlen($lin) > $width and $width = strlen($lin);
            }

        return $width;
    }

    /**
     * Get the maximum line number (count the new-line character).
     *
     * @access  protected
     * @param   mixed      $lines    The line (or group of lines).
     * @return  int
     */
    protected static function getMaxLineNumber ( $lines ) {

        if(!is_array($lines))
            $lines = array(0 => $lines);

        $number = 0;

        foreach($lines as $foo => $line)
            substr_count($line, "\n") > $number and
                $number = substr_count($line, "\n");

        return $number;
    }

    /**
     * My own wordwrap (just force the wordwrap() $cut parameter)..
     *
     * @access  public
     * @param   string  $text     Text to wrap.
     * @param   int     $width    Line width.
     * @param   string  $break    String to make the break.
     * @return  string
     */
    public static function wordwrap ( $text, $width = null, $break = "\n" ) {

        if(null === $width)
            $width = Hoa_Console_Environment_Window::getColumns();

        return wordwrap($text, $width, $break, true);
    }

    /**
     * Underline with a special string.
     *
     * @access  public
     * @param   string  $text       The text to underline.
     * @param   string  $pattern    The string used to underline.
     * @return  string
     */
    public static function underline ( $text, $pattern = '*' ) {

        $text = explode("\n", $text);
        $card = strlen($pattern);

        foreach($text as $key => &$value) {

            $i   = -1;
            $max = strlen($value);
            while($value{++$i} == ' ' && $i < $max);

            $underline = str_repeat(' ', $i) .
                         str_repeat($pattern, strlen(trim($value)) / $card) .
                         str_repeat(' ', strlen($value) - $i - strlen(trim($value)));

            $value .= "\n" . $underline;
        }

        return implode("\n", $text);
    }
}
