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
 * \Hoa\Console\System\Exception
 */
-> import('Console.System.Exception');

}

namespace Hoa\Console\System {

/**
 * Class \Hoa\Console\System.
 *
 * This class is a thin layer to get a system access.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007-2011 Ivan ENDERLIN.
 * @license    New BSD License
 */

class System {

    /**
     * Execute a command in the terminal.
     *
     * @access  public
     * @param   string  $command    The command to execute.
     * @return  string
     * @throw   \Hoa\Console\System\Exception
     */
    public static function execute ( $command ) {

        if(null === $command)
            throw new Exception(
                'Cannot execute a null command.', 0);

        ob_start();
        passthru($command . ' 2>&1', $return);
        $content = ob_get_contents();
        ob_end_clean();

        if($return > 0)
            throw new Exception(
                'Error when executing the command %s (returned the code %d).',
                1, array($command, $return));

        return $content;
    }

    /**
     * Escape a command argument.
     *
     * @access  public
     * @param   string  $argument    The argument.
     * @return  string
     */
    public static function escapeArgument ( $argument ) {

        return escapeshellarg($argument);
    }

    /**
     * Escape a command line.
     *
     * @access  public
     * @param   string  $command    The command.
     * @return  string
     */
    public static function escapeCommand ( $command ) {

        return escapeshellcmd($command);
    }
}

}
