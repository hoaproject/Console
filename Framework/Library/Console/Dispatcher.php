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
 * Copyright (c) 2007, 2009 Ivan ENDERLIN. All rights reserved.
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
 * @subpackage  Hoa_Console_Dispatcher
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Console
 */
import('Console.~');

/**
 * Hoa_Console_Exception
 */
import('Console.Exception');

/**
 * Hoa_Console_Core_Cli
 */
import('Console.Core.Cli');

/**
 * Hoa_Console_Command_Abstract
 */
import('Console.Command.Abstract');

/**
 * Class Hoa_Console_Dispatcher.
 *
 * Dispatch group and command.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Dispatcher
 */

class Hoa_Console_Dispatcher implements Hoa_Framework_Parameterizable_Readable {

    /**
     * Parameters of Hoa_Console.
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters = null;



    /**
     * Construct a dispatcher.
     *
     * @access  public
     * @param   Hoa_Framework_Parameter  $parameters    Parameters.
     * @return  void
     */
    public function __construct ( Hoa_Framework_Parameter $parameters ) {

        $this->_parameters = $parameters;

        return;
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
     * Dispatch: get a prompt, the command-line parsing, the command name,
     * instance class, call method, manage errors, exceptions and returns etc.
     * Take a look to
     * http://www.opengroup.org/onlinepubs/007908799/xbd/termios.html#tag_008_001_009
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Console_Exception
     */
    public function dispatch ( ) {

        $cli = new Hoa_Console_Core_Cli($this->_parameters);
        $this->_parameters->shareWith(
            $this,
            $cli,
            Hoa_Framework_Parameter::PERMISSION_READ |
            Hoa_Framework_Parameter::PERMISSION_WRITE
        );

        do {

            $cli->newPrompt();

            $class     = $this->getFormattedParameter('command.class');
            $file      = $this->getFormattedParameter('command.file');
            $directory = $this->getFormattedParameter('command.directory');
            $path      = $directory . '/' . $file;

            try {

                if(!file_exists($path))
                    throw new Hoa_Console_Exception(
                        'File %s is not found.', -1, $path);

                require_once $path;

                $reflection = new ReflectionClass($class);
                $argument   = array(
                                  $this->_parameters,
                                  $cli->getParsed()
                              );
                $object     = $reflection->newInstanceArgs($argument);
                $return     = HC_EXIT;

                $this->_parameters->shareWith(
                    $this,
                    $object,
                    Hoa_Framework_Parameter::PERMISSION_READ
                );

                if($object instanceof Hoa_Console_Command_Abstract) {

                    $return = $object->main();

                    if(null === $return)
                        $return = HC_SUCCESS;
                }
                else {

                    $object = null;
                    throw new Hoa_Console_Exception(
                        'Class %s must extend Hoa_Console_Command_Abstract.',
                        6, $class);
                }
            }
            catch ( ReflectionException $e ) {

                throw new Hoa_Console_Exception($e->getMessage(), $e->getCode());
            }
            catch ( Hoa_Console_Exception $e ) {

                throw $e;
            }

            $continue = ~$return & HC_EXIT;

            if(true === Hoa_Console::isStandalone())
                $continue = false;

        } while($continue);
    }
}
