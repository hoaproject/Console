<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
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
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console_Exception
 */
import('Console.Exception');

/**
 * Hoa_Console_Dispatcher
 */
import('Console.Dispatcher');

/**
 * Hoa_Console_Core_Io
 */
import('Console.Core.Io', true);

/**
 * Hoa_Console_Interface_Style
 */
import('Console.Interface.Style');

/**
 * Special characters.
 * Please, see: http://www.opengroup.org/onlinepubs/007908799/xbd/termios.html#tag_008_001_009
 * HC means Hoa Console.
 */
_define('HC_SUCCESS',  1);
_define('HC_EXIT',     2);
_define('HC_ERROR',    4);
_define('HC_START',    8);
_define('HC_STOP',    16);

/**
 * Class Hoa_Console.
 *
 * This class get and set the Hoa_Console parameters, and start the dispatch.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 */

class Hoa_Console implements Hoa_Framework_Parameterizable {

    /**
     * Singleton.
     *
     * @var Hoa_Console object
     */
    private static $_instance = null;

    /**
     * Whether exception should be thrown out from console.
     *
     * @var Hoa_Console bool
     */
    protected $throwException = false;

    /**
     * The request object.
     *
     * @var Hoa_Console_Request object
     */
    protected $_request       = null;

    /**
     * The Hoa_Console parameters.
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters      = null;



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    private function __construct ( Array $parameters = array() ) {

        $this->_parameters = new Hoa_Framework_Parameter(
            $this,
            array(
                'group'   => 'main',
                'command' => 'welcome',
                'style'   => 'default'
            ),
            array(
                'command.class'     => '(:command:U:)Command',
                'command.file'      => '(:command:U:).php',
                'command.directory' => 'hoa://Data/Bin/Command/(:group:U:)',

                'cli.separator'     => ':',
                'cli.longonly'      => false,

                'prompt.prefix'     => '',
                'prompt.symbol'     => '> ',

                'style.class'       => '(:style:U:)Style',
                'style.file'        => '(:style:U:).php',
                'style.directory'   => 'hoa://Data/Bin/Style',

                'command.php'       => 'php',
                'command.browser'   => 'open'
            )
        );

        $this->setParameters($parameters);
    }

    /**
     * Singleton : get instance of Hoa_Console.
     *
     * @access  public
     * @param   array   $parameters    Parameters.
     * @return  void
     */
    public static function getInstance ( Array $parameters = array() ) {

        if(null === self::$_instance)
            self::$_instance = new self($parameters);

        return self::$_instance;
    }

    /**
     * Set many parameters to a class.
     *
     * @access  public
     * @param   array   $in      Parameters to set.
     * @return  void
     * @throw   Hoa_Exception
     */
    public function setParameters ( Array $in ) {

        return $this->_parameters->setParameters($this, $in);
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   Hoa_Exception
     */
    public function getParameters ( ) {

        return $this->_parameters->getParameters($this);
    }

    /**
     * Set a parameter to a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @param   mixed   $value    Value.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function setParameter ( $key, $value ) {

        return $this->_parameters->setParameter($this, $key, $value);
    }

    /**
     * Get a parameter from a class.
     *
     * @access  public
     * @param   string  $key      Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getParameter ( $key ) {

        return $this->_parameters->getParameter($this, $key);
    }

    /**
     * Get a formatted parameter from a class (i.e. zFormat with keywords and
     * other parameters).
     *
     * @access  public
     * @param   string  $key    Key.
     * @return  mixed
     * @throw   Hoa_Exception
     */
    public function getFormattedParameter ( $key ) {

        return $this->_parameters->getFormattedParameter($this, $key);
    }

    /**
     * Run the dispatcher.
     *
     * @access  public
     * @return  Hoa_Console
     * @throw   Hoa_Console_Exception
     */
    public function dispatch ( ) {

        try {

            $dispatcher = new Hoa_Console_Dispatcher($this->_parameters);
            $this->_parameters->shareWith(
                $this,
                $dispatcher,
                Hoa_Framework_Parameter::PERMISSION_READ |
                Hoa_Framework_Parameter::PERMISSION_SHARE
            );
            $dispatcher->dispatch();
        }
        catch ( Hoa_Console_Exception $e ) {

            if(false !== $this->getThrowException())
                throw $e;

            Hoa_Console_Core_Io::cout(
                Hoa_Console_Interface_Style::styleExists('_exception')
                    ? Hoa_Console_Interface_Style::stylize(
                          $e->getFormattedMessage(),
                          '_exception'
                      )
                    : $e->getFormattedMessage()
            );

            $expand = Hoa_Console_Core_Io::cin(
                          'Expand the exception ?',
                          Hoa_Console_Core_Io::TYPE_YES_NO
                      );

            if(true === $expand)
                Hoa_Console_Core_Io::cout(
                    Hoa_Console_Interface_Style::styleExists('_exception')
                        ? Hoa_Console_Interface_Style::stylize(
                              $e->raiseError(Hoa_Exception::ERROR_RETURN),
                              '_exception'
                          )
                        : $e->raiseError(Hoa_Exception::ERROR_RETURN)
                );
        }

        return $this;
    }

    /**
     * A shortcut to import style.
     *
     * @access  public
     * @param   string  $style    The style filename.
     * @return  Hoa_Console
     * @throw   Hoa_Console_Exception
     */
    public function importStyle ( $style ) {

        $this->_parameters->setKeyword($this, 'style', $style);
        $class     = $this->getFormattedParameter('style.class');
        $file      = $this->getFormattedParameter('style.file');
        $directory = $this->getFormattedParameter('style.directory');
        $path      = $directory . '/' . $file;

        if(!file_exists($path))
            throw new Hoa_Console_Exception(
                'The style %s is not found at %s.', 0, array($style, $path));

        require_once $path;

        $sheet     = new $class();

        if(!($sheet instanceof Hoa_Console_Interface_Style))
            throw new Hoa_Console_Exception(
                'The style %s must extend the Hoa_Console_Interface_Style class.',
                1, $class);

        $sheet->import();

        return $this;
    }

    /**
     * Set the parameter throwException. If it is set, all exception will be
     * thrown out of the console, else a simplement message (from the method
     * raiseError()) will be print.
     *
     * @access  public
     * @param   bool    $throw    Throw exception or not ?
     * @return  bool
     */
    public function setThrowException ( $throw = false ) {

        $old                  = $this->throwException;
        $this->throwException = $throw;
    }

    /**
     * Get the parameter throwException.
     *
     * @access  public
     * @return  bool
     */
    public function getThrowException ( ) {

        return $this->throwException;
    }

    /**
     * If the Hoa_Console package is used in standalone mode or not, i.e. if the
     * script is running from :
     *     $ php <script>.php
     * or
     *     $ ./<script>.php
     * Always return true for now.
     *
     * @note  Will be deleted one day …
     */
    public static function isStandalone ( ) {

        return true;
    }
}
