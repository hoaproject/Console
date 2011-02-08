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
 * \Hoa\Console\Command\Exception
 */
-> import('Console.Command.Exception')

/**
 * \Hoa\Console\Core\GetOption
 */
-> import('Console.Core.GetOption')

/**
 * \Hoa\Console\Core\Io
 */
-> import('Console.Core.Io', true)

/**
 * \Hoa\Console\Chrome\Style
 */
-> import('Console.Chrome.Style')

/**
 * \Hoa\Console\Chrome\Sound
 */
-> import('Console.Chrome.Sound')

/**
 * \Hoa\Console\Chrome\Text
 */
-> import('Console.Chrome.Text')

/**
 * \Hoa\Console\System
 */
-> import('Console.System.~')

/**
 * \Hoa\Console\Environment
 */
-> import('Console.Environment.~');

}

namespace Hoa\Console\Command {

/**
 * Class \Hoa\Console\Command\Generic.
 *
 * This class is a bridge between the command and the \Hoa\Console package, i.e.
 * it is a wrapper for many classes.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

abstract class Generic implements \Hoa\Core\Parameterizable\Readable {

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
     * Alias constants of \Hoa\Console\Chrome\Text::ALIGN_*.
     *
     * @const int
     */
    const ALIGN_LEFT         = 0;
    const ALIGN_RIGHT        = 1;
    const ALIGN_CENTER       = 2;

    /**
     * Alias constants of \Hoa\Console\Chrome\Style::COLOR_*, and TEXT_*.
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
    const COLOR_BACKGROUND_BLACK  = 40;
    const COLOR_BACKGROUND_RED    = 41;
    const COLOR_BACKGROUND_GREEN  = 42;
    const COLOR_BACKGROUND_YELLOW = 43;
    const COLOR_BACKGROUND_BLUE   = 44;
    const COLOR_BACKGROUND_VIOLET = 45;
    const COLOR_BACKGROUND_CYAN   = 46;
    const COLOR_BACKGROUND_WHITE  = 47;
    const TEXT_BOLD               = 1;
    const TEXT_UNDERLINE          = 4;
    const TEXT_BLINK              = 5;
    const TEXT_REVERSE            = 7;
    const TEXT_CONCEAL            = 8;

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
    protected $options     = array();

    /**
     * The author name.
     *
     * @var \Hoa\Console\Command\Generic string
     */
    protected $author      = null;

    /**
     * The program name.
     *
     * @var \Hoa\Console\Command\Generic string
     */
    protected $programName = null;

    /**
     * Parser.
     *
     * @var \Hoa\Console\Core\Cli\Parser object
     */
    private $_parser       = null;

    /**
     * GetOption.
     *
     * @var \Hoa\Console\Core\GetOption object
     */
    private $gopt          = null;

    /**
     * Parameters of \Hoa\Console.
     *
     * @var \Hoa\Core\Parameter object
     */
    private $_parameters   = null;

    

    /**
     * Set the request and the parser.
     *
     * @access  public
     * @param   \Hoa\Core\Parameter      $parameters    Parameters.
     * @param   \Hoa\Console\Core\Cli\Parser  $parser        The parser instance.
     * @return  void
     */
    final public function __construct ( \Hoa\Core\Parameter     $parameters,
                                        \Hoa\Console\Core\Cli\Parser $parser ) {

        $this->_parameters = $parameters;
        $this->setParser($parser);
        $this->setGopt();

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
     * Set the router.
     *
     * @access  private
     * @param   \Hoa\Console\Core\Cli\Parser  $parser    The parser instance.
     * @return  \Hoa\Console\Core\Cli\Parser
     */
    private function setParser ( \Hoa\Console\Core\Cli\Parser $parser ) {

        $old           = $this->_parser;
        $this->_parser = $parser;

        return $old;
    }

    /**
     * Get the parser.
     *
     * @access  private
     * @return  \Hoa\Console\Core\Cli\Parser
     */
    private function getParser ( ) {

        return $this->_parser;
    }

    /**
     * Set the getOption object.
     *
     * @access  private
     * @return  \Hoa\Console\Core\GetOption
     */
    private function setGopt ( ) {

        $old        = $this->gopt;
        $this->gopt = new \Hoa\Console\Core\GetOption(
                          $this->_getOption(),
                          $this->getParser()
                      );

        return $old;
    }

    /**
     * Get the getOption object.
     *
     * @access  private
     * @return  \Hoa\Console\Core\GetOption
     */
    private function getOpt ( ) {

        return $this->gopt;
    }

    /**
     * Get options.
     *
     * @access  private
     * @return  array
     */
    private function _getOption ( ) {

        return $this->options;
    }

    /**
     * Get option from a pipette. Please, see the \Hoa\Console\Core\GetOption
     * class.
     *
     * @access  protected
     * @param   string     $optionValue    Place a variable that will receive
     *                                     the value of the current option.
     * @param   string     $short          Short options to scan (in a single
     *                                     string). If $short = null, all short
     *                                     options will be selected.
     * @return  mixed
     */
    protected function getOption ( &$optionValue = null, $short = null ) {

        return $this->getOpt()->getOption($optionValue, $short);
    }

    /**
     * Get all inputs.
     *
     * @access  public
     * @return  array
     */
    public function getInputs ( ) {

        return $this->getParser()->getInputs();
    }

    /**
     * Wrap the \Hoa\Console\Core\Cli\Parser::listInputs() method.
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

        return $this->getParser()->listInputs(
            $a, $b, $c, $d, $e, $f, $g, $h, $i, $j,
            $k, $l, $m, $n, $o, $p, $q, $r, $s, $t,
            $u, $v, $w, $x, $y, $z
        );
    }

    /**
     * Wrap the \Hoa\Console\Core\Cli\Parser::parseSpecialValue() method.
     *
     * @access  public
     * @param   string  $value       The value to parse.
     * @param   array   $keywords    Value of keywords.
     * @return  array
     * @throw   \Hoa\Console\Core\Cli\Exception
     */
    public function parseSpecialValue ( $value, Array $keywords = array() ) {

        return $this->getParser()->parseSpecialValue($value, $keywords);
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Style::stylize() method.
     *
     * @access  public
     * @param   string  $text    
     * @param   string  $options    Should be an integer or an array of integer
     *                              (given by
     *                              \Hoa\Console\Chrome\Style::COLOR_* or 
     *                              \Hoa\Console\Chrome\Style::TEXT_*
     *                              constants combinaisons), or a style name.
     * @return  string
     */
    public function stylize ( $text, $options = array() ) {

        return \Hoa\Console\Chrome\Style::stylize($text, $options);
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Sound::bip() method.
     *
     * @access  public
     * @return  string
     */
    public function bip ( ) {

        return \Hoa\Console\Chrome\Sound::bip();
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Text::align() method.
     *
     * @access  public
     * @param   string  $text          The text.
     * @param   string  $alignement    The text alignement.
     * @param   int     $width         The layer width.
     * @return  string
     */
    public function align ( $text,
                            $alignement = \Hoa\Console\Chrome\Text::ALIGN_LEFT,
                            $width      = null ) {

        if(null === $width)
            $width = $this->getEnvironment('window.columns');

        return \Hoa\Console\Chrome\Text::align($text, $alignement, $width);
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Text::columnize() method.
     *
     * @access  public
     * @param   Array   $line                 The table represented by an array
     *                                        (see the method documentation).
     * @param   int     $alignement           The global alignement of the text
     *                                        in cell.
     * @param   int     $horizontalPadding    The horizontal padding (right
     *                                        padding).
     * @param   int     $verticalPadding      The vertical padding.
     * @param   string  $separator            String where each character is a
     *                                        column separator.
     * @return  string
     */
    public function columnize ( Array $line,
                                $alignement        = \Hoa\Console\Chrome\Text::ALIGN_LEFT,
                                $horizontalPadding = 2,
                                $verticalPadding   = 0,
                                $separator         = null ) {

        return \Hoa\Console\Chrome\Text::columnize(
                   $line,
                   $alignement,
                   $horizontalPadding,
                   $verticalPadding,
                   $separator
               );
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Text::wordwrap() method.
     *
     * @access  public
     * @param   string  $text     Text to wrap.
     * @param   int     $width    Line width.
     * @param   string  $break    String to make the break.
     * @return  string
     */
    public function wordwrap ( $text, $width = null, $break = "\n" ) {

        return \Hoa\Console\Chrome\Text::wordwrap($text, $width, $break);
    }

    /**
     * Wrap the \Hoa\Console\Chrome\Text::underline() method.
     *
     * @access  public
     * @param   string  $text       The text to underline.
     * @param   string  $pattern    The string used to underline.
     * @return  string
     */
    public function underline ( $text, $pattern = '*' ) {

        return \Hoa\Console\Chrome\Text::underline($text, $pattern);
    }

    /**
     * Wrap the \Hoa\Console\System::execute() method.
     *
     * @access  public
     * @param   string  $command    The command to execute.
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     */
    public function execute ( $command ) {

        return \Hoa\Console\System::execute($command);
    }

    /**
     * Wrap the \Hoa\Console\Environment::get() method.
     *
     * @access  public
     * @param   string  $data    Data to get.
     * @return  mixed
     * @throw   \Hoa\Console\Environment\Exception
     */
    public function getEnvironment ( $data ) {

        return \Hoa\Console\Environment::get($data);
    }

    /**
     * It is a helper to make the usage options list (through the
     * \Hoa\Console\Chrome\Text::columnize() method).
     *
     * @access  public
     * @param   Array   $definition    An associative array : short or long
     *                                 option associated to the definition.
     * @return  string
     */
    public function makeUsageOptionsList ( Array $definition = array() ) {

        $out = array();

        foreach($this->options as $i => $options)
            $out[] = array(
                '    -' . $options[self::OPTION_VAL] . ', --' .
                $options[self::OPTION_NAME] .
                ($options[self::OPTION_HAS_ARG] === self::REQUIRED_ARGUMENT
                    ? '='
                    : ($options[self::OPTION_HAS_ARG] === self::OPTIONAL_ARGUMENT
                          ? '[=]'
                          : '')),
                (isset($definition[$options[self::OPTION_VAL]])
                    ? $definition[$options[self::OPTION_VAL]]
                    : (isset($definition[$options[0]])
                          ? $definition[$options[self::OPTION_NAME]]
                          : null
                      )
                 )
            );

        return $this->columnize(
                   $out,
                   \Hoa\Console\Chrome\Text::ALIGN_LEFT,
                   .5,
                   0,
                   '|: '
               );
    }

    /**
     * Make a render of an operation.
     *
     * @accesss  public
     * @param    string  $text      The operation text.
     * @param    bool    $status    The operation status.
     * @return   void
     */
    public function status ( $text, $status ) {

        $out = '  ' . $this->stylize('*', 'info') . ' ' .
               $text .
               str_pad(
                   ' ',
                   $this->getEnvironment('window.columns')
                   - strlen(preg_replace('#' . "\033". '\[[0-9]+m#', '', $text))
                   - 9
               ) .
               (
                $status === true
                    ? '[' . $this->stylize('ok', 'success')   . ']'
                    : '[' . $this->stylize('!!', 'nosuccess') . ']'
               );

        cout($out, \Hoa\Console\Core\Io::NEW_LINE, \Hoa\Console\Core\Io::NO_WORDWRAP);

        return;
    }

    /**
     * Abstract main. It is the entry method of a command.
     *
     * @access  public
     * @return  int
     */
    abstract public function main ( );

    /**
     * Abstract usage. It describes the command comportement.
     *
     * @access  public
     * @return  int
     */
    abstract public function usage ( );
}

}
