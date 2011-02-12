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
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * \Hoa\Console\Core\Exception
 */
-> import('Console.Core.Exception');

}

namespace Hoa\Console\Core {

/**
 * Class \Hoa\Console\Core\GetOption.
 *
 * This class is complementary to the \Hoa\Console\Core\Cli\Parser class.
 * This class manages the options profile for a command, i.e. argument,
 * interactivity, option name etc.
 * And, of course, it proposes the getOption method, that allow user to loop
 * easily the command options/arguments.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class GetOption {

    /**
     * Values of the has_arg field of options array.
     *
     * @const int
     */
    const NO_ARGUMENT        = 0;
    const REQUIRED_ARGUMENT  = 1;
    const OPTIONAL_ARGUMENT  = 2;

    /**
     * Interactive arguments.
     *
     * @const int
     */
    const INTERACTIVE_NEVER  = 0; /* 0 : no option or --interactive=never    */
    const INTERACTIVE_ONCE   = 1; /* 1 : -I or --interactive=once            */
    const INTERACITVE_ALWAYS = 2; /* 2 : default, -i or --interactive=always */

    /**
     * Give the access to the option description.
     *
     * @const int
     */
    const OPTION_NAME        = 0;
    const OPTION_HAS_ARG     = 1;
    const OPTION_VAL         = 2;

    /**
     * Describe the command options (or switches).
     * An option is describing like this :
     *     name, has_arg, val
     * (In C, we got the flag data before val, but it does not have sens here).
     *
     * The name is the option name and the long option value.
     * The has_arg is a constant : NO_ARGUMENT, REQUIRED_ARGUMENT, and
     * OPTIONAL_ARGUMENT.
     * The val is the short option value.
     *
     * @var \Hoa\Console\Command\Generic array
     */
    protected $options         = array();

    /**
     * The differents values for interactive arguments.
     *
     * @var \Hoa\Console\Command\Generic array
     */
    protected $interactiveArgs = array(
        0 => array('false', '0', 'never',  'no',  'none'),
        1 => array('once'),
        2 => array('true',  '1', 'always', 'yes')
    );

    /**
     * The pipette contains all the short value of options.
     *
     * @var \Hoa\Console\Core\GetOption char
     */
    protected $pipette         = array();



    /**
     * Prepare the pipette.
     *
     * @access  public
     * @param   array                         $options    The option definition.
     * @param   \Hoa\Console\Core\Cli\Parser  $parser     The parser.
     * @return  void
     * @todo    Implement the INTERACTIVE_* constants.
     */
    public function __construct ( Array                        $options,
                                  \Hoa\Console\Core\Cli\Parser $parser   ) {

        if(empty($options))
            $this->pipette[null] = null;

        $this->options = $options;

        foreach($parser->getSwitches() as $name => $value) {

            $found    = null;
            $argument = null;

            foreach($options as $foo => $option) {

                if($option[self::OPTION_NAME] === "$name") {

                    $found    = $option[self::OPTION_VAL];
                    $argument = $option[self::OPTION_HAS_ARG];
                    break;
                }

                if($option[self::OPTION_VAL] === "$name") {

                    $found    = $option[self::OPTION_VAL];
                    $argument = $option[self::OPTION_HAS_ARG];
                    break;
                }
            }

            if(null === $found)
                continue;

            if($argument === self::NO_ARGUMENT) {

                if(!is_bool($value))
                    $parser->transferSwitchToInput($name, $value);
            }

            elseif(   $argument === self::REQUIRED_ARGUMENT
                   && !is_string($value))
                throw new Exception(
                    'The argument %s requires a value (it is not a switch).',
                    0, $name);

            $this->pipette[$found] = $value;
        }

        $this->pipette[null] = null;
        reset($this->pipette);

        return;
    }

    /**
     * Get option from the pipette.
     *
     * @access  public
     * @param   string  $optionValue    Place a variable that will receive the
     *                                  value of the current option.
     * @param   string  $short          Short options to scan (in a single
     *                                  string). If $short = null, all short
     *                                  options will be selected.
     * @return  mixed
     */
    public function getOption ( &$optionValue, $short = null ) {

        static $first = true;

        if(true === $this->isPipetteEmpty() && true === $first) {

            $first       = false;
            $optionValue = null;

            return null;
        }

        if(   ''   === key($this->pipette)
           && null === current($this->pipette)) {

            reset($this->pipette);
            $first = true;
            return false;
        }

        $allow = array();

        if(null === $short)
            foreach($this->options as $foo => $option)
                $allow[] = $option[self::OPTION_VAL];
        else
            $allow = str_split($short);

        if(!in_array(key($this->pipette), $allow))
            return false;

        $optionValue = current($this->pipette);
        $return      = key($this->pipette);
        next($this->pipette);

        return $return;
    }

    /**
     * Check if the pipette is empty.
     *
     * @access  public
     * @return  bool
     */
    public function isPipetteEmpty ( ) {

        return count($this->pipette) == 1;
    }
}

}
