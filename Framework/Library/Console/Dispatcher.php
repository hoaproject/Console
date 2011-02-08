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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Console
 */
-> import('Console.~')

/**
 * \Hoa\Console\Exception
 */
-> import('Console.Exception')

/**
 * \Hoa\Console\Core\Cli
 */
-> import('Console.Core.Cli.~')

/**
 * \Hoa\Console\Command\Generic
 */
-> import('Console.Command.Generic');

}

namespace Hoa\Console {

/**
 * Class \Hoa\Console\Dispatcher.
 *
 * Dispatch group and command.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Dispatcher implements \Hoa\Core\Parameterizable\Readable {

    /**
     * Parameters of \Hoa\Console.
     *
     * @var \Hoa\Core\Parameter object
     */
    private $_parameters = null;



    /**
     * Construct a dispatcher.
     *
     * @access  public
     * @param   \Hoa\Core\Parameter  $parameters    Parameters.
     * @return  void
     */
    public function __construct ( \Hoa\Core\Parameter $parameters ) {

        $this->_parameters = $parameters;

        return;
    }

    /**
     * Get many parameters from a class.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Core\Exception
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
     * @throw   \Hoa\Console\Exception
     */
    public function dispatch ( ) {

        $cli = new Core\Cli($this->_parameters);
        $this->_parameters->shareWith(
            $this,
            $cli,
            \Hoa\Core\Parameter::PERMISSION_READ |
            \Hoa\Core\Parameter::PERMISSION_WRITE
        );

        do {

            $cli->newPrompt();

            $class     = $this->getFormattedParameter('command.class');
            $file      = $this->getFormattedParameter('command.file');
            $directory = $this->getFormattedParameter('command.directory');
            $path      = $directory . '/' . $file;

            try {

                if(!file_exists($path))
                    throw new Exception(
                        'File %s is not found.', -1, $path);

                require_once $path;

                $reflection = new \ReflectionClass($class);
                $argument   = array(
                                  $this->_parameters,
                                  $cli->getParsed()
                              );
                $object     = $reflection->newInstanceArgs($argument);
                $return     = HC_EXIT;

                $this->_parameters->shareWith(
                    $this,
                    $object,
                    \Hoa\Core\Parameter::PERMISSION_READ
                );

                if($object instanceof Command\Generic) {

                    $return = $object->main();

                    if(null === $return)
                        $return = HC_SUCCESS;
                }
                else {

                    $object = null;
                    throw new Exception(
                        'Class %s must extend \Hoa\Console\Command\Generic.',
                        6, $class);
                }
            }
            catch ( \ReflectionException $e ) {

                throw new Exception($e->getMessage(), $e->getCode());
            }
            catch ( Exception $e ) {

                throw $e;
            }

            $continue = ~$return & HC_EXIT;

            if(true === Console::isStandalone())
                $continue = false;

        } while($continue);
    }
}

}
