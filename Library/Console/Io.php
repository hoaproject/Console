<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console\Exception
 */
-> import('Console.Exception')

/**
 * \Hoa\Console\Environment\Window
 */
-> import('Console.Environment.Window');

/**
 * Whether they are not defined.
 */
_define('STDIN',  fopen('php://stdin' , 'rb'));
_define('STDOUT', fopen('php://stdout', 'wb'));
_define('STDERR', fopen('php://stderr', 'wb'));

}

namespace Hoa\Console {

/**
 * Class \Hoa\Console\Io.
 *
 * This class allows to treat the STDIN, STDOUT, and STDERR stream.
 * Methods have options to make a bit more than just writte in a i/o stream,
 * like wordwrap the text, or prepare a question (y/n) etc.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Io {

    /**
     * Write a \n after the cout message.
     *
     * @cont bool
     */
    const NEW_LINE      = true;

    /**
     * Do not write a \n after the cout message.
     *
     * @const bool
     */
    const NO_NEW_LINE   = false;

    /**
     * Wordwrap text when using cout() method.
     *
     * @const bool
     */
    const WORDWRAP      = true;

    /**
     * Do not wordwrap text when using cout() method.
     *
     * @const bool
     */
    const NO_WORDWRAP   = false;

    /**
     * Whether the cin() should have a normal comportement.
     *
     * @const int
     */
    const TYPE_NORMAL   = 0;

    /**
     * Whether the cin() method should receive an answer to a question. The
     * answer should be : “yes” or “no”.
     *
     * @const int
     */
    const TYPE_YES_NO   = 1;

    /**
     * Whether the cin() method should receive a password. In this case, do not
     * show the password.
     *
     * @const int
     */
    const TYPE_PASSWORD = 2;



    /**
     * Get data from the standard input.
     *
     * @access  public
     * @param   string  $prefix          The prefix text before getting data
     *                                   from STDIN.
     * @param   bool    $comportement    If the cin should receive an answer to
     *                                   a question, a password, or a normal
     *                                   string.
     * @param   bool    $ln              Whether add a \n at the end of data.
     * @param   bool
     * @return  mixed
     * @throw   \Hoa\Console\Exception
     * @todo    Remake the password system (maybe with the fflush() function,
     *          see the unix:///coreutils-6.11/lib/getpass.c)
     */
    public static function cin ( $prefix = null, $comportement = self::TYPE_NORMAL,
                                 $ln     = self::NEW_LINE ) {

        if($comportement === self::TYPE_YES_NO)
            $prefix .= ' (y/n)';

        self::cout($prefix, $ln);

        // Hack for password, bad hack.
        if($comportement === self::TYPE_PASSWORD)
            if(!OS_WIN && function_exists('posix_isatty'))
                self::cout("\033[8m", self::NO_NEW_LINE);

        if(false === $in = fgets(STDIN))
            throw new Exception(
                'Cannot read the standard input.', 0);

        if($comportement === self::TYPE_PASSWORD)
            if(!OS_WIN && function_exists('posix_isatty'))
                self::cout("\033[0m", self::NO_NEW_LINE);

        $in = trim($in);

        if($comportement !== self::TYPE_YES_NO)
            return $in;

        $return = false;

        switch($in) {

            case 'y':
            case 'ye':
            case 'yes':
            case 'yeah': // hihi
                $return = true;
              break;

            default:
                $return = false;
        }

        return $return;
    }

    /**
     * Write data into the standard output.
     *
     * @access  public
     * @param   mixed   $out    Data to write.
     * @param   bool    $ln     Whether add a \n at the end of data.
     * @param   bool    $ww     Wordwrap the text or not.
     * @return  void
     * @throw   \Hoa\Console\Exception
     */
    public static function cout ( $out = null, $ln = self::NEW_LINE,
                                  $ww  = self::WORDWRAP ) {

        if(self::WORDWRAP === $ww)
            $out = wordwrap(
                $out,
                Environment\Window::getColumns(),
                "\n",
                true
            );

        if(self::NEW_LINE === $ln)
            $out .= "\n";

        if(false === @fwrite(STDOUT, $out))
            throw new Exception(
                'Cannot write in the standard output. Data was %s.', 1, $out);

        return;
    }
}

}

namespace {

/**
 * Alias of \Hoa\Console\Io::cin.
 *
 * @access  public
 * @param   string  $prefix          The prefix text before getting data
 *                                   from STDIN.
 * @param   bool    $comportement    If the cin should receive an answer to
 *                                   a question, a password, or a normal
 *                                   string.
 * @param   bool    $ln              Whether add a \n at the end of data.
 * @return  string
 * @throw   \Hoa\Console\Exception
 */
if(!ƒ('cin')) {
function cin ( $prefix       = null,
               $comportement = \Hoa\Console\Io::TYPE_NORMAL,
               $ln           = \Hoa\Console\Io::NEW_LINE ) {

    return \Hoa\Console\Io::cin($prefix, $comportement, $ln);
}}

/**
 * Alias of \Hoa\Console\Io::cout.
 *
 * @access  public
 * @param   mixed   $out    Data to write.
 * @param   bool    $ln     Whether add a \n at the end of data.
 * @param   bool    $ww     Wordwrap the text or not.
 * @return  void
 * @throw   \Hoa\Console\Exception
 */
if(!ƒ('cout')) {
function cout ( $out = null, $ln = \Hoa\Console\Io::NEW_LINE,
                $ww  = \Hoa\Console\Io::WORDWRAP ) {

    return \Hoa\Console\Io::cout($out, $ln, $ww);
}}

}
