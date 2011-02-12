<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
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
