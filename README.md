![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Console

This library allows to interact easily with a terminal: getoption, cursor,
window, processus, readline etc.

## Quick usage

We propose a quick overview of some features: cursor, window, readline,
processus and finally getOption.

Note: some features need an “advanced interaction”, thus we should write
`Hoa\Console::advancedInteraction();`.

### Cursor

The `Hoa\Console\Cursor` class allows to manipulate the cursor. Here is a list
of some operations:

  * `move`;
  * `moveTo`;
  * `save`;
  * `restore`;
  * `clear`;
  * `hide`;
  * `show`;
  * `getPosition`;
  * `colorize`;
  * etc.

The API is very straightforward. For example, we can use `l`, `left` or `←` to
move the cursor on the left column. Thus we move the cursor to the left 3-times
and then to the top 2-times:

    Hoa\Console\Cursor::move('← ← ← ↑ ↑');

This method moves the cursor relatively from its current position, but we are
able to move the cursor with absolute coordinates:

    Hoa\Console\Cursor::moveTo(13, 42);

We are also able to save the current cursor position, to move, clear etc., and
then to restore the saved position:

    Hoa\Console\Cursor::save();     // save
    Hoa\Console\Cursor::move('↓');  // move below
    Hoa\Console\Cursor::clear('↔'); // clear the line
    echo 'Something below…';        // write something
    Hoa\Console\Cursor::restore();  // restore

Another example with color:

    Hoa\Console\Cursor::colorize(
        'underlined foreground(yellow) background(#932e2e)'
    );

Please, read the API documentation for more informations, and note that Windows
support is very weak.

### Window

The `Hoa\Console\Window` class allows to manipulate the window. Here is a list
of some operations:

  * `setSize`;
  * `getSize`;
  * `moveTo`;
  * `getPosition`;
  * `minimize`;
  * `setTitle`;
  * `getTitle`;
  * `copy`;
  * etc.

Furthermore, we have the `hoa://Event/Console/Window:resize` event channel to
listen when the window has been resized.

For example, we resize the window to 40 lines and 80 columns, and then we move
the window to 400px horizontally and 100px vertically:

    Hoa\Console\Window::setSize(40, 80);
    Hoa\Console\Window::moveTo(400, 100);

If we do not like our user, we are able to minimize its window:

    Hoa\Console\Window::minimize();

We are also able to set or get the title of the window:

    Hoa\Console\Window::setTitle('My awesome application');

Finally, if we have a complex application layout, we can repaint it when the
window is resized by listening the `hoa://Event/Console/Window:resize` event
channel:

    event('hoa://Event/Console/Window:resize')
        ->attach(function ( Hoa\Core\Event\Bucket $bucket ) {

            $data = $bucket->getData();
            $size = $data['size'];

            echo 'New dimensions: ', $size['x'], ' lines x ',
                 $size['y'], ' columns.', "\n";
        });

Please, read the API documentation for more informations, and note that Windows
support is very weak.

### Readline

The `Hoa\Console\Readline` class proposes an advanced readline which allows the
following operations:

  * edition;
  * history;
  * autocompletion.

It supports UTF-8. It is based on bindings, and here are some:

  * `arrow up` and `arrow down`: move in the history;
  * `arrow left` and `arrow right`: move the cursor left and right;
  * `Ctrl-A`: move to the beginning of the line;
  * `Ctrl-E`: move to the end of the line;
  * `Ctrl-B`: move backward of one word;
  * `Ctrl-F`: move forward of one word;
  * `Ctrl-W`: delete first backard word;
  * `Backspace`: delete first backward character;
  * `Enter`: submit the line;
  * `Tab`: autocomplete.

Thus, to read one line:

    $readline = new Hoa\Console\Readline();
    $line     = $readline->readLine('> '); // “> ” is the prefix of the line.

The `Hoa\Console\Readline\Password` allows the same operations but without
printing on STDOUT.

    $password = new Hoa\Console\Readline\Password();
    $line     = $password->readLine('password: ');

We are able to add a mapping with the help of the
`Hoa\Console\Readline::addMapping` method. We use `\e[…` for `\033[`, `\C-…` for
`Ctrl-…` and a character for the rest. We can associate a character or a
callable:

    $readline->addMapping('a', 'z'); // crazy, we replace “a” by “z”.
    $readline->addMapping('\C-R', function ( $readline ) {

        // do something when pressing Ctrl-R.
    });

We are also able to manipulate the history, thanks to the `addHistory`,
`clearHistory`, `getHistory`, `previousHistory` and `nextHistory` methods on the
`Hoa\Console\Readline` class.

Finally, we have autocompleters that are enabled on `Tab`. If one solution is
proposed, it will be inserted directly. If many solutions are proposed, we are
able to navigate in a menu to select the solution (with the help of keyboard
arrows, Enter, Esc etc.).

On Windows, a readline is equivalent to a simple `fgets(STDIN)`.

### Processus

The `Hoa\Console\Processus` class allows to manipulate processus as a stream
which implements `Hoa\Stream\IStream\In`, `Hoa\Stream\IStream\Out` and
`Hoa\Stream\IStream\Pathable` interfaces.

Basically, we can read STDOUT like this:

    $processus = new Hoa\Console\Processus('ls');
    echo $processus->readAll();

And we can write on STDIN like this:

    $processus->writeAll('foobar');

etc. This is very classical.

We are also able to read and write on more pipes than 0 (STDOUT), 1 (STDIN) and
2 (STDERR). In the same way, we can set the current working directory of the
processus and its environment.

We can execute a processus quickly without using a stream with the help of the
`Hoa\Console\Processus::execute` method.

### GetOption

The `Hoa\Console\Parser` and `Hoa\Console\GetOption` classes allow to parse a
command-line and get options and inputs values easily.

First, we need to parse a command-line, such as:

    $parser = new Hoa\Console\Parser();
    $parser->parse('-s --long=value input');

Second, we need to define our options:

    $options = new Hoa\Console\GetOption(
        array(
            //   long name                 type                  short name
            //       ↓                      ↓                         ↓
            array('short', Hoa\Console\GetOption::NO_ARGUMENT,       's'),
            array('long',  Hoa\Console\GetOption::REQUIRED_ARGUMENT, 'l')
        ),
        $parser
    );

And finally, we iterate over options:

    $short = false;
    $long  = null;

    //          short name                 value
    //               ↓                        ↓
    while(false !== $c = $options->getOption($v)) switch($c) {

        case 's':
            $short = true;
          break;

        case 'l':
            $long = $v;
          break;
    }

    var_dump($short, $long); // bool(true) and string(5) "value".

Please, see API documentation of `Hoa\Console\Parser` to see all supported forms
of options (flags or switches, long or short ones, inputs etc.).

It also support typos in options. In this case, we have to add:

        case '__ambiguous':
            $options->resolveOptionAmbiguity($v);
          break;

If one solution is found, it will select this one automatically, else it will
raise an exception. This exception is caught by `Hoa\Console\Dispatcher\Kit`
when using the `hoa` script and a prompt is proposed.

Thanks to `Hoa\Router` and `Hoa\Dispatcher` (with its dedicated kit
`Hoa\Console\Dispatcher\Kit`), we are able to build commands easily. Please, see
all `Bin/` directories in different libraries (for example
`Hoa\Core\Bin\Resolve`) and `Hoa/Core/Bin/Hoa.php` to learn more.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
