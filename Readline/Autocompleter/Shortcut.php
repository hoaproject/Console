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
 * \Hoa\Console\Readline\Autocompleter
 */
-> import('Console.Readline.Autocompleter.~');

}

namespace Hoa\Console\Readline\Autocompleter {

/**
 * Class \Hoa\Console\Readline\Autocompleter\Shortcut.
 *
 * The simplest auto-completer.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Shortcut implements Autocompleter {

    /**
     * List of words.
     *
     * @var \Hoa\Console\Readline\Autocompleter\Shortcut array
     */
    protected $_shortcuts = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   array  $shortcuts    Shortcuts.
     * @return  void
     */
    public function __construct ( Array $shortcuts ) {

        $this->setShortcuts($shortcuts);

        return;
    }

    /**
     * Expand a shortcut.
     * Returns a full-word or an array of full-words.
     *
     * @access  public
     * @param   string  $prefix    Prefix to autocomplete.
     * @return  string
     */
    public function complete ( $prefix ) {
        foreach($this->getShortcuts() as $shortcut=>$word)
            if($shortcut === $prefix) return $word;
        
        return null;
    }

    /**
     * Set list of shortcuts.
     *
     * @access  public
     * @param   array  $shortcuts    Shortcuts.
     * @return  array
     */
    public function setShortcuts ( Array $shortcuts ) {

        $old          = $this->_shortcuts;
        $this->_shortcuts = $shortcuts;

        return $old;
    }

    /**
     * Get list of shortcuts.
     *
     * @access  public
     * @return  array
     */
    public function getShortcuts ( ) {

        return $this->_shortcuts;
    }
}

}
