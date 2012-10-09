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

/*
 * \Hoa\Console\System
 */
-> import('Console.System');

}

namespace Hoa\Console\Readline {

/**
 * Class \Hoa\Console\Readline.
 *
 * Read, edit, bind… a line from STDIN.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

class Readline {

    /**
     * State: continue to read.
     *
     * @const int
     */
    const STATE_CONTINUE = 1;

    /**
     * State: stop to read.
     *
     * @const int
     */
    const STATE_BREAK    = 2;

    /**
     * State: no output the current buffer.
     *
     * @const int
     */
    const STATE_NO_ECHO  = 4;

    /**
     * Old STTY options.
     *
     * @var \Hoa\Console\Readline string
     */
    protected $_oldStty        = null;

    /**
     * Current editing line.
     *
     * @var \Hoa\Console\Readline string
     */
    protected $_line           = null;

    /**
     * Current editing line seek.
     *
     * @var \Hoa\Console\Readline int
     */
    protected $_lineCurrent    = 0;

    /**
     * Current editing line length.
     *
     * @var \Hoa\Console\Readline int
     */
    protected $_lineLength     = 0;

    /**
     * Current buffer (most of the time, a char).
     *
     * @var \Hoa\Console\Readline string
     */
    protected $_buffer         = null;

    /**
     * Mapping.
     *
     * @var \Hoa\Console\Readline array
     */
    protected $_mapping        = array();

    /**
     * History.
     *
     * @var \Hoa\Console\Readline array
     */
    protected $_history        = array();

    /**
     * History current position.
     *
     * @var \Hoa\Console\Readline int
     */
    protected $_historyCurrent = 0;

    /**
     * History size.
     *
     * @var \Hoa\Console\Readline int
     */
    protected $_historySize    = 0;

    /**
     * Prefix.
     *
     * @var \Hoa\Console\Readline string
     */
    protected $_prefix         = null;



    /**
     * Initialize the readline editor.
     *
     * @access  public
     * @return  void
     */
    public function __construct ( ) {

        if(OS_WIN)
            return;

        $this->_oldStty = \Hoa\Console\System::execute('stty -g');
        \Hoa\Console\System::execute('stty -echo -icanon min 1 time 0');
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');

        $this->_mapping["\033[A"] = xcallable($this, '_bindArrowUp');
        $this->_mapping["\033[B"] = xcallable($this, '_bindArrowDown');
        $this->_mapping["\033[C"] = xcallable($this, '_bindArrowRight');
        $this->_mapping["\033[D"] = xcallable($this, '_bindArrowLeft');
        $this->_mapping["\001"]   = xcallable($this, '_bindControlA');
        $this->_mapping["\002"]   = xcallable($this, '_bindControlB');
        $this->_mapping["\005"]   = xcallable($this, '_bindControlE');
        $this->_mapping["\006"]   = xcallable($this, '_bindControlF');
        $this->_mapping["\010"]   =
        $this->_mapping["\177"]   = xcallable($this, '_bindBackspace');
        $this->_mapping["\027"]   = xcallable($this, '_bindControlW');
        $this->_mapping["\n"]     = xcallable($this, '_bindNewline');

        return;
    }

    /**
     * Read a line from STDIN.
     *
     * @access  public
     * @return  string
     */
    public function readLine ( $prefix = null ) {

        if(OS_WIN) {

            $this->_write($prefix);

            return fgets(STDIN);
        }

        $this->resetLine();
        $this->setPrefix($prefix);
        $this->_write($prefix);
        $read = array(STDIN);

        while(true) {

            @stream_select($read, $write, $except, 30, 0);

            if(empty($read)) {

                $read = array(STDIN);
                continue;
            }

            $char          = $this->_read();
            $this->_buffer = $char;

            if(   isset($this->_mapping[$char])
               && is_callable($this->_mapping[$char])) {

                $mapping = $this->_mapping[$char];
                $return  = $mapping($this);
            }
            else {

                if($this->getLineLength() == $this->getLineCurrent()) {

                    $this->appendLine($this->_buffer);
                    $return = static::STATE_CONTINUE;
                }
                else {

                    $this->insertLine($this->_buffer);
                    $tail          = mb_substr(
                        $this->getLine(),
                        $this->getLineCurrent() - 1
                    );
                    $this->_buffer = "\033[K" . $tail . str_repeat(
                        "\033[D",
                        mb_strlen($tail) - 1
                    );

                    $return = self::STATE_CONTINUE;
                }
            }

            if(0 === ($return & self::STATE_NO_ECHO))
                $this->_write($this->_buffer);

            if(0 !== ($return & self::STATE_BREAK))
                break;
        }

        return $this->getLine();
    }

    /**
     * Add mappings.
     *
     * @access  public
     * @param   array  $mappings    Mappings.
     * @return  void
     */
    public function addMappings ( Array $mappings ) {

        foreach($mappings as $key => $mapping)
            $this->addMapping($key, $mapping);

        return;
    }

    /**
     * Add a mapping.
     * Supported key:
     *     • \e[… for \033[…;
     *     • \C-… for Cltr-…;
     *     • abc for a simple mapping.
     * A mapping is a callable that has only one parameter of type
     * Hoa\Console\Readline and that returns a self::STATE_* constant.
     *
     * @access  public
     * @param   string  $key        Key.
     * @param   mixed   $mapping    Mapping (a callable).
     * @return  void
     */
    public function addMapping ( $key, $mapping ) {

        if('\e[' == substr($key, 0, 3))
            $this->_mapping["\033[" . substr($key, 3)] = $mapping;
        elseif('\C-' == substr($key, 0, 3)) {

            $_key = ord(strtolower(substr($key, 3))) - 96;
            $this->_mapping[chr($_key)] = $mapping;
        }
        else
            $this->_mapping[$key] = $mapping;

        return;
    }

    /**
     * Add an entry in the history.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  void
     */
    public function addHistory ( $line = null ) {

        if(empty($line))
            return;

        $this->_history[]      = $line;
        $this->_historyCurrent = $this->_historySize++;

        return;
    }

    /**
     * Clear history.
     *
     * @access  public
     * @return  void
     */
    public function clearHistory ( ) {

        unset($this->_history);
        $this->_history        = array();
        $this->_historyCurrent = 0;
        $this->_historySize    = 1;

        return;
    }

    /**
     * Get an entry in the history.
     *
     * @access  public
     * @param   int  $i    Index of the entry.
     * @return  string
     */
    public function getHistory ( $i = null ) {

        if(null === $i)
            $i = $this->_historyCurrent;

        if(!isset($this->_history[$i]))
            return null;

        return $this->_history[$i];
    }

    /**
     * Go backward in the history.
     *
     * @access  public
     * @return  string
     */
    public function previousHistory ( ) {

        if(0 >= $this->_historyCurrent)
            return $this->getHistory(0);

        return $this->getHistory($this->_historyCurrent--);
    }

    /**
     * Go forward in the history.
     *
     * @access  public
     * @return  string
     */
    public function nextHistory ( ) {

        if($this->_historyCurrent + 1 >= $this->_historySize)
            return $this->getLine();

        return $this->getHistory(++$this->_historyCurrent);
    }

    /**
     * Get current line.
     *
     * @access  public
     * @return  string
     */
    public function getLine ( ) {

        return $this->_line;
    }

    /**
     * Append to current line.
     *
     * @access  public
     * @param   string  $append    String to append.
     * @return  void
     */
    public function appendLine ( $append ) {

        $this->_line       .= $append;
        $this->_lineLength  = mb_strlen($this->_line);
        $this->_lineCurrent = $this->_lineLength;

        return;
    }

    /**
     * Insert into current line at the current seek.
     *
     * @access  public
     * @param   string  $insert    String to insert.
     * @return  void
     */
    public function insertLine ( $insert ) {

        if($this->_lineLength == $this->_lineCurrent)
            return $this->appendLine($insert);

        $this->_line         = mb_substr($this->_line, 0, $this->_lineCurrent) .
                               $insert .
                               mb_substr($this->_line, $this->_lineCurrent);
        $this->_lineLength   = mb_strlen($this->_line);
        $this->_lineCurrent += mb_strlen($insert);

        return;
    }

    /**
     * Reset current line.
     *
     * @access  public
     * @return  void
     */
    protected function resetLine ( ) {

        $this->_line        = null;
        $this->_lineCurrent = 0;
        $this->_lineLength  = 0;

        return;
    }

    /**
     * Get current line seek.
     *
     * @access  public
     * @return  int
     */
    public function getLineCurrent ( ) {

        return $this->_lineCurrent;
    }

    /**
     * Get current line length.
     *
     * @access  public
     * @return  int
     */
    public function getLineLength ( ) {

        return $this->_lineLength;
    }

    /**
     * Set prefix.
     *
     * @access  public
     * @return  void
     */
    public function setPrefix ( $prefix ) {

        $this->_prefix = $prefix;

        return;
    }

    /**
     * Get prefix.
     *
     * @access  public
     * @return  string
     */
    public function getPrefix ( ) {

        return $this->_prefix;
    }

    /**
     * Get buffer. Not for user.
     *
     * @access  public
     * @return  string
     */
    public function getBuffer ( ) {

        return $this->_buffer;
    }

    /**
     * Write on STDIN. Not for user.
     *
     * @access  public
     * @param   string  $string    String to write.
     * @return  void
     */
    public function _write ( $string ) {

        fwrite(STDIN, $string);

        return;
    }

    /**
     * Read on STDIN. Not for user.
     *
     * @access  public
     * @param   int  $length    Length.
     * @return  string
     */
    public function _read ( $length = 512 ) {

        return fread(STDIN, $length);
    }

    /**
     * Set current line. Not for user.
     *
     * @access  public
     * @param   string  $line    Line.
     * @return  void
     */
    public function setLine ( $line ) {

        $this->_line        = $line;
        $this->_lineLength  = mb_strlen($this->_line);
        $this->_lineCurrent = $this->_lineLength;

        return;
    }

    /**
     * Set current line seek. Not for user.
     *
     * @access  public
     * @param   int  $current    Seek.
     * @return  void
     */
    public function setLineCurrent ( $current ) {

        $this->_lineCurrent = $current;

        return;
    }

    /**
     * Set buffer. Not for user.
     *
     * @access  public
     * @param   string  $buffer    Buffer.
     * @return  string
     */
    public function setBuffer ( $buffer ) {

        $this->_buffer = $buffer;

        return;
    }

    /**
     * Up arrow binding.
     * Go backward in the history.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindArrowUp ( Readline $self ) {

        $self->_write("\r\033[K" . $self->getPrefix());
        $self->setBuffer($buffer = $self->previousHistory());
        $self->setLine($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Down arrow binding.
     * Go forward in the history.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindArrowDown ( Readline $self ) {

        $self->_write("\r\033[K" . $self->getPrefix());
        $self->setBuffer($buffer = $self->nextHistory());
        $self->setLine($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Right arrow binding.
     * Move cursor to the right.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindArrowRight ( Readline $self ) {

        if($self->getLineLength() > $self->getLineCurrent()) {

            $self->_write("\033[C");
            $self->setLineCurrent($self->getLineCurrent() + 1);
        }

        $self->setBuffer(null);

        return static::STATE_CONTINUE;
    }

    /**
     * Left arrow binding.
     * Move cursor to the left.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindArrowLeft ( Readline $self ) {

        if(0 < $self->getLineCurrent()) {

            $self->_write("\033[D");
            $self->setLineCurrent($this->getLineCurrent() - 1);
        }

        $self->setBuffer(null);

        return static::STATE_CONTINUE;
    }

    /**
     * Backspace and Control-H binding.
     * Delete the first character at the right of the cursor.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindBackspace ( Readline $self ) {

        $buffer = null;

        if(0 < $self->getLineCurrent()) {

            $self->_write("\033[D\033[K");

            if($self->getLineLength() == $current = $self->getLineCurrent())
                $self->setLine(mb_substr($this->getLine(), 0, -1));
            else {

                $line    = $self->getLine();
                $current = $self->getLineCurrent();
                $tail    = mb_substr($line, $current);
                $buffer  = $tail . str_repeat("\033[D", mb_strlen($tail));
                $self->setLine(mb_substr($line, 0, $current - 1) . $tail);
                $self->setLineCurrent($current - 1);
            }
        }

        $self->setBuffer($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-A binding.
     * Move cursor to beginning of line.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindControlA ( Readline $self ) {

        for($i = $self->getLineCurrent() - 1; 0 <= $i; --$i)
            $self->_bindArrowLeft($self);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-B binding.
     * Move cursor backward one word.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindControlB ( Readline $self ) {

        $current = $self->getLineCurrent();

        if(0 === $current)
            return static::STATE_CONTINUE;

        $words = preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        for($i = 0, $max = count($words) - 1;
            $i < $max && $words[$i + 1][1] < $current;
            ++$i);

        for($j = $words[$i][1] + 1; $current >= $j; ++$j)
            $self->_bindArrowLeft($self);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-E binding.
     * Move cursor to end of line.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindControlE ( Readline $self ) {

        for($i = $self->getLineCurrent(), $max = $self->getLineLength();
            $i < $max;
            ++$i)
            $self->_bindArrowRight($self);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-F binding.
     * Move cursor forward one word.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindControlF ( Readline $self ) {

        $current = $self->getLineCurrent();

        if($this->getLineLength() === $current)
            return static::STATE_CONTINUE;

        $words = preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        for($i = 0, $max = count($words) - 1;
            $i < $max && $words[$i][1] < $current;
            ++$i);

        if(!isset($words[$i + 1]))
            $words[$i + 1] = array(1 => $this->getLineLength());

        for($j = $words[$i + 1][1]; $j > $current; --$j)
            $self->_bindArrowRight($self);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-W binding.
     * Delete first backward word.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindControlW ( Readline $self ) {

        $current = $self->getLineCurrent();

        if(0 === $current)
            return static::STATE_CONTINUE;

        $words = preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        for($i = 0, $max = count($words) - 1;
            $i < $max && $words[$i + 1][1] < $current;
            ++$i);

        for($j = $words[$i][1] + 1; $current >= $j; ++$j)
            $self->_bindBackspace($self);

        return static::STATE_CONTINUE;
    }

    /**
     * Newline binding.
     *
     * @access  public
     * @param   \Hoa\Console\Readline  $self    Self.
     * @return  int
     */
    public function _bindNewline ( Readline $self ) {

        $self->addHistory($self->getLine());

        return static::STATE_BREAK;
    }

    /**
     * Restore STTY options.
     *
     * @access  public
     * @return  void
     */
    public function __destruct ( ) {

        \Hoa\Console\System::execute('stty ' . $this->_oldStty);

        return;
    }
}

}
