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
 * Hoa_Console_Request
 */
import('Console.Request');

/**
 * Hoa_Console_Dispatcher
 */
import('Console.Dispatcher');

/**
 * Hoa_Console_Core_Io
 */
import('Console.Core.Io');

/**
 * Hoa_Console_Interface_Style
 */
import('Console.Interface.Style');

/**
 * Special characters.
 * Please, see : http://www.opengroup.org/onlinepubs/007908799/xbd/termios.html#tag_008_001_009
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

class Hoa_Console {

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
     * @var Hoa_Console array
     */
    protected $parameters     = array(
        'system.group.value'        => null,
        'system.group.default'      => 'Main',
        'system.command.value'      => null,
        'system.command.default'    => 'Welcome',
        'system.command.file'       => null,
        'system.command.class'      => null,

        'route.directory'           => 'Command/',
        'route.grpcmd.separator'    => ':',

        'pattern.group'             => '(:Group)',
        'pattern.command.name'      => '(:Command)',
        'pattern.command.file'      => '(:Command)',
        'pattern.command.class'     => '(:Command)Command',

        'prompt.prefix'             => '',
        'prompt.symbol'             => '> ',

        'cli.longonly'              => false,

        'interface.style.directory' => 'Style/',

        'command.php'               => 'php',
        'command.browser'           => 'open'
    );



    /**
     * Singleton, and set parameters.
     *
     * @access  private
     * @param   array    $parameters    Parameters.
     * @return  void
     */
    private function __construct ( Array $parameters = array() ) {

        #IF_DEFINED HOA_STANDALONE
        if(empty($parameters))
            Hoa_Framework::configurePackage(
                'Console', $parameters, Hoa_Framework::CONFIGURATION_DOT);
        #END_IF

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
     * Set parameters.
     *
     * @access  protected
     * @param   array      $parameters    Parameters.
     * @param   array      $recursive     Used for recursive parameters.
     * @return  array
     */
    protected function setParameters ( Array $parameters = array(),
                                             $recursive  = array() ) {

        if($recursive === array()) {
            $array       =& $this->parameters;
            $recursivity = false;
        }
        else {
            $array       =& $recursive;
            $recursivity = true;
        }

        if(empty($parameters))
            return $array;

        foreach($parameters as $option => $value) {

            if(empty($option) || (empty($value) && !is_bool($value)))
                continue;

            if(is_array($value))
                $array[$option] = $this->setParameters($value, $array[$option]);

            else
                $array[$option] = $value;
        }

        return $array;
    }

    /**
     * Get all parameters.
     *
     * @access  protected
     * @return  array
     */
    protected function getParameters ( ) {

        return $this->parameters;
    }

    /**
     * Get a specific parameter.
     *
     * @access  protected
     * @param   string     $parameter    The parameter name.
     * @return  mixed
     * @throw   Hoa_Console_Exception
     */
    protected function getParameter ( $parameter ) {

        if(!isset($this->parameters[$parameter]))
            throw new Hoa_Console(
                'The parameter %s does not exists.', 0, $parameter);

        return $this->parameters[$parameter];
    }

    /**
     * Set the request object, with parameters.
     *
     * @access  protected
     * @return  Hoa_Console_Request
     */
    protected function setRequest ( ) {

        $old            = $this->_request;
        $this->_request = new Hoa_Console_Request(
                              $this->getParameters()
                          );

        return $old;
    }

    /**
     * Get the request object.
     *
     * @access  protected
     * @return  Hoa_Console_Request
     */
    protected function getRequest ( ) {

        return $this->_request;
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

            $this->setRequest();

            $dispatcher = new Hoa_Console_Dispatcher();
            $dispatcher->setRequest($this->getRequest());
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

        $directory = $this->getParameter('interface.style.directory');
        $path      = HOA_DATA_BIN . DS . $directory . DS . $style . '.php';

        if(!file_exists($path))
            throw new Hoa_Console_Exception(
                'The style %s is not found at %s.', 0, array($style, $path));

        require_once $path;

        $sheet     = new $style();

        if(!($sheet instanceof Hoa_Console_Interface_Style))
            throw new Hoa_Console_Exception(
                'The style %s must extend the Hoa_Console_Interface_Style class.',
                1, $style);

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
