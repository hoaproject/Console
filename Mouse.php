<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
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

namespace Hoa\Console;

use Hoa\Core;

/**
 * Class \Hoa\Console\Mouse.
 *
 * Allow to listen the mouse.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Mouse implements Core\Event\Listenable
{
    /**
     * Singleton.
     *
     * @var \Hoa\Console\Mouse
     */
    protected static $_instance = null;

    /**
     * Whether the mouse is tracked or not.
     *
     * @var bool
     */
    protected static $_enabled  = false;

    /**
     * Listeners.
     *
     * @var \Hoa\Core\Event\Listener
     */
    protected $_on              = null;



    /**
     * Constructor.
     *
     * @return  void
     */
    private function __construct()
    {
        $this->_on = new Core\Event\Listener($this, [
            'mouseup',
            'mousedown',
            'wheelup',
            'wheeldown',
        ]);

        return;
    }

    /**
     * Singleton.
     *
     * @return  \Hoa\Console\Mouse
     */
    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * Track the mouse.
     *
     * @return  bool
     */
    public static function track()
    {
        if (true === static::$_enabled) {
            return;
        }

        static::$_enabled = true;

        echo "\033[1;2'z";
        echo "\033[?1000h";
        echo "\033[?1003h";

        $instance = static::getInstance();
        $bucket   = [
            'x'      => 0,
            'y'      => 0,
            'button' => null,
            'shift'  => false,
            'meta'   => false,
            'ctrl'   => false
        ];
        $read = [STDIN];

        while (true) {
            @stream_select($read, $write, $except, 30);

            $string = fread(STDIN, 1);

            if ("\033" !== $string) {
                continue;
            }

            $char = fread(STDIN, 1);

            if ('[' !== $char) {
                continue;
            }

            $char = fread(STDIN, 1);

            if ('M' !== $char) {
                continue;
            }

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

            switch ($cb) {
                case 64:
                    $instance->_on->fire(
                        'wheelup',
                        new Core\Event\Bucket($bucket)
                    );

                    break;

                case 65:
                    $instance->_on->fire(
                        'wheeldown',
                        new Core\Event\Bucket($bucket)
                    );

                    break;

                case 3:
                    $instance->_on->fire(
                        'mouseup',
                        new Core\Event\Bucket($bucket)
                    );
                    $bucket['button'] = null;

                    break;

                default:
                    if (0 === $cb) {
                        $bucket['button'] = 'left';
                    } elseif (1 === $cb) {
                        $bucket['button'] = 'middle';
                    } elseif (2 === $cb) {
                        $bucket['button'] = 'right';
                    } else {

                        // hover
                        continue 2;
                    }

                    $instance->_on->fire(
                        'mousedown',
                        new Core\Event\Bucket($bucket)
                    );
            }
        }

        return;
    }

    /**
     * Untrack the mouse.
     *
     * @return  void
     */
    public static function untrack()
    {
        if (false === static::$_enabled) {
            return;
        }

        echo "\033[?1003l";
        echo "\033[?1000l";

        static::$_enabled = false;

        return;
    }

    /**
     * Attach a callable to a listenable component.
     *
     * @param   string  $listenerId    Listener ID.
     * @param   mixed   $callable      Callable.
     * @return  \Hoa\Core\Event\Listenable
     * @throws  \Hoa\Core\Exception
     */
    public function on($listenerId, $callable)
    {
        $this->_on->attach($listenerId, $callable);

        return $this;
    }
}

/**
 * Advanced interaction.
 */
Console::advancedInteraction();

/**
 * Untrack mouse.
 */
Core::registerShutdownFunction('\Hoa\Console\Mouse', 'untrack');
