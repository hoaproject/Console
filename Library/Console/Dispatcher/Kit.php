<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console\Parser
 */
-> import('Console.Parser')

/**
 * \Hoa\Console\GetOption
 */
-> import('Console.GetOption')

/**
 * \Hoa\Console\Io
 */
-> import('Console.Io', true)

/**
 * \Hoa\Console\Readline
 */
-> import('Console.Readline.~')

/**
 * \Hoa\Console\Readline\Password
 */
-> import('Console.Readline.Password')

/**
 * \Hoa\Console\Chrome\Style
 */
-> import('Console.Chrome.Style')

/**
 * \Hoa\Console\Chrome\Text
 */
-> import('Console.Chrome.Text')

/**
 * \Hoa\Console\Environment
 */
-> import('Console.Environment.~')

/**
 * \Hoa\Dispatcher\Kit
 */
-> import('Dispatcher.Kit')

/**
 * \Hoa\Router
 */
-> import('Router.~');

}

namespace Hoa\Console\Dispatcher {

/**
 * Class \Hoa\Console\Dispatcher\Kit.
 *
 * A structure, given to action, that holds some important data.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Kit extends \Hoa\Dispatcher\Kit {

    /**
     * CLI parser.
     *
     * @var \Hoa\Console\Parser object
     */
    public $parser      = null;

    /**
     * Options (as described in \Hoa\Console\GetOption).
     *
     * @var \Hoa\Console\Dispatcher\Kit array
     */
    protected $options  = null;

    /**
     * Options analyzer.
     *
     * @var \Hoa\Console\GetOption object
     */
    protected $_options = null;



    /**
     * Build a dispatcher kit.
     *
     * @access  public
     * @param   \Hoa\Router           $router        The router.
     * @param   \Hoa\Dispatcher       $dispatcher    The dispatcher.
     * @param   \Hoa\View\Viewable    $view          The view.
     * @return  void
     */
     public function __construct ( \Hoa\Router        $router,
                                   \Hoa\Dispatcher    $dispatcher,
                                   \Hoa\View\Viewable $view = null ) {

        parent::__construct($router, $dispatcher, $view);

        $this->parser = new \Hoa\Console\Parser();

        return;
    }

    /**
     * Alias of \Hoa\Console\GetOption::getOptions().
     *
     * @access  public
     * @param   string  &$optionValue    Please, see original API.
     * @param   string  &$short          Please, see original API.
     * @return  mixed
     */
    public function getOption ( &$optionValue, $short = null ) {

        if(null === $this->_options && !empty($this->options))
            $this->setOptions($this->options);

        if(null === $this->_options)
            return false;

        return $this->_options->getOption($optionValue, $short);
    }

    /**
     * Initialize options.
     *
     * @access  public
     * @param   array  $options    Options, as described in
     *                             \Hoa\Console\GetOption.
     * @return  array
     */
    public function setOptions ( Array $options ) {

        $old           = $this->options;
        $this->options = $options;
        $rule          = $this->router->getTheRule();
        $variables     = $rule[\Hoa\Router::RULE_VARIABLES];

        if(isset($variables['_tail'])) {

            $this->parser->parse($variables['_tail']);
            $this->_options = new \Hoa\Console\GetOption(
                $this->options,
                $this->parser
            );
        }

        return $old;
    }

    /**
     * It is a helper to make the usage options list.
     *
     * @access  public
     * @param   array  $definitions    An associative arry: short or long option
     *                                 associated to the definition.
     * @return  string
     */
    public function makeUsageOptionsList ( Array $definition = array() ) {

        $out = array();

        foreach($this->options as $i => $options)
            $out[] = array(
                ' -' . $options[\Hoa\Console\GetOption::OPTION_VAL] . ', --' .
                $options[\Hoa\Console\GetOption::OPTION_NAME] .
                ($options[\Hoa\Console\GetOption::OPTION_HAS_ARG] ===
                    \Hoa\Console\GetOption::REQUIRED_ARGUMENT
                    ? '='
                    : ($options[\Hoa\Console\GetOption::OPTION_HAS_ARG] ===
                           \Hoa\Console\GetOption::OPTIONAL_ARGUMENT
                        ? '[=]'
                        : '')),
                (isset($definition[$options[\Hoa\Console\GetOption::OPTION_VAL]])
                    ? $definition[$options[\Hoa\Console\GetOption::OPTION_VAL]]
                    : (isset($definition[$options[0]])
                        ? $definition[$options[\Hoa\Console\GetOption::OPTION_NAME]]
                        : null
                    )
                )
            );

        return \Hoa\Console\Chrome\Text::columnize(
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
     * @accesss public
     * @param string  $text      The operation text.
     * @param bool    $status    The operation status.
     * @return void
     */
    public function status ( $text, $status ) {

        $out = ' ' . $this->stylize('*', 'info') . ' ' .
               $text . str_pad(
                   ' ',
                   \Hoa\Console\Environment::get('window.columns')
                   - strlen(preg_replace('#' . "\033". '\[[0-9]+m#', '', $text))
                   - 8
               ) .
               ($status === true
                   ? '[' . $this->stylize('ok', 'success') . ']'
                   : '[' . $this->stylize('!!', 'nosuccess') . ']');

        cout($out, \Hoa\Console\Io::NEW_LINE, \Hoa\Console\Io::NO_WORDWRAP);

        return;
    }

    /**
     * Alias to \Hoa\Console\Chrome\Style::stylize().
     *
     * @access  public
     * @param   string  $text       Please, see original API.
     * @param   mixed   $options    Please, see original API.
     * @return  string
     */
    public function stylize ( $text, $options = array() ) {

        return \Hoa\Console\Chrome\Style::stylize($text, $options);
    }

    /**
     * Read, edit, bind… a line from STDIN.
     *
     * @access  public
     * @param   string  $prefix    Prefix.
     * @return  string
     */
    public function readLine ( $prefix = null ) {

        static $_rl = null;

        if(null === $_rl)
            $_rl = new \Hoa\Console\Readline();

        return $_rl->readLine($prefix);
    }

    /**
     * Read, edit, bind… a password from STDIN.
     *
     * @access  public
     * @param   string  $prefix    Prefix.
     * @return  string
     */
    public function readPassword ( $prefix = null ) {

        static $_rl = null;

        if(null === $_rl)
            $_rl = new \Hoa\Console\Readline\Password();

        return $_rl->readLine($prefix);
    }
}

}
