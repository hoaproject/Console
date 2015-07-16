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
 * Class \Hoa\Console.
 *
 * Util.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Console
{
    /**
     * Pipe mode: FIFO.
     *
     * @var int
     */
    const IS_FIFO      = 0;

    /**
     * Pipe mode: character.
     *
     * @var int
     */
    const IS_CHARACTER = 1;

    /**
     * Pipe mode: directory.
     *
     * @var int
     */
    const IS_DIRECTORY = 2;

    /**
     * Pipe mode: block.
     *
     * @var int
     */
    const IS_BLOCK     = 3;

    /**
     * Pipe mode: regular.
     *
     * @var int
     */
    const IS_REGULAR   = 4;

    /**
     * Pipe mode: link.
     *
     * @var int
     */
    const IS_LINK      = 5;

    /**
     * Pipe mode: socket.
     *
     * @var int
     */
    const IS_SOCKET    = 6;

    /**
     * Pipe mode: whiteout.
     *
     * @var int
     */
    const IS_WHITEOUT  = 7;

    /**
     * Advanced interaction is on.
     *
     * @var bool
     */
    private static $_advanced = null;

    /**
     * Previous STTY configuration.
     *
     * @var string
     */
    private static $_old      = null;

    /**
     * Mode.
     *
     * @var array
     */
    protected static $_mode   = [];

    /**
     * Tput.
     *
     * @var \Hoa\Console\Tput
     */
    protected static $_tput   = null;



    /**
     * Prepare the environment for advanced interactions.
     *
     * @return  bool
     */
    public static function advancedInteraction()
    {
        if (null !== self::$_advanced) {
            return self::$_advanced;
        }

        if (OS_WIN) {
            return self::$_advanced = false;
        }

        if (false === self::isDirect(STDIN)) {
            return self::$_advanced = false;
        }

        self::$_old = Processus::execute('stty -g');
        Processus::execute('stty -echo -icanon min 1 time 0');

        return self::$_advanced = true;
    }

    /**
     * Restore previous interaction options.
     *
     * @return  void
     */
    public static function restoreInteraction()
    {
        if (null === self::$_old) {
            return;
        }

        Processus::execute('stty ' . self::$_old);

        return;
    }

    /**
     * Get mode of a certain pipe.
     * Inspired by sys/stat.h.
     *
     * @param   resource  $pipe    Pipe.
     * @return  int
     */
    public static function getMode($pipe = STDIN)
    {
        $_pipe = (int) $pipe;

        if (isset(self::$_mode[$_pipe])) {
            return self::$_mode[$_pipe];
        }

        $stat = fstat($pipe);

        switch ($stat['mode'] & 0170000) {
            // named pipe (fifo).
            case 0010000:
                $mode = self::IS_FIFO;

                break;

            // character special.
            case 0020000:
                $mode = self::IS_CHARACTER;

                break;

            // directory.
            case 0040000:
                $mode = self::IS_DIRECTORY;

                break;

            // block special.
            case 0060000:
                $mode = self::IS_BLOCK;

                break;

            // regular.
            case 0100000:
                $mode = self::IS_REGULAR;

                break;

            // symbolic link.
            case 0120000:
                $mode = self::IS_LINK;

                 break;

            // socket.
            case 0140000:
                $mode = self::IS_SOCKET;

                break;

            // whiteout.
            case 0160000:
                $mode = self::IS_WHITEOUT;

                break;

            default:
                $mode = -1;
        }

        return self::$_mode[$_pipe] = $mode;
    }

    /**
     * Check whether a certain pipe is a character device (keyboard, screen
     * etc.).
     * For example:
     *     $ php Mode.php
     * In this case, self::isDirect(STDOUT) will return true.
     *
     * @param   resource  $pipe    Pipe.
     * @return  bool
     */
    public static function isDirect($pipe)
    {
        return self::IS_CHARACTER === self::getMode($pipe);
    }

    /**
     * Check whether a certain pipe is a pipe.
     * For example:
     *     $ php Mode.php | foobar
     * In this case, self::isPipe(STDOUT) will return true.
     *
     * @param   resource  $pipe    Pipe.
     * @return  bool
     */
    public static function isPipe($pipe)
    {
        return self::IS_FIFO === self::getMode($pipe);
    }

    /**
     * Check whether a certain pipe is a redirection.
     * For example:
     *     $ php Mode.php < foobar
     * In this case, self::isRedirection(STDIN) will return true.
     *
     * @param   resource  $pipe    Pipe.
     * @return  bool
     */
    public static function isRedirection($pipe)
    {
        $mode = self::getMode($pipe);

        return
            self::IS_REGULAR   === $mode ||
            self::IS_DIRECTORY === $mode ||
            self::IS_LINK      === $mode ||
            self::IS_SOCKET    === $mode ||
            self::IS_BLOCK     === $mode;
    }

    /**
     * Get the current tput instance of the current process.
     *
     * @return  \Hoa\Console\Tput
     */
    public static function getTput()
    {
        if (null === static::$_tput) {
            static::$_tput = new Tput();
        }

        return static::$_tput;
    }
}

/**
 * Restore interaction.
 */
Core::registerShutdownFunction('Hoa\Console\Console', 'restoreInteraction');

/**
 * Flex entity.
 */
Core\Consistency::flexEntity('Hoa\Console\Console');
