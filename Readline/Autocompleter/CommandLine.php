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

namespace Hoa\Console\Readline\Autocompleter {

/**
 * Class \Hoa\Console\Readline\Autocompleter\CommandLine.
 *
 * Simple word completion for Readline's autocomplete
 *
 * @author     Kevin Gravier <kevin@mrkmg.com>
 * @copyright  Copyright © 2012 Kevin Gravier
 * @license    New BSD License
 */

class CommandLine {
    /**
     * Avaliable Commands
     * 
     * @var \Hoa\Console\Readline\Autocompleter\CommandLine array
     */
    protected $_full = [];

    /**
     * Avaliable Commands
     * 
     * @var \Hoa\Console\Readline\Autocompleter\CommandLine array
     */
    protected $_short = [];

    /**
     * Avaliable Commands
     * 
     * @var \Hoa\Console\Readline\Autocompleter\CommandLine array
     */
    protected $_value = [];

    /**
     * Initialize the CommandLine autocomplete.
     *
     * @access  public
     * @return  void
     */
    public function __construct($options){
        if(!is_array($options)) throw new Exception('$options is not an array.');
        foreach($options as $cmd=>$params){
            $this->_full[$cmd] = array_keys($params);

            $shorts = array();
            $values = array();
            foreach($params as $option=>$attributes){
                if(isset($attributes['short'])) $shorts[$attributes['short']] = $option;
                if(isset($attributes['value'])) $values[$option] = $attributes['value'];
            }
            $this->_short[$cmd] = $shorts;
            $this->_value[$cmd] = $values;
        }
    }

    /**
     * Invoke method
     * 
     * @access public
     * @return array
     */
    public function __invoke($context,$word){
        $matches = [];
        if(strlen($context)){
            $contexts = $this->explode($context);
            $cmd = $contexts[0];
            if(!isset($this->_full[$cmd])) return [];
            if(substr($word,0,1)=='-'){
                $matches = $this->returnOptions($cmd);
            }
            else{
                $count = count($contexts);
                if($count > 1){
                    $last = $contexts[$count-1];
                    if(substr($last,0,1)=='-'){
                        if(substr($last,0,2)=='--'){
                            $option = trim($last,'-');
                        }
                        else{
                            $o = trim($last,'-');
                            if(!isset($this->_short[$cmd][$o])) return [];
                            $option = $this->_short[$cmd][$o];
                        }
                        $matches = $this->returnValue($cmd,$option,$context,$word);
                    }
                    else{
                        $matches = $this->returnOptions($cmd);
                    }
                }
                else{
                    $matches = $this->returnOptions($cmd);
                }
            }
        }
        else{
            $matches = $this->returnCommands();
        }

        return $this->filterResults($word,$matches);
    }

    private function returnCommands(){
        return array_keys($this->_full);
    }

    private function returnOptions($cmd){
        $longs = $this->_full[$cmd];
        array_walk($longs,function(&$v,$k){ $v = '--'.$v;});
        $shorts = array_keys($this->_short[$cmd]);
        array_walk($shorts,function(&$v,$k){ $v = '-'.$v;});
        $options = array_merge($longs,$shorts);
        sort($options,SORT_STRING);
        return $options;
    }

    private function returnValue($cmd,$option,$context,$word){
        if(isset($this->_value[$cmd][$option])){
            if(is_array($this->_value[$cmd][$option])) return $this->_value[$cmd][$option];
            elseif(is_callable($this->_value[$cmd][$option])) return $this->_value[$cmd][$option]($context,$word);
            else return [];
        }
        else{
            return [];
        }
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
}

}
