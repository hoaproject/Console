<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console
 */
-> import('Console.~');

}

namespace Hoa\Console {

/**
 * Class \Hoa\Console\Mouse.
 *
 * Allow to listen the mouse.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Mouse implements \Hoa\Core\Event\Listenable {

    /**
     * Singleton.
     *
     * @var \Hoa\Console\Mouse object
     */
    protected static $_instance = null;

    /**
     * Whether the mouse is tracked or not.
     *
     * @var \Hoa\Console\Mouse bool
     */
    protected static $_enabled  = false;

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener object
     */
    protected $_on              = null;



    /**
     * Constructor.
     *
     * @access  private
     * @return  void
     */
    private function __construct ( ) {

        $this->_on = new \Hoa\Core\Event\Listener($this, array(
            'mouseup',
            'mousedown',
            'wheelup',
            'wheeldown',
        ));

        return;
    }

    /**
     * Singleton.
     *
     * @access  public
     * @return  \Hoa\Console\Mouse
     */
    public static function getInstance ( ) {

        if(null === static::$_instance)
            static::$_instance = new static();

        return static::$_instance;
    }

    /**
     * Track the mouse.
     *
     * @access  public
     * @return  bool
     */
    public static function track ( ) {

        if(true === static::$_enabled)
            return;

        static::$_enabled = true;

        echo "\033[1;2'z";
        echo "\033[?1000h";
        echo "\033[?1003h";

        $instance = static::getInstance();
        $bucket   = array(
            'x'      => 0,
            'y'      => 0,
            'button' => null,
            'shift'  => false,
            'meta'   => false,
            'ctrl'   => false
        );
        $read = array(STDIN);

        while(true) {

            @stream_select($read, $write, $except, 30);

            $string = fread(STDIN, 1);

            if("\033" !== $string)
                continue;

            $string .= $char = fread(STDIN, 1);

            if('[' !== $char)
                continue;

            $string .= $char = fread(STDIN, 1);

            if('M' !== $char)
                continue;

            $data = fread(STDIN, 3);
            $cb   = ord($data[0]);
            $cx   = ord($data[1]) - 32;
            $cy   = ord($data[2]) - 32;

            $bucket['x']     = $cx;
            $bucket['y']     = $cy;
            $bucket['shift'] = 0 !== ($cb &  4);
            $bucket['meta']  = 0 !== ($cb &  8);
            $bucket['ctrl']  = 0 !== ($cb & 16);

            $cb  = ($cb | 28) ^ 28; // 28 = 4 | 8 | 16
            $cb -= 32;

            switch($cb) {

                case 64:
                    $instance->_on->fire(
                        'wheelup',
                        new \Hoa\Core\Event\Bucket($bucket)
                    );
                  break;

                case 65:
                    $instance->_on->fire(
                        'wheeldown',
                        new \Hoa\Core\Event\Bucket($bucket)
                    );
                  break;

                case 3:
                    $instance->_on->fire(
                        'mouseup',
                        new \Hoa\Core\Event\Bucket($bucket)
                    );
                    $bucket['button'] = null;
                  break;

                default:
                    if(0 === $cb)
                        $bucket['button'] = 'left';
                    elseif(1 === $cb)
                        $bucket['button'] = 'middle';
                    elseif(2 === $cb)
                        $bucket['button'] = 'right';
                    else {

                        // hover
                        continue 2;
                    }

                    $instance->_on->fire(
                        'mousedown',
                        new \Hoa\Core\Event\Bucket($bucket)
                    );
            }
        }

        return;
    }

    /**
     * Untrack the mouse.
     *
     * @access  public
     * @return  void
     */
    public static function untrack ( ) {

        if(false === static::$_enabled)
            return;

        echo "\033[?1003l";
        echo "\033[?1000l";

        static::$_enabled = false;

        return;
    }

    /**
     * Attach a callable to a listenable component.
     *
     * @access  public
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $callable      Callable.
     * @return  \Hoa\Core\Event\Listenable
     * @throw   \Hoa\Core\Exception
     */
    public function on ( $listenerId, $callable ) {

        $this->_on->attach($listenerId, $callable);

        return $this;
    }
}

}

namespace {

/**
 * Advanced interaction.
 */
Hoa\Console::advancedInteraction();

/**
 * Untrack mouse.
 */
Hoa\Core::registerShutdownFunction('\Hoa\Console\Mouse', 'untrack');

}
