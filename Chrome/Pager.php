<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Console\Processus
 */
-> import('Console.Processus');

}

namespace Hoa\Console\Chrome {

/**
 * Class \Hoa\Console\Chrome\Pager.
 *
 * Use a pager for the output buffer. Example:
 *
 *     ob_start('Hoa\Console\Chrome\Pager::less');
 *     echo file_get_contents(__FILE__);
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Pager {

    /**
     * Represent LESS(1).
     *
     * @const string
     */
    const LESS = 'less';

    /**
     * Represent MORE(1).
     *
     * @const string
     */
    const MORE = 'more';



    /**
     * Use less.
     *
     * @access  public
     * @param   string  $output    Output (from the output buffer).
     * @param   int     $mode      Mode (from the output buffer).
     * @return  string
     */
    public static function less ( $output, $mode ) {

        return self::pager($output, $mode, self::LESS);
    }

    /**
     * Use more.
     *
     * @access  public
     * @param   string  $output    Output (from the output buffer).
     * @param   int     $mode      Mode (from the output buffer).
     * @return  string
     */
    public static function more ( $output, $mode ) {

        return self::pager($output, $mode, self::MORE);
    }

    /**
     * Use pager set in the environment (i.e. $_ENV['PAGER']).
     *
     * @access  public
     * @param   string  $output    Output (from the output buffer).
     * @param   int     $mode      Mode (from the output buffer).
     * @param   string  $type      Type. Please, see self::LESS or self::MORE.
     * @return  string
     */
    public static function pager ( $output, $mode, $type = null ) {

        static $process = null;
        static $pipes   = null;

        if($mode & PHP_OUTPUT_HANDLER_START) {

            $pager = null !== $type
                         ? \Hoa\Console\Processus::locate($type)
                         : (isset($_ENV['PAGER']) ? $_ENV['PAGER'] : null);

            if(null === $pager)
                return $output;

            $process = new \Hoa\Console\Processus(
                $pager,
                null,
                array(0 => array('pipe', 'r'))
            );
            $process->open();
        }

        $process->writeAll($output);

        if($mode & PHP_OUTPUT_HANDLER_FINAL)
            $process->close();

        return null;
    }
}

}

namespace {

/**
 * Define PHP_OUTPUT_HANDLER_FINAL if PHP < 5.4.
 */
PHP_VERSION_ID < 50400 &&
    _define('PHP_OUTPUT_HANDLER_FINAL', PHP_OUTPUT_HANDLER_END);

}
