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
 * @subpackage  Hoa_Console_Core_Cli
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
 * Hoa_Console_Core_Exception
 */
import('Console.Core.Exception');

/**
 * Hoa_Console_Core_Cli_Parser
 */
import('Console.Core.Cli.Parser');

/**
 * Hoa_Console_Core_Io
 */
import('Console.Core.Io');

/**
 * Class Hoa_Console_Core_Cli.
 *
 * This class get a command-line (from the global variale, e.g. $_SERVER, or
 * from a printed prompt), and run the CLI parser, through the
 * Hoa_Console_Core_Cli_Parser class. When the parsing is over, this class
 * proposes the result to other classes.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2009 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Core_Cli
 */

class Hoa_Console_Core_Cli implements Hoa_Framework_Parameterizable_Readable {

    /**
     * Command parsed.
     *
     * @var Hoa_Console_Core_Cli_Parser object
     */
    protected $_commandline  = null;

    /**
     * Parameters of Hoa_Console.
     *
     * @var Hoa_Framework_Parameter object
     */
    private $_parameters     = null;



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
     * Get the command from a prompt of from the $_SERVER variable, and start
     * the parser.
     *
     * @access  public
     * @return  string
     */
    public function newPrompt ( ) {

        if(false === Hoa_Console::isStandalone())
            do {

                Hoa_Console_Core_Io::cout(
                    $this->getParameter('prompt.prefix'),
                    $this->getParameter('prompt.symbol'),
                    false
                );

                $command = Hoa_Console_Core_Io::cin();
            } while ( $command == '' );
        else
            $command = $this->rebuildCommand();

        $this->setParsed($command);

        return $command;
    }

    /**
     * Set the command given in the last prompt, and parse it with the
     * Hoa_Console_Core_Cli_Parser object.
     *
     * @access  protected
     * @param   string     $commandline     The command line.
     * @return  Hoa_Console_Core_Cli_Parser
     */
    protected function setParsed ( $commandline ) {

        $old                = $this->_commandline;
        $this->_commandline = new Hoa_Console_Core_Cli_Parser();
        $this->_commandline->setLongOnly($this->getParameter('cli.longonly'));
        $separator          = $this->getParameter('cli.separator');

        if(empty($commandline))
            $commandline = $this->_parameters->getKeyword($this, 'group') .
                           $separator .
                           $this->_parameters->getKeyword($this, 'command');

        $this->_commandline->parse($commandline);
        $command = $this->_commandline->getCommand();

        if(false === strpos($command, $separator))
            $command = $separator . $command;

        list($group, $command) = explode($separator, $command);

        if(!empty($group))
            $this->_parameters->setKeyword($this, 'group', $group);

        if(!empty($command))
            $this->_parameters->setKeyword($this, 'command', $command);

        return $old;
    }

    /**
     * Get the command parsed given in the last prompt.
     *
     * @acccess  public
     * @return   Hoa_Console_Core_Cli_Parser
     */
    public function getParsed ( ) {

        return $this->_commandline;
    }

    /**
     * Rebuilt command line from $_SERVER['argv'].
     *
     * @access  protected
     * @return  string
     */
    protected function rebuildCommand ( ) {

        if(!isset($_SERVER['argv'][0]))
            return null;

        $out = null;
        unset($_SERVER['argv'][0]);

        foreach($_SERVER['argv'] as $foo => $arg) {

            $handle = $arg;

            if(false !== strpos($arg, '=')) {

                if    (false !== strpos($arg, '"')) {

                    $handle = str_replace('"', '\\"', $arg);
                    $handle = str_replace('=', '="',  $handle) . '"';
                }

                elseif(false !== strpos($arg, "'")) {

                    $handle = str_replace("'", "\\'", $arg);
                    $handle = str_replace('=', "='",  $handle) . "'";
                }
            }

            if(false !== strpos($arg, ' '))
                $handle = '"' . str_replace('"', '\\"', $arg) . '"';


            $out .= $handle . ' ';
        }

        return trim($out);
    }
}
