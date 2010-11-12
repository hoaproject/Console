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
 *
 *
 * @category    Framework
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Core_Cli_Parser
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Console_Core_Cli_Exception
 */
import('Console.Core.Cli.Exception');

/**
 * Class Hoa_Console_Core_Cli_Parser.
 *
 * This class parses a command line.
 * See the parse() method to get more informations about command-line
 * vocabulary, patterns, limitations, etc.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Console
 * @subpackage  Hoa_Console_Core_Cli_Parser
 */

class Hoa_Console_Core_Cli_Parser {

    /**
     * If long value is not enabled, -abc is equivalent to -a -b -c, else -abc
     * is equivalent to --abc.
     *
     * @var Hoa_Console_Core_Cli_Parser
     */
    protected $longonly = false;

    /**
     * The parsed result in three categories : command, input, and switch.
     *
     * @var Hoa_Console_Core_Cli_Parser array
     */
    protected $parsed   =  array(
        'command'       => array(),
        'input'         => array(),
        'switch'        => array()
    );



    /**
     * Call parse method if the command is not null.
     *
     * @access  public
     * @param   string  $command    The command to parse.
     * @return  void
     */
    public function __construct ( $command = null ) {

        if(null !== $command)
            $this->parse($command);
    }

    /**
     * Parse a command.
     * Some explanations :
     * 1. Command :
     *   $ cmd         is the command : cmd ;
     *   $ "cmd sub"   is the command : cmd sub ;
     *   $ cmd\ sub    is the command : cmd sub.
     *
     * 2. Short option :
     *   $ … -s        is a short option ;
     *   $ … -abc      is equivalent to -a -b -c if and only if $longonly is set
     *                 to false, else (set to true) -abc is equivalent to --abc.
     *
     * 3. Long option :
     *   $ … --long    is a long option ;
     *   $ … --lo-ng   is a long option.
     *   $ etc.
     *
     * 4. Boolean switch or flag :
     *   $ … -s        is a boolean switch, -s is set to true ;
     *   $ … --long    is a boolean switch, --long is set to true ;
     *   $ … -s -s and --long --long
     *                 are boolean switches, -s and --long are set to false ;
     *   $ … -aa       are boolean switches, -a is set to false, if and only if
     *                 the $longonly is set to false, else --aa is set to true.
     *
     * 5. Valued switch :
     *   x should be s, -long, abc etc.
     *   All the following examples are valued switches, where -x is set to the
     *   specified value.
     *   $ … -x=value      : value ;
     *   $ … -x=va\ lue    : va lue ;
     *   $ … -x="va lue"   : va lue ;
     *   $ … -x="va l\"ue" : va l"ue ;
     *   $ … -x value      : value ;
     *   $ … -x va\ lue    : va lue ;
     *   $ … -x "value"    : value ;
     *   $ … -x "va lue"   : va lue ;
     *   $ … -x va\ l"ue   : va l"ue ;
     *   $ … -x 'va "l"ue' : va "l"ue ;
     *   $ etc. (we did not written all cases, but the philosophy is here).
     *   Two type of quotes are supported : double quotes ("), and simple
     *   quotes (').
     *   We got very particulary cases :
     *   $ … -x=-value     : -value ;
     *   $ … -x "-value"   : -value ;
     *   $ … -x \-value    : -value ;
     *   $ … -x -value     : two switches, -x and -value are set to true ;
     *   $ … -x=-7         : -7, a negative number.
     *   And if we have more than one valued switch, the value is overwritted :
     *   $ … -x a -x b     : b.
     *   Maybe, it should produce an array, like the special valued switch (see
     *   the point 6. please).
     *
     * 6. Special valued switch :
     *   Some valued switch can have a list, or an interval in value ;
     *   e.g. -x=a,b,c, or -x=1:7 etc.
     *   This class gives the value as it is, i.e. no manipulation or treatment
     *   is made.
     *   $ … -x=a,b,c      : a,b,c (and no array('a', 'b', 'c')) ;
     *   $ etc.
     *   These manipulations should be made by the user no ? The
     *   self::parseSpecialValue() is written for that.
     *
     * 7. Input :
     *   The regular expression sets a value as much as possible to each
     *   switch (option). If a switch does not take a value (see the
     *   Hoa_Console_Core_GetOption::NO_ARGUMENT constant), the value will be
     *   transfered to the input stack. But this action is not made in this
     *   class, only in the Hoa_Console_Core_GetOption class, because this class
     *   does not have the options profile. We got the transferSwitchToInput()
     *   method, that is called in the GetOption class.
     *   So :
     *   $ cmd -x input           the input is the -x value ;
     *   $ cmd -x -- input        the input is a real input, not a value ;
     *   $ cmd -x value input     -x is set to value, and the input is a real
     *                            input ;
     *   $ cmd -x value -- input  equivalent to -x value input ;
     *   $ … -a b i -c d ii       -a is set to b, -c to d, and we got two
     *                            inputs : i and ii.
     *
     * Warning : if the command was reconstitued from the $_SERVER variable, all
     * these cases are not sure to work, because the command was already
     * interpreted/parsed by an other parser (Shell, DOS etc.), and maybe they
     * remove some character, or some particular case. But if we give the
     * command manually — i.e. without any reconstitution —, all these cases
     * will work :).
     *
     * @access  public
     * @param   string  $command    The command to parse.
     * @return  void
     * @return  Hoa_Console_Core_Cli_Exception
     */
    public function parse ( $command = '' ) {

        if(empty($command))
            throw new Hoa_Console_Core_Cli_Exception(
                'The command could not be empty.', 0);

        /**
         * Here we go …
         *
         *     #
         *     (?:
         *         (?<b>--?[^=\s]+)
         *         (?:
         *             (?:(=)|(\s))
         *             (?<!\\\)(?:("|\')|)
         *             (?<s>(?(3)[^-]|).*?)
         *             (?(4)
         *                 (?<!\\\)\4
         *                 |
         *                 (?(2)
         *                     (?<!\\\)\s
         *                     |
         *                     (?:(?:(?<!\\\)\s)|$)
         *                 )
         *             )
         *         )?
         *     )
         *     |
         *     (?:
         *         (?<!\\\)(?:("|\')|)
         *         (?<i>.*?)
         *         (?(6)
         *             (?<!\\\)\6
         *             |
         *             (?:(?:(?<!\\\)\s)|$)
         *         )
         *     )
         *     #xSsm
         *
         * Nice isn't it :D ?
         *
         * Note : this regular expression likes to capture empty array (near
         * <input>), why ?
         */

        $regex = '#(?:(?<b>--?[^=\s]+)(?:(?:(=)|(\s))(?<!\\\)(?:("|\')|)(?<s>(?(3)[^-]|).*?)(?(4)(?<!\\\)\4|(?(2)(?<!\\\)\s|(?:(?:(?<!\\\)\s)|$))))?)|(?:(?<!\\\)(?:("|\')|)(?<i>.*?)(?(6)(?<!\\\)\6|(?:(?:(?<!\\\)\s)|$)))#Ssm';

        preg_match_all($regex, $command, $matches, PREG_SET_ORDER);

        foreach($matches as $i => $match)
            if($this->isInput($match))
                $this->addInput($match);

            elseif($this->isBoolSwitch($match))
                $this->addBoolSwitch($match);

            elseif($this->isValuedSwitch($match))
                $this->addValuedSwitch($match);

        $this->setCommand(array_shift($this->parsed['input']));

        return;
    }

    /**
     * Check if the match is an input.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  bool
     */
    protected function isInput ( Array $match ) {

        return isset($match['i']);
    }

    /**
     * Check if the match is an boolean switch.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  bool
     */
    protected function isBoolSwitch ( Array $match ) {

        return !isset($match['i']) && !isset($match['s']);
    }

    /**
     * Check if the match is a valued switch.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  bool
     */
    protected function isValuedSwitch ( Array $match ) {

        return !isset($match['i']) && isset($match['s']);
    }

    /**
     * Add an input.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  void
     */
    protected function addInput ( Array $match ) {

        if(false === $this->isInput($match))
            return;

        if(empty($match['i']))
            return;

        $handle = $match['i'];

        if(!empty($match[6]))
            $handle = str_replace('\\' . $match[6], $match[6], $handle);
        else
            $handle = str_replace('\\ ', ' ', $handle);

        $this->parsed['input'][] = $handle;
    }

    /**
     * Add a boolean switch.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  void
     */
    protected function addBoolSwitch ( Array $match ) {

        if(false === $this->isBoolSwitch($match))
            return;

        $this->addSwitch($match['b'], true);
    }

    /**
     * Add a valued switch.
     *
     * @access  protected
     * @param   array      $match    The match result.
     * @return  void
     */
    protected function addValuedSwitch ( Array $match ) {

        if(false === $this->isValuedSwitch($match))
            return;

        $this->addSwitch($match['b'], $match['s'], $match[4]);
    }

    /**
     * Add a switch.
     *
     * @access  protected
     * @param   string     $name      Switch name.
     * @param   string     $value     Switch value.
     * @param   string     $escape    Character to espace.
     * @return  void
     */
    protected function addSwitch ( $name, $value, $escape = null ) {

        if(substr($name, 0, 2) == '--')
            $this->addSwitch(substr($name, 2), $value, $escape);

        elseif(substr($name, 0, 1) == '-')

            if(true === $this->getLongOnly())
                $this->addSwitch('-' . $name, $value, $escape);

            else
                foreach(str_split(substr($name, 1)) as $foo => $switch)
                    $this->addSwitch($switch, $value, $escape);
        else {

            if(null !== $escape) {

                $escape = $escape == '' ? ' ' : $escape;
                if(is_string($value))
                    $value  = str_replace('\\' . $escape, $escape, $value);
            }
            else
                if(is_string($value))
                    $value  = str_replace('\\ ', ' ', $value);

            if(   isset($this->parsed['switch'][$name])
               && is_bool($this->parsed['switch'][$name]))
                $value = (bool) (1 - $this->parsed['switch'][$name]);

            if(empty($name))
                return $this->addInput(array(6 => null, 'i' => $value));

            $this->parsed['switch'][$name] = $value;
        }

        return;
    }

    /**
     * Transfer a switch value in the input stack.
     *
     * @access  public
     * @param   string  $name     The switch name.
     * @param   string  $value    The switch value.
     * @return  void
     */
    public function transferSwitchToInput ( $name, &$value ) {

        if(!isset($this->parsed['switch'][$name]))
            return;

        $this->parsed['input'][] = $this->parsed['switch'][$name];
        $value                   = true;
        unset($this->parsed['switch'][$name]);

        return;
    }

    /**
     * Set the command.
     *
     * @access  protected
     * @param   string     $command    The command.
     * @return  void
     */
    protected function setCommand ( $command ) {

        $this->parsed['command'] = $command;
    }

    /**
     * Get command.
     *
     * @access  protected
     * @return  string
     */
    public function getCommand ( ) {

        return $this->parsed['command'];
    }

    /**
     * Get all inputs.
     *
     * @access  protected
     * @return  array
     */
    public function getInputs ( ) {

        return $this->parsed['input'];
    }

    /**
     * Distribute inputs in variable (like the list() function, but without
     * error).
     *
     * @access  public
     * @param   string  $a     First input.
     * @param   string  $b     Second input.
     * @param   string  $c     Third input.
     * @param   ...     ...    ...
     * @param   string  $z     26th input.
     * @return  void
     */
    public function listInputs ( &$a,        &$b = null, &$c = null, &$d = null, &$e = null,
                                 &$f = null, &$g = null, &$h = null, &$i = null, &$j = null,
                                 &$k = null, &$l = null, &$m = null, &$n = null, &$o = null,
                                 &$p = null, &$q = null, &$r = null, &$s = null, &$t = null,
                                 &$u = null, &$v = null, &$w = null, &$x = null, &$y = null,
                                 &$z = null ) {

        $inputs = $this->getInputs();

        $i  = 'a';
        $ii = -1;

        while(isset($inputs[++$ii]) && $i <= 'z')
            ${$i++} = $inputs[$ii];

        return;
    }

    /**
     * Get all switches.
     *
     * @access  protected
     * @return  array
     */
    public function getSwitches ( ) {

        return $this->parsed['switch'];
    }

    /**
     * Parse a special value, i.e. with comma and intervals.
     *
     * @access  public
     * @param   string  $value       The value to parse.
     * @param   array   $keywords    Value of keywords.
     * @return  array
     * @throw   Hoa_Console_Core_Cli_Exception
     * @todo    Could be ameliorate with a ":" explode, and some eval.
     *          Check if operands are integer.
     */
    public function parseSpecialValue ( $value, Array $keywords = array() ) {

        $out = array();

        foreach(explode(',', $value) as $key => $subvalue) {

            $subvalue = str_replace(
                            array_keys($keywords),
                            array_values($keywords),
                            $subvalue
                        );

            if(0 !== preg_match('#^(-?[0-9]+):(-?[0-9]+)$#', $subvalue, $matches)) {

                if($matches[1] < 0 && $matches[2] < 0)
                    throw new Hoa_Console_Core_Cli_Exception(
                        'Cannot give two negative numbers, given %s.',
                        1, $subvalue);

                array_shift($matches);
                $max = max ($matches);
                $min = min ($matches);

                if($max < 0 || $min < 0) {

                    if($max - $min < 0)
                        throw new Hoa_Console_Core_Cli_Exception(
                            'The difference between operands must be ' .
                            'positive.', 2);

                    $min = $max + $min;
                }

                $out = array_merge(range($min, $max), $out);
            }
            else
                $out[] = $subvalue;
        }

        return $out;
    }

    /**
     * Set the long-only parameter.
     *
     * @access  public
     * @param   bool    $longonly    The long-only value.
     * @return  bool
     */
    public function setLongOnly ( $longonly = false ) {

        $old            = $this->longonly;
        $this->longonly = $longonly;

        return $old;
    }

    /**
     * Get the long-only value.
     *
     * @access  public
     * @return  bool
     */
    public function getLongOnly ( ) {

        return $this->longonly;
    }
}
