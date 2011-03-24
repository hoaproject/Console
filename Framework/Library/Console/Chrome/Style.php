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

from('Hoa')

/**
 * \Hoa\Console\Chrome\Exception
 */
-> import('Console.Chrome.Exception');

}

namespace Hoa\Console\Chrome {

/**
 * Class \Hoa\Console\Chrome\Style.
 *
 * Manage the text style: color of foreground, background, or text style (bold,
 * underscore etc.).
 * This class also allows to register and recover styles.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Style {

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
     * @var \Hoa\Console\Chrome\Style array
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
     * @throw   \Hoa\Console\Chrome\Exception
     */
    public static function addStyles ( Array $styles ) {

        foreach($styles as $name => $options)
            self::addStyle($name, $options);

        return;
    }

    /**
     * Add a style.
     *
     * @access  public
     * @param   string  $name         The style name.
     * @param   mixed   $options      Should be an integer or an array of integer.
     * @param   bool    $overwrite    Overwrite style or not.
     * @return  array
     * @throw   \Hoa\Console\Chrome\Exception
     */
    public static function addStyle ( $name, $options,
                                      $overwrite = self::STYLE_IMPORTANT ) {

        if(!is_array($options))
            $options   = array(0 => $options);

        if(isset($options['!important'])) {

            $overwrite = $options['!important'];
            unset($options['!important']);
        }

        if(   true === self::styleExists($name)
           && self::STYLE_IMPORTANT === $overwrite)
            throw new Exception(
                'The %s style already exists, ask to do not overwrite if exists.',
                0, $name);

        self::$style[$name] = $options;

        return;
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
     * @throw   \Hoa\Console\Chrome\Exception
     */
    protected static function getStyle ( $name ) {

        if(false === self::styleExists($name))
            throw new Exception(
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

}
