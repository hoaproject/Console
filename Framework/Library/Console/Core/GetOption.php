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
 * @license    New BSD License
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

        foreach($parser->getSwitches() as $name => $values) {

            if(!is_array($values))
                $values = array($values);

            foreach($values as $value) {

                $found    = null;
                $argument = null;

                foreach($options as $option) {

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

                $this->pipette[] = array($found, $value);
            }
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

        $k     = key($this->pipette);
        $c     = current($this->pipette);
        $key   = $c[0];
        $value = $c[1];

        if('' === $k && null === $c) {

            reset($this->pipette);
            $first = true;

            return false;
        }

        $allow = array();

        if(null === $short)
            foreach($this->options as $option)
                $allow[] = $option[self::OPTION_VAL];
        else
            $allow = str_split($short);

        if(!in_array($key, $allow))
            return false;

        $optionValue = $value;
        $return      = $key;
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
