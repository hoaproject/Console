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
 * Class \Hoa\Console\Readline\Autocomplete\Word.
 *
 * Simple word completion for Readline's autocomplete
 *
 * @author     Kevin Gravier <kevin@mrkmg.com>
 * @copyright  Copyright © 2012 Kevin Gravier
 * @license    New BSD License
 */

class CommandLine {
    /**
     * Avaliable commands
     * 
     * @var \Hoa\Console\Readline\Autocomplete\Words array
     */
    protected $_command = [];

    /**
     * Lookup table
     * 
     * @var \Hoa\Console\Readline\Autocomplete\Words array
     */
    protected $_lookup = [];

    /**
     * Initialize the Word autocomplete.
     *
     * @access  public
     * @return  void
     */
    public function __construct($commands,$lookup){
        if(!is_array($commands)) throw new Exception('$commands is not an array.');
        if(!is_array($lookup)) throw new Exception('$lookup is not an array.');
        $this->_command = $commands;
        $this->_lookup = $lookup;
    }

    /**
     * Invoke method
     * 
     * @access public
     * @return array
     */
    public function __invoke($context,$word){
        $matched = array();
        if(strlen($context)){
            $contexts = $this->explode($context);
            if(!isset($this->_command[$contexts[0]])) return [];
            $param_string = $this->_command[$contexts[0]];
            $params = $this->explode($param_string);
            $current_location = count($contexts)-1;
            if(count($params) <= $current_location){
                $last_param = $params[count($params)-1];
                if(substr($last_param,strlen($last_param)-1) == '+')
                    $matched = $this->processParam($context,$word,$last_param);
                else return [];
            }
            else{
                $matched = $this->processParam($context,$word,$params[$current_location]);
            }
        }
        else{
            $matched = array_keys($this->_command);
        }

        return $this->filterResults($word,$matched);
    }

    private function filterResults($word,$results){
        $matches = [];
        if(!$word_length = strlen($word)) return $results;
        foreach($results as $check)
            if(substr($check,0,$word_length) == $word) $matches[] = $check;
        return $matches;
    }

    private function explode($context){
        $broken = explode(' ',$context);
        $broken = array_filter($broken,function($v){return !empty($v); });
        array_walk($broken,function(&$v,$k){ $v = trim($v); });
        return $broken;
    }

    private function processParam($context,$word,$param){
        switch(substr($param,0,1)){  //Using a switch to allow for future addition of more string modifiers
            case '$':
                $param = trim($param,'+$');
                if(isset($this->_lookup[$param])){
                    $paramobj = $this->_lookup[$param];
                    if(is_array($paramobj)) return $paramobj;
                    elseif(is_callable($paramobj)) return $paramobj($context,$word);
                    else return [];
                }
                else{
                    return [];
                }
                break;
            default:
                return [$param];
                break;
        }
    }
}

}
