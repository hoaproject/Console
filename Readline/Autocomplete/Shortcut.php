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

namespace Hoa\Console\Readline\Autocomplete {

/**
 * Class \Hoa\Console\Readline\Autocomplete\Shortcut.
 *
 * Simple shortcut replacement for Readline's autocomplete
 *
 * @author     Kevin Gravier <kevin@mrkmg.com>
 * @copyright  Copyright © 2012 Kevin Gravier
 * @license    New BSD License
 */

class Shortcut {
    /**
     * Avaliable shortcuts
     * 
     * @var \Hoa\Console\Readline\Autocomplete\Words array
     */
    protected $_shortcuts = [];

    /**
     * Initialize the shortcut autocomplete.
     *
     * @access  public
     * @return  void
     */
    public function __construct($shortcuts){
        if(!is_array($shortcuts)) throw new Exception('$shortcuts is not an array.');
        $this->_shortcuts = $shortcuts;
    }

    /**
     * Invoke method
     * 
     * @access public
     * @return array
     */
    public function __invoke($context,$word){
        return isset($this->_shortcuts[$word])?[$this->_shortcuts[$word]]:[];
    }
}

}
