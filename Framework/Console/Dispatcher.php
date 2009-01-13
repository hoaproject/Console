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
 * Hoa_Console_Router
 */
import('Console.Router');

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
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Dispatcher
 */

class Hoa_Console_Dispatcher {

    /**
     * Command Line Interface.
     *
     * @var Hoa_Console_Core_Cli object
     */
    protected $_cli     = null;

    /**
     * Router.
     *
     * @var Hoa_Console_Router object
     */
    protected $_router  = null;

    /**
     * Request.
     *
     * @var Hoa_Console_Request object
     */
    protected $_request = null;



    /**
     * Set the request.
     *
     * @access  public
     * @param   Hoa_Console_Request  $request    The request instance.
     * @return  Hoa_Console_Request
     */
    public function setRequest ( Hoa_Console_Request $request ) {

        $old            = $this->_request;
        $this->_request = $request;

        return $old;
    }

    /**
     * Get the request.
     *
     * @access  public
     * @return  Hoa_Console_Request
     */
    public function getRequest ( ) {

        return $this->_request;
    }

    /**
     * Dispatch : get a prompt, the command-line parsing, the command name,
     * instance class, call method, manage errors, exceptions and returns etc.
     * Take a look to
     * http://www.opengroup.org/onlinepubs/007908799/xbd/termios.html#tag_008_001_009
     *
     * @access  public
     * @return  void
     * @throw   Hoa_Console_Exception
     */
    public function dispatch ( ) {

        $this->setCli();
        $this->setRouter();

        do {

            $this->getCli()->setRequest($this->getRequest());
            $this->getCli()->newPrompt();

            $this->getRouter()->setRequest($this->getRequest());
            $this->getRouter()->route($this->getCli()->getParsed()->getCommand());

            $path    = $this->getRequest()->getParameter('route.directory');
            $group   = $this->getRequest()->getParameter('system.group.value');
            $command = $this->getRequest()->getParameter('system.command.value');
            $file    = $this->getRequest()->getParameter('system.command.file');
            $class   = $this->getRequest()->getParameter('system.command.class');

            try {

                $this->load($path . $group . DS . $file . '.php');

                $reflection = new ReflectionClass($class);
                $argument   = array(
                                  $this->getRequest(),
                                  $this->getCli()->getParsed()
                              );
                $object     = $reflection->newInstanceArgs($argument);
                $return     = HC_EXIT;

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

    /**
     * Set the CLI object.
     *
     * @access  protected
     * @return  Hoa_Console_Core_Cli
     */
    protected function setCli ( ) {

        $old        = $this->_cli;
        $this->_cli = new Hoa_Console_Core_Cli();

        return $old;
    }

    /**
     * Get the CLI object.
     *
     * @access  public
     * @return  Hoa_Console_Core_Cli
     */
    public function getCli ( ) {

        return $this->_cli;
    }

    /**
     * Set the router.
     *
     * @access  protected
     * @return  Hoa_Console_Router
     */
    protected function setRouter ( ) {

        $old           = $this->_router;
        $this->_router = new Hoa_Console_Router();

        return $old;
    }

    /**
     * Get the router.
     *
     * @access  public
     * @return  Hoa_Console_Router
     */
    public function getRouter ( ) {

        return $this->_router;
    }

    /**
     * Load file.
     *
     * @access  private
     * @param   string   $file    File to load.
     * @return  void
     * @throw   Hoa_Console_Exception
     */
    private function load ( $file = '' ) {

        if(!file_exists($file))
            throw new Hoa_Console_Exception(
                'File %s is not found.', 7, $file);

        require_once $file;
    }
}
