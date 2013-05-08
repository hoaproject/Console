<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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
 * \Hoa\Stream
 */
-> import('Stream.~')

/**
 * \Hoa\Stream\IStream\In
 */
-> import('Stream.I~.In')

/**
 * \Hoa\Stream\IStream\Out
 */
-> import('Stream.I~.Out')

/**
 * \Hoa\Stream\IStream\Pathable
 */
-> import('Stream.I~.Pathable');

}

namespace Hoa\Console {

/**
 * Class \Hoa\Console\System.
 *
 * Manipulate a processus as a stream.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class          Processus
    extends    \Hoa\Stream
    implements \Hoa\Stream\IStream\In,
               \Hoa\Stream\IStream\Out,
               \Hoa\Stream\IStream\Pathable {

    /**
     * Signal: terminal line hangup (terminate process).
     *
     * @const int
     */
    const SIGHUP    =  1;

    /**
     * Signal: interrupt program (terminate process).
     *
     * @const int
     */
    const SIGINT    =  2;

    /**
     * Signal: quit program (create core image).
     *
     * @const int
     */
    const SIGQUIT   =  3;

    /**
     * Signal: illegal instruction (create core image).
     *
     * @const int
     */
    const SIGILL    =  4;

    /**
     * Signal: trace trap (create core image).
     *
     * @const int
     */
    const SIGTRAP   =  5;

    /**
     * Signal: abort program, formerly SIGIOT (create core image).
     *
     * @const int
     */
    const SIGABRT   =  6;

    /**
     * Signal: emulate instruction executed (create core image).
     *
     * @const int
     */
    const SIGEMT    =  7;

    /**
     * Signal: floating-point exception (create core image).
     *
     * @const int
     */
    const SIGFPE    =  8;

    /**
     * Signal: kill program (terminate process).
     *
     * @const int
     */
    const SIGKILL   =  9;

    /**
     * Signal: bus error.
     *
     * @const int
     */
    const SIGBUS    = 10;

    /**
     * Signal: segmentation violation (create core image).
     *
     * @const int
     */
    const SIGSEGV   = 11;

    /**
     * Signal: non-existent system call invoked (create core image).
     *
     * @const int
     */
    const SIGSYS    = 12;

    /**
     * Signal: write on a pipe with no reader (terminate process).
     *
     * @const int
     */
    const SIGPIPE   = 13;

    /**
     * Signal: real-time timer expired (terminate process).
     *
     * @const int
     */
    const SIGALRM   = 14;

    /**
     * Signal: software termination signal (terminate process).
     *
     * @const int
     */
    const SIGTERM   = 15;

    /**
     * Signal: urgent condition present on socket (discard signal).
     *
     * @const int
     */
    const SIGURG    = 16;

    /**
     * Signal: stop, cannot be caught or ignored  (stop proces).
     *
     * @const int
     */
    const SIGSTOP   = 17;

    /**
     * Signal: stop signal generated from keyboard (stop process).
     *
     * @const int
     */
    const SIGTSTP   = 18;

    /**
     * Signal: continue after stop (discard signal).
     *
     * @const int
     */
    const SIGCONT   = 19;

    /**
     * Signal: child status has changed (discard signal).
     *
     * @const int
     */
    const SIGCHLD   = 20;

    /**
     * Signal: background read attempted from control terminal (stop process).
     *
     * @const int
     */
    const SIGTTIN   = 21;

    /**
     * Signal: background write attempted to control terminal (stop process).
     *
     * @const int
     */
    const SIGTTOU   = 22;

    /**
     * Signal: I/O is possible on a descriptor, see fcntl(2) (discard signal).
     *
     * @const int
     */
    const SIGIO     = 23;

    /**
     * Signal: cpu time limit exceeded, see setrlimit(2) (terminate process).
     *
     * @const int
     */
    const SIGXCPU   = 24;

    /**
     * Signal: file size limit exceeded, see setrlimit(2) (terminate process).
     *
     * @const int
     */
    const SIGXFSZ   = 25;

    /**
     * Signal: virtual time alarm, see setitimer(2) (terminate process).
     *
     * @const int
     */
    const SIGVTALRM = 26;

    /**
     * Signal: profiling timer alarm, see setitimer(2) (terminate process).
     *
     * @const int
     */
    const SIGPROF   = 27;

    /**
     * Signal: Window size change (discard signal).
     *
     * @const int
     */
    const SIGWINCH  = 28;

    /**
     * Signal: status request from keyboard (discard signal).
     *
     * @const int
     */
    const SIGINFO   = 29;

    /**
     * Signal: User defined signal 1 (terminate process).
     *
     * @const int
     */
    const SIGUSR1   = 30;

    /**
     * Signal: User defined signal 2 (terminate process).
     *
     * @const int
     */
    const SIGUSR2   = 31;

    /**
     * Command name.
     *
     * @var \Hoa\Console\Processus string
     */
    protected $_command     = null;

    /**
     * Command options (options => value, or input).
     *
     * @var \Hoa\Console\Processus array
     */
    protected $_options     = array();

    /**
     * Current working directory.
     *
     * @var \Hoa\Console\Processus string
     */
    protected $_cwd         = null;

    /**
     * Environment.
     *
     * @var \Hoa\Console\Processus array
     */
    protected $_environment = null;

    /**
     * Timeout.
     *
     * @var \Hoa\Console\Processus int
     */
    protected $_timeout     = 30;

    /**
     * Descriptor.
     *
     * @var \Hoa\Console\Processus array
     */
    protected $_descriptors = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w')
    );

    /**
     * Pipe descriptors of the processus.
     *
     * @var \Hoa\Console\Processus string
     */
    protected $_pipes       = null;



    /**
     * Start a processus.
     *
     * @access  public
     * @param   string  $command        Command name.
     * @param   array   $options        Command options.
     * @param   array   $descriptors    Descriptors (descriptor => mode —r, w or
     *                                  a—).
     * @param   string  $cwd            Current working directory.
     * @param   array   $environment    Environment.
     * @return  void
     * @throw   \Hoa\Console\Exception
     */
    public function __construct (       $command,
                                  Array $options     = null,
                                  Array $descriptors = null,
                                        $cwd         = null,
                                  Array $environment = null,
                                        $timeout     = 30 ) {

        $this->setCommand($command);

        if(null !== $options)
            $this->setOptions($options);

        if(null !== $descriptors) {

            $this->_descriptors = array();

            foreach($descriptors as $descriptor => $nature) {

                if(isset($this->_descriptors[$descriptor]))
                    throw new Exception(
                        'Pipe descriptor %d already exists, cannot ' .
                        'redefine it.',
                        0, $descriptor);

                $this->_descriptors[$descriptor] = $nature;
            }
        }

        $this->setCwd($cwd ?: getcwd());

        if(null !== $environment)
            $this->setEnvironment($environment);

        $this->setTimeout($timeout);
        parent::__construct($this->getCommandLine(), null, true);
        $this->_on->addIds(array('input', 'output', 'timeout', 'start', 'stop'));

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @access  protected
     * @param   string               $streamName    Stream name (e.g. path or URL).
     * @param   \Hoa\Stream\Context  $context       Context.
     * @return  resource
     */
    protected function &_open ( $streamName, \Hoa\Stream\Context $context = null ) {

        $out = @proc_open(
            $streamName,
            $this->_descriptors,
            $this->_pipes,
            $this->getCwd(),
            $this->getEnvironment()
        );

        if(false === $out)
            throw new Exception(
                'Something wrong happen when running %s.',
                1, $streamName);

        return $out;
    }

    /**
     * Close the current stream.
     *
     * @access  protected
     * @return  bool
     */
    protected function _close ( ) {

        foreach($this->_pipes as $pipe)
            @fclose($pipe);

        return @proc_close($this->getStream());
    }

    /**
     * Run the process and fire events (amongst start, stop, input, output and
     * timeout).
     * If an event returns false, it will close the current pipe.
     * For a simple run without firing events, use the $this->open() method.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $this->open();
        $this->_on->fire('start', new \Hoa\Core\Event\Bucket());

        $_read   = array();
        $_write  = array();
        $_except = array();

        foreach($this->_pipes as $p => $pipe) {

            stream_set_blocking($pipe, false);

            switch($this->_descriptors[$p][1]) {

                case 'r':
                    $_write[] = $pipe;
                  break;

                case 'w':
                case 'a':
                    $_read[]  = $pipe;
                  break;
            }
        }

        while(true) {

            $read   = $_read;
            $write  = $_write;
            $except = $_except;
            $select = stream_select($read, $write, $except, $this->getTimeout());

            if(0 === $select) {

                $this->_on->fire('timeout', new \Hoa\Core\Event\Bucket());

                break;
            }

            foreach($read as $i => $_r) {

                $pipe = array_search($_r, $this->_pipes);
                $line = $this->readLine($pipe);

                if(false === $line)
                    $result = array(false);
                else
                    $result = $this->_on->fire(
                        'output',
                        new \Hoa\Core\Event\Bucket(array(
                            'pipe' => $pipe,
                            'line' => $line
                        ))
                    );

                if(true === feof($_r) || in_array(false, $result, true)) {

                    fclose($_r);
                    unset($_read[$i]);

                    break;
                }
            }

            foreach($write as $j => $_w) {

                $result = $this->_on->fire(
                    'input',
                    new \Hoa\Core\Event\Bucket(array(
                        'pipe' => array_search($_w, $this->_pipes)
                    ))
                );

                if(true === feof($_w) || in_array(false, $result, true)) {

                    fclose($_w);
                    unset($_write[$j]);
                }
            }

            if(empty($_read))
                break;
        }

        $this->_on->fire('stop', new \Hoa\Core\Event\Bucket());

        return;
    }

    /**
     * Get pipe resource.
     *
     * @access  protected
     * @param   int  $pipe    Pipe descriptor.
     * @return  resource
     * @throw   \Hoa\Console\Exception
     */
    protected function getPipe ( $pipe ) {

        if(!isset($this->_pipes[$pipe]))
            throw new Exception(
                'Pipe descriptor %d does not exist, cannot read from it.',
                2, $pipe);

        return $this->_pipes[$pipe];
    }

    /**
     * Test for end-of-file.
     *
     * @access  public
     * @param   int  $pipe    Pipe descriptor.
     * @return  bool
     */
    public function eof ( $pipe = 1 ) {

        return feof($this->getPipe($pipe));
    }

    /**
     * Read n characters.
     *
     * @access  public
     * @param   int  $length    Length.
     * @param   int  $pipe      Pipe descriptor.
     * @return  string
     * @throw   \Hoa\Console\Exception
     */
    public function read ( $length, $pipe = 1 ) {

        if(0 > $length)
            throw new Exception(
                'Length must be greater than 0, given %d.', 3, $length);

        return fread($this->getPipe($pipe), $length);
    }

    /**
     * Alias of $this->read().
     *
     * @access  public
     * @param   int  $length    Length.
     * @param   int  $pipe      Pipe descriptor.
     * @return  string
     */
    public function readString ( $length, $pipe = 1 ) {

        return $this->read($length, $pipe);
    }

    /**
     * Read a character.
     *
     * @access  public
     * @param   int  $pipe    Pipe descriptor.
     * @return  string
     */
    public function readCharacter ( $pipe = 1 ) {

        return fgetc($this->getPipe($pipe));
    }

    /**
     * Read a boolean.
     *
     * @access  public
     * @param   int  $pipe    Pipe descriptor.
     * @return  bool
     */
    public function readBoolean ( $pipe = 1 ) {

        return (bool) $this->read(1, $pipe);
    }

    /**
     * Read an integer.
     *
     * @access  public
     * @param   int  $length    Length.
     * @param   int  $pipe      Pipe descriptor.
     * @return  int
     */
    public function readInteger ( $length = 1, $pipe = 1 ) {

        return (int) $this->read($length, $pipe);
    }

    /**
     * Read a float.
     *
     * @access  public
     * @param   int     $length    Length.
     * @param   int     $pipe      Pipe descriptor.
     * @return  float
     */
    public function readFloat ( $length = 1, $pipe = 1 ) {

        return (float) $this->read($length, $pipe);
    }

    /**
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @param   int     $pipe      Pipe descriptor.
     * @return  array
     */
    public function readArray ( $format = null, $pipe = 1 ) {

        return $this->scanf($format, $pipe);
    }

    /**
     * Read a line.
     *
     * @access  public
     * @param   int  $pipe    Pipe descriptor.
     * @return  string
     */
    public function readLine ( $pipe = 1 ) {

        return stream_get_line($this->getPipe($pipe), 1 << 15, "\n");
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @access  public
     * @param   int  $pipe    Pipe descriptor.
     * @return  string
     */
    public function readAll ( $pipe = 1 ) {

        return stream_get_contents($this->getPipe($pipe));
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @access  public
     * @param   string  $format    Format (see printf's formats).
     * @param   int     $pipe      Pipe descriptor.
     * @return  array
     */
    public function scanf ( $format, $pipe = 1 ) {

        return fscanf($this->getPipe($pipe), $format);
    }

    /**
     * Write n characters.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $length    Length.
     * @param   int     $pipe      Pipe descriptor.
     * @return  mixed
     * @throw   \Hoa\Console\Exception
     */
    public function write ( $string, $length, $pipe = 0 ) {

        if(0 > $length)
            throw new Exception(
                'Length must be greater than 0, given %d.', 4, $length);

        return fwrite($this->getPipe($pipe), $string, $length);
    }

    /**
     * Write a string.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $pipe      Pipe descriptor.
     * @return  mixed
     */
    public function writeString ( $string, $pipe = 0 ) {

        $string = (string) $string;

        return $this->write($string, strlen($string), $pipe);
    }

    /**
     * Write a character.
     *
     * @access  public
     * @param   string  $char    Character.
     * @param   int     $pipe    Pipe descriptor.
     * @return  mixed
     */
    public function writeCharacter ( $char, $pipe = 0 ) {

        return $this->write((string) $char[0], 1, $pipe);
    }

    /**
     * Write a boolean.
     *
     * @access  public
     * @param   bool  $boolean    Boolean.
     * param    int   $pipe       Pipe descriptor.
     * @return  mixed
     */
    public function writeBoolean ( $boolean, $pipe = 0 ) {

        return $this->write((string) (bool) $boolean, 1, $pipe);
    }

    /**
     * Write an integer.
     *
     * @access  public
     * @param   int  $integer    Integer.
     * @param   int  $pipe       Pipe descriptor.
     * @return  mixed
     */
    public function writeInteger ( $integer, $pipe = 0 ) {

        $integer = (string) (int) $integer;

        return $this->write($integer, strlen($integer), $pipe);
    }

    /**
     * Write a float.
     *
     * @access  public
     * @param   float   $float    Float.
     * @param   int     $pipe     Pipe descriptor.
     * @return  mixed
     */
    public function writeFloat ( $float, $pipe = 0 ) {

        $float = (string) (float) $float;

        return $this->write($float, strlen($float), $pipe);
    }

    /**
     * Write an array.
     *
     * @access  public
     * @param   array   $array    Array.
     * @param   int     $pipe     Pipe descriptor.
     * @return  mixed
     */
    public function writeArray ( Array $array, $pipe = 0 ) {

        $array = var_export($array, true);

        return $this->write($array, strlen($array), $pipe);
    }

    /**
     * Write a line.
     *
     * @access  public
     * @param   string  $line    Line.
     * @param   int     $pipe    Pipe descriptor.
     * @return  mixed
     */
    public function writeLine ( $line, $pipe = 0 ) {

        if(false === $n = strpos($line, "\n"))
            return $this->write($line . "\n", strlen($line) + 1, $pipe);

        ++$n;

        return $this->write(substr($line, 0, $n), $n, $pipe);
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @access  public
     * @param   string  $string    String.
     * @param   int     $pipe      Pipe descriptor.
     * @return  mixed
     */
    public function writeAll ( $string, $pipe = 0 ) {

        return $this->write($string, strlen($string), $pipe);
    }

    /**
     * Truncate a file to a given length.
     *
     * @access  public
     * @param   int  $size    Size.
     * @param   int  $pipe    Pipe descriptor.
     * @return  bool
     */
    public function truncate ( $size, $pipe = 0 ) {

        return ftruncate($this->getPipe($pipe), $size);
    }

    /**
     * Get filename component of path.
     *
     * @access  public
     * @return  string
     */
    public function getBasename ( ) {

        return basename($this->getCommand());
    }

    /**
     * Get directory name component of path.
     *
     * @access  public
     * @return  string
     */
    public function getDirname ( ) {

        return dirname($this->getCommand());
    }

    /**
     * Get status.
     *
     * @access  public
     * @return  array
     */
    public function getStatus ( ) {

        return proc_get_status($this->getStream());
    }

    /**
     * Get exit code (alias of $this->getStatus()['exitcode']);
     *
     * @access  public
     * @return  int
     */
    public function getExitCode ( ) {

        $handle = $this->getStatus();

        return $handle['exitcode'];
    }

    /**
     * Whether the processus have ended successfully.
     *
     * @access  public
     * @return  boolean
     */
    public function isSuccessful () {

        return 0 === $this->getExitCode();
    }

    /**
     * Terminate the process.
     *
     * @access  public
     * @param   int  $signal    Signal, amongst self::SIGHUP, SIGINT, SIGQUIT,
     *                          SIGABRT, SIGKILL, SIGALRM and SIGTERM. Default
     *                          is self::SIGTERM.
     * @return  bool
     */
    public function terminate ( $signal = self::SIGTERM ) {

        return proc_terminate($this->getStream(), $signal);
    }

    /**
     * Set command name.
     *
     * @access  protected
     * @param   string  $command    Command name.
     * @return  string
     */
    protected function setCommand ( $command ) {

        $old            = $this->_command;
        $this->_command = escapeshellcmd($command);

        return $old;
    }

    /**
     * Get command name.
     *
     * @access  public
     * @return  string
     */
    public function getCommand ( ) {

        return $this->_command;
    }

    /**
     * Set command options.
     *
     * @access  protected
     * @param   array  $options    Options (option => value, or input).
     * @return  array
     */
    protected function setOptions ( Array $options ) {

        foreach($options as &$option)
            $option = escapeshellarg($option);

        $old            = $this->_options;
        $this->_options = $options;

        return $old;
    }

    /**
     * Get options.
     *
     * @access  public
     * @return  array
     */
    public function getOptions ( ) {

        return $this->_options;
    }

    /**
     * Get command-line.
     *
     * @access  public
     * @return  string
     */
    public function getCommandLine ( ) {

        $out = $this->getCommand();

        foreach($this->getOptions() as $key => $value)
            if(!is_int($key))
                $out .= ' ' . $key . '=' . $value;
            else
                $out .= ' ' . $value;

        return $out;
    }

    /**
     * Set current working directory of the process.
     *
     * @access  protected
     * @param   string  $cwd    Current working directory.
     * @return  string
     */
    protected function setCwd ( $cwd ) {

        $old        = $this->_cwd;
        $this->_cwd = $cwd;

        return $old;
    }

    /**
     * Get current working directory of the process.
     *
     * @access  public
     * @return  string
     */
    public function getCwd ( ) {

        return $this->_cwd;
    }

    /**
     * Set environment of the process.
     *
     * @access  protected
     * @param   array  $environment    Environment.
     * @return  array
     */
    protected function setEnvironment ( Array $environment ) {

        $old                = $this->_environment;
        $this->_environment = $environment;

        return $old;
    }

    /**
     * Get environment of the process.
     *
     * @access  public
     * @return  array
     */
    public function getEnvironment ( ) {

        return $this->_environment;
    }

    /**
     * Set timeout of the process.
     *
     * @access  public
     * @param   int  $timeout    Timeout.
     * @return  int
     */
    public function setTimeout ( $timeout ) {

        $old            = $this->_timeout;
        $this->_timeout = $timeout;

        return $old;
    }

    /**
     * Get timeout of the process.
     *
     * @access  public
     * @return  int
     */
    public function getTimeout ( ) {

        return $this->_timeout;
    }

    /**
     * Found the place of a binary.
     *
     * @access  public
     * @param   string  $binary    Binary.
     * @return  string
     */
    public static function locate ( $binary ) {

        if(isset($_ENV['PATH'])) {

            $separator = ':';
            $path      = &$_ENV['PATH'];
        }
        elseif(isset($_SERVER['PATH'])) {

            $separator = ':';
            $path      = &$_SERVER['PATH'];
        }
        elseif(isset($_SERVER['Path'])) {

            $separator = ';';
            $path      = &$_SERVER['Path'];
        }

        foreach(explode($separator, $path) as $directory)
            if(true === file_exists($out = $directory . DS . $binary))
                return $out;

        return null;
    }

    /**
     * Quick process execution.
     * Returns only the STDOUT.
     *
     * @access  public
     * @return  string
     */
    public static function execute ( $commandLine ) {

        return rtrim(shell_exec(escapeshellcmd($commandLine)));
    }
}

}
