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
 * @subpackage  Hoa_Console_Interface_Style
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Interface_Exception
 */
import('Console.Interface.Exception');

/**
 * Class Hoa_Console_Interface_Style.
 *
 * Manage the text style : color of foreground, background, or text style (bold,
 * underscore etc.).
 * This class also allows to register and recover styles.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Interface_Style
 */

abstract class Hoa_Console_Interface_Style {

    /**
     * The eigth foreground console color.
     *
     * @const int
     */
    const COLOR_FOREGROUND_BLACK  = 30;
    const COLOR_FOREGROUND_RED    = 31;
    const COLOR_FOREGROUND_GREEN  = 32;
    const COLOR_FOREGROUND_YELLOW = 33;
    const COLOR_FOREGROUND_BLUE   = 34;
    const COLOR_FOREGROUND_VIOLET = 35;
    const COLOR_FOREGROUND_CYAN   = 36;
    const COLOR_FOREGROUND_WHITE  = 37;

    /**
     * The eigth background console color.
     *
     * @const int
     */
    const COLOR_BACKGROUND_BLACK  = 40;
    const COLOR_BACKGROUND_RED    = 41;
    const COLOR_BACKGROUND_GREEN  = 42;
    const COLOR_BACKGROUND_YELLOW = 43;
    const COLOR_BACKGROUND_BLUE   = 44;
    const COLOR_BACKGROUND_VIOLET = 45;
    const COLOR_BACKGROUND_CYAN   = 46;
    const COLOR_BACKGROUND_WHITE  = 47;

    /**
     * The text styles.
     *
     * @const int
     */
    const TEXT_BOLD               = 1;
    const TEXT_UNDERLINE          = 4;
    const TEXT_BLINK              = 5;
    const TEXT_REVERSE            = 7;
    const TEXT_CONCEAL            = 8;

    /**
     * The style is important, do not overwrite.
     *
     * @const bool
     */
    const STYLE_IMPORTANT         = false;

    /**
     * The style can be overwrite by an other.
     *
     * @const bool
     */
    const STYLE_OVERWRITE         = true;

    /**
     * Register of style.
     *
     * @var Hoa_Console_Interface_Style array
     */
    protected static $style = array();



    /**
     * Stylize a text.
     *
     * @access  public
     * @param   string  $text       The text to stylize.
     * @param   mixed   $options    Should be an integer or an array of integer
     *                              (given by COLOR_* or TEXT_* constants
     *                              combinaisons), or a style name.
     * @return  string
     */
    public static function stylize ( $text = null, $options = array() ) {

        // Disable colors if not supported (windows or non tty console).
        if(OS_WIN || !function_exists('posix_isatty'))
            return $text;

        if(!is_array($options))
            if(true === self::styleExists($options))
                $options = self::getStyle($options);
            elseif(empty($options))
                return $text;
            else
                $options = array(0 => $options);

        return "\033[" . implode(';', $options) . 'm' . $text . "\033[0m";
    }

    /**
     * Add styles.
     *
     * @access  public
     * @param   array   $styles    Styles.
     * @return  array
     * @throw   Hoa_Console_Interface_Exception
     */
    public static function addStyles ( Array $styles ) {

        foreach($styles as $name => $options)
            self::addStyle($name, $options);
    }

    /**
     * Add a style.
     *
     * @access  public
     * @param   string  $name         The style name.
     * @param   mixed   $options      Should be an integer or an array of integer.
     * @param   bool    $overwrite    Overwrite style or not.
     * @return  array
     * @throw   Hoa_Console_Interface_Exception
     */
    public static function addStyle ( $name, $options,
                                      $overwrite = self::STYLE_IMPORTANT ) {

        if(!is_array($options))
            $options   = array(0 => $options);

        if(isset($options['!important'])) {

            $overwrite = $options['!important'];
            unset($options['!important']);
        }

        if(true === self::styleExists($name) && self::STYLE_IMPORTANT === $overwrite)
            throw new Hoa_Console_Interface_Exception(
                'The %s style already exists, ask to do not overwrite if exists.',
                0, $name);

        self::$style[$name] = $options;
    }

    /**
     * Check if a style already exists.
     *
     * @access  public
     * @param   string  $name    The style name.
     * @return  bool
     */
    public static function styleExists ( $name ) {

        return isset(self::$style[$name]);
    }

    /**
     * Get a specific style.
     *
     * @access  protected
     * @param   string     $name    The style name.
     * @return  array
     * @throw   Hoa_Console_Interface_Exception
     */
    protected static function getStyle ( $name ) {

        if(false === self::styleExists($name))
            throw new Hoa_Console_Interface_Exception(
                'The %s style does not exists.', 1, $name);

        return self::$style[$name];
    }

    /**
     * If this class is extended for adding style, it would be great if all
     * style implement the same method for adding a group a style.
     *
     * @access  public
     * @return  void
     */
    abstract public function import ( );
}
