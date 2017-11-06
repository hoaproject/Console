<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

namespace Hoa\Console\Test\Unit;

use Hoa\Console as LUT;
use Hoa\Console\Cursor as SUT;
use Hoa\File;
use Hoa\Test;

/**
 * Class \Hoa\Console\Test\Unit\Cursor.
 *
 * Test suite of the cursor.
 *
 * @license    New BSD License
 */
class Cursor extends Test\Unit\Suite
{
    public function beforeTestMethod($methodName): void
    {
        parent::beforeTestMethod($methodName);
        LUT::setTput(new LUT\Tput('hoa://Library/Console/Source/Terminfo/78/xterm-256color'));

        return;
    }

    public function case_move_u(): void
    {
        $this
            ->when(SUT::move('u'))
            ->then
                ->output
                    ->isEqualTo("\033[1A");
    }

    public function case_move_up(): void
    {
        $this
            ->when(SUT::move('up'))
            ->then
                ->output
                    ->isEqualTo("\033[1A");
    }

    public function case_move_↑(): void
    {
        $this
            ->when(SUT::move('↑'))
            ->then
                ->output
                    ->isEqualTo("\033[1A");
    }

    public function case_move_↑_repeated(): void
    {
        $this
            ->when(SUT::move('↑', 42))
            ->then
                ->output
                    ->isEqualTo("\033[42A");
    }

    public function case_move_r(): void
    {
        $this
            ->when(SUT::move('r'))
            ->then
                ->output
                    ->isEqualTo("\033[1C");
    }

    public function case_move_right(): void
    {
        $this
            ->when(SUT::move('right'))
            ->then
                ->output
                    ->isEqualTo("\033[1C");
    }

    public function case_move_→(): void
    {
        $this
            ->when(SUT::move('→'))
            ->then
                ->output
                    ->isEqualTo("\033[1C");
    }

    public function case_move_→_repeated(): void
    {
        $this
            ->when(SUT::move('→', 42))
            ->then
                ->output
                    ->isEqualTo("\033[42C");
    }

    public function case_move_d(): void
    {
        $this
            ->when(SUT::move('d'))
            ->then
                ->output
                    ->isEqualTo("\033[1B");
    }

    public function case_move_down(): void
    {
        $this
            ->when(SUT::move('down'))
            ->then
                ->output
                    ->isEqualTo("\033[1B");
    }

    public function case_move_↓(): void
    {
        $this
            ->when(SUT::move('↓'))
            ->then
                ->output
                    ->isEqualTo("\033[1B");
    }

    public function case_move_↓_repeated(): void
    {
        $this
            ->when(SUT::move('↓', 42))
            ->then
                ->output
                    ->isEqualTo("\033[42B");
    }

    public function case_move_l(): void
    {
        $this
            ->when(SUT::move('l'))
            ->then
                ->output
                    ->isEqualTo("\033[1D");
    }

    public function case_move_left(): void
    {
        $this
            ->when(SUT::move('left'))
            ->then
                ->output
                    ->isEqualTo("\033[1D");
    }

    public function case_move_←(): void
    {
        $this
            ->when(SUT::move('←'))
            ->then
                ->output
                    ->isEqualTo("\033[1D");
    }

    public function case_move_←_repeated(): void
    {
        $this
            ->when(SUT::move('←', 42))
            ->then
                ->output
                    ->isEqualTo("\033[42D");
    }

    public function case_move_sequence(): void
    {
        $this
            ->when(SUT::move('↑ → ↓ ←'))
            ->then
                ->output
                    ->isEqualTo("\033[1A\033[1C\033[1B\033[1D");
    }

    public function case_move_to_x_y(): void
    {
        $this
            ->when(SUT::moveTo(7, 42))
            ->then
                ->output
                    ->isEqualTo("\033[42;7H");
    }

    public function case_move_to_x(): void
    {
        $this
            ->given(
                $file = new File\ReadWrite('hoa://Test/Vfs/Input?type=file'),
                $file->writeAll("\033[42;7R"),
                $file->rewind(),
                $input  = LUT::setInput(new LUT\Input($file))
            )
            ->when(SUT::moveTo(153))
            ->then
                ->output
                    ->isEqualTo("\033[6n\033[42;153H");
    }

    public function case_move_to_y(): void
    {
        $this
            ->given(
                $file = new File\ReadWrite('hoa://Test/Vfs/Input?type=file'),
                $file->writeAll("\033[42;7R"),
                $file->rewind(),
                $input  = LUT::setInput(new LUT\Input($file))
            )
            ->when(SUT::moveTo(null, 153))
            ->then
                ->output
                    ->isEqualTo("\033[6n\033[153;7H");
    }

    public function case_get_position(): void
    {
        $this
            ->given(
                $file = new File\ReadWrite('hoa://Test/Vfs/Input?type=file'),
                $file->writeAll("\033[42;7R"),
                $file->rewind(),
                $input  = LUT::setInput(new LUT\Input($file))
            )
            ->when($result = SUT::getPosition())
            ->then
                ->output
                    ->isEqualTo("\033[6n")
                ->array($result)
                    ->isEqualTo([
                        'x' => 7,
                        'y' => 42
                    ]);
    }

    public function case_save(): void
    {
        $this
            ->when(SUT::save())
            ->then
                ->output
                    ->isEqualTo("\0337");
    }

    public function case_restore(): void
    {
        $this
            ->when(SUT::restore())
            ->then
                ->output
                    ->isEqualTo("\0338");
    }

    public function case_clear_a(): void
    {
        $this
            ->when(SUT::clear('a'))
            ->then
                ->output
                    ->isEqualTo("\033[H\033[2J\033[1;1H");
    }

    public function case_clear_all(): void
    {
        $this
            ->when(SUT::clear('all'))
            ->then
                ->output
                    ->isEqualTo("\033[H\033[2J\033[1;1H");
    }

    public function case_clear_↕(): void
    {
        $this
            ->when(SUT::clear('↕'))
            ->then
                ->output
                    ->isEqualTo("\033[H\033[2J\033[1;1H");
    }

    public function case_clear_u(): void
    {
        $this
            ->when(SUT::clear('u'))
            ->then
                ->output
                    ->isEqualTo("\033[1J");
    }

    public function case_clear_up(): void
    {
        $this
            ->when(SUT::clear('up'))
            ->then
                ->output
                    ->isEqualTo("\033[1J");
    }

    public function case_clear_↑(): void
    {
        $this
            ->when(SUT::clear('↑'))
            ->then
                ->output
                    ->isEqualTo("\033[1J");
    }

    public function case_clear_r(): void
    {
        $this
            ->when(SUT::clear('r'))
            ->then
                ->output
                    ->isEqualTo("\033[K");
    }

    public function case_clear_right(): void
    {
        $this
            ->when(SUT::clear('right'))
            ->then
                ->output
                    ->isEqualTo("\033[K");
    }

    public function case_clear_→(): void
    {
        $this
            ->when(SUT::clear('→'))
            ->then
                ->output
                    ->isEqualTo("\033[K");
    }

    public function case_clear_d(): void
    {
        $this
            ->when(SUT::clear('d'))
            ->then
                ->output
                    ->isEqualTo("\033[J");
    }

    public function case_clear_down(): void
    {
        $this
            ->when(SUT::clear('down'))
            ->then
                ->output
                    ->isEqualTo("\033[J");
    }

    public function case_clear_↓(): void
    {
        $this
            ->when(SUT::clear('↓'))
            ->then
                ->output
                    ->isEqualTo("\033[J");
    }

    public function case_clear_l(): void
    {
        $this
            ->when(SUT::clear('l'))
            ->then
                ->output
                    ->isEqualTo("\033[1K");
    }

    public function case_clear_left(): void
    {
        $this
            ->when(SUT::clear('left'))
            ->then
                ->output
                    ->isEqualTo("\033[1K");
    }

    public function case_clear_←(): void
    {
        $this
            ->when(SUT::clear('←'))
            ->then
                ->output
                    ->isEqualTo("\033[1K");
    }

    public function case_clear_line(): void
    {
        $this
            ->when(SUT::clear('line'))
            ->then
                ->output
                    ->isEqualTo("\r\033[K");
    }

    public function case_clear_↔(): void
    {
        $this
            ->when(SUT::clear('↔'))
            ->then
                ->output
                    ->isEqualTo("\r\033[K");
    }

    public function case_hide(): void
    {
        $this
            ->when(SUT::hide())
            ->then
                ->output
                    ->isEqualTo("\033[?25l");
    }

    public function case_show(): void
    {
        $this
            ->when(SUT::show())
            ->then
                ->output
                    ->isEqualTo("\033[?12;25h");
    }

    public function case_colorize_n(): void
    {
        $this
            ->when(SUT::colorize('n'))
            ->then
                ->output
                    ->isEqualTo("\033[0m");
    }

    public function case_colorize_normal(): void
    {
        $this
            ->when(SUT::colorize('normal'))
            ->then
                ->output
                    ->isEqualTo("\033[0m");
    }

    public function case_colorize_normal_repeated(): void
    {
        $this
            ->when(SUT::colorize('n normal'))
            ->then
                ->output
                    ->isEqualTo("\033[0;0m");
    }

    public function case_colorize_b(): void
    {
        $this
            ->when(SUT::colorize('b'))
            ->then
                ->output
                    ->isEqualTo("\033[1m");
    }

    public function case_colorize_bold(): void
    {
        $this
            ->when(SUT::colorize('bold'))
            ->then
                ->output
                    ->isEqualTo("\033[1m");
    }

    public function case_colorize_u(): void
    {
        $this
            ->when(SUT::colorize('u'))
            ->then
                ->output
                    ->isEqualTo("\033[4m");
    }

    public function case_colorize_underlined(): void
    {
        $this
            ->when(SUT::colorize('underlined'))
            ->then
                ->output
                    ->isEqualTo("\033[4m");
    }

    public function case_colorize_bl(): void
    {
        $this
            ->when(SUT::colorize('bl'))
            ->then
                ->output
                    ->isEqualTo("\033[5m");
    }

    public function case_colorize_blink(): void
    {
        $this
            ->when(SUT::colorize('blink'))
            ->then
                ->output
                    ->isEqualTo("\033[5m");
    }

    public function case_colorize_i(): void
    {
        $this
            ->when(SUT::colorize('i'))
            ->then
                ->output
                    ->isEqualTo("\033[7m");
    }

    public function case_colorize_inverse(): void
    {
        $this
            ->when(SUT::colorize('inverse'))
            ->then
                ->output
                    ->isEqualTo("\033[7m");
    }

    public function case_colorize_not_b(): void
    {
        $this
            ->when(SUT::colorize('!b'))
            ->then
                ->output
                    ->isEqualTo("\033[22m");
    }

    public function case_colorize_not_bold(): void
    {
        $this
            ->when(SUT::colorize('!bold'))
            ->then
                ->output
                    ->isEqualTo("\033[22m");
    }

    public function case_colorize_not_u(): void
    {
        $this
            ->when(SUT::colorize('!u'))
            ->then
                ->output
                    ->isEqualTo("\033[24m");
    }

    public function case_colorize_not_underlined(): void
    {
        $this
            ->when(SUT::colorize('!underlined'))
            ->then
                ->output
                    ->isEqualTo("\033[24m");
    }

    public function case_colorize_not_bl(): void
    {
        $this
            ->when(SUT::colorize('!bl'))
            ->then
                ->output
                    ->isEqualTo("\033[25m");
    }

    public function case_colorize_not_blink(): void
    {
        $this
            ->when(SUT::colorize('!blink'))
            ->then
                ->output
                    ->isEqualTo("\033[25m");
    }

    public function case_colorize_not_i(): void
    {
        $this
            ->when(SUT::colorize('!i'))
            ->then
                ->output
                    ->isEqualTo("\033[27m");
    }

    public function case_colorize_not_inverse(): void
    {
        $this
            ->when(SUT::colorize('!inverse'))
            ->then
                ->output
                    ->isEqualTo("\033[27m");
    }

    public function case_colorize_fg_black(): void
    {
        $this
            ->when(SUT::colorize('fg(black)'))
            ->then
                ->output
                    ->isEqualTo("\033[30m");
    }

    public function case_colorize_foreground_black(): void
    {
        $this
            ->when(SUT::colorize('foreground(black)'))
            ->then
                ->output
                    ->isEqualTo("\033[30m");
    }

    public function case_colorize_fg_red(): void
    {
        $this
            ->when(SUT::colorize('fg(red)'))
            ->then
                ->output
                    ->isEqualTo("\033[31m");
    }

    public function case_colorize_fg_green(): void
    {
        $this
            ->when(SUT::colorize('fg(green)'))
            ->then
                ->output
                    ->isEqualTo("\033[32m");
    }

    public function case_colorize_fg_yellow(): void
    {
        $this
            ->when(SUT::colorize('fg(yellow)'))
            ->then
                ->output
                    ->isEqualTo("\033[33m");
    }

    public function case_colorize_fg_blue(): void
    {
        $this
            ->when(SUT::colorize('fg(blue)'))
            ->then
                ->output
                    ->isEqualTo("\033[34m");
    }

    public function case_colorize_fg_magenta(): void
    {
        $this
            ->when(SUT::colorize('fg(magenta)'))
            ->then
                ->output
                    ->isEqualTo("\033[35m");
    }

    public function case_colorize_fg_cyan(): void
    {
        $this
            ->when(SUT::colorize('fg(cyan)'))
            ->then
                ->output
                    ->isEqualTo("\033[36m");
    }

    public function case_colorize_fg_white(): void
    {
        $this
            ->when(SUT::colorize('fg(white)'))
            ->then
                ->output
                    ->isEqualTo("\033[37m");
    }

    public function case_colorize_fg_default(): void
    {
        $this
            ->when(SUT::colorize('fg(default)'))
            ->then
                ->output
                    ->isEqualTo("\033[39m");
    }

    public function case_colorize_bg_black(): void
    {
        $this
            ->when(SUT::colorize('bg(black)'))
            ->then
                ->output
                    ->isEqualTo("\033[40m");
    }

    public function case_colorize_background_black(): void
    {
        $this
            ->when(SUT::colorize('background(black)'))
            ->then
                ->output
                    ->isEqualTo("\033[40m");
    }

    public function case_colorize_bg_red(): void
    {
        $this
            ->when(SUT::colorize('bg(red)'))
            ->then
                ->output
                    ->isEqualTo("\033[41m");
    }

    public function case_colorize_bg_green(): void
    {
        $this
            ->when(SUT::colorize('bg(green)'))
            ->then
                ->output
                    ->isEqualTo("\033[42m");
    }

    public function case_colorize_bg_yellow(): void
    {
        $this
            ->when(SUT::colorize('bg(yellow)'))
            ->then
                ->output
                    ->isEqualTo("\033[43m");
    }

    public function case_colorize_bg_blue(): void
    {
        $this
            ->when(SUT::colorize('bg(blue)'))
            ->then
                ->output
                    ->isEqualTo("\033[44m");
    }

    public function case_colorize_bg_magenta(): void
    {
        $this
            ->when(SUT::colorize('bg(magenta)'))
            ->then
                ->output
                    ->isEqualTo("\033[45m");
    }

    public function case_colorize_bg_cyan(): void
    {
        $this
            ->when(SUT::colorize('bg(cyan)'))
            ->then
                ->output
                    ->isEqualTo("\033[46m");
    }

    public function case_colorize_bg_white(): void
    {
        $this
            ->when(SUT::colorize('bg(white)'))
            ->then
                ->output
                    ->isEqualTo("\033[47m");
    }

    public function case_colorize_bg_default(): void
    {
        $this
            ->when(SUT::colorize('bg(default)'))
            ->then
                ->output
                    ->isEqualTo("\033[49m");
    }

    public function case_colorize_foreground_ff0066(): void
    {
        $this
            ->when(SUT::colorize('foreground(#ff0066)'))
            ->then
                ->output
                    ->isEqualTo("\033[38;5;197m");
    }

    public function case_colorize_background_ff0066(): void
    {
        $this
            ->when(SUT::colorize('background(#ff0066)'))
            ->then
                ->output
                    ->isEqualTo("\033[48;5;197m");
    }

    public function case_colorize_foreground_color_index(): void
    {
        $this
            ->when(SUT::colorize('foreground(42)'))
            ->then
                ->output
                    ->isEqualTo("\033[38;5;42m");
    }

    public function case_change_color(): void
    {
        $this
            ->when(SUT::changeColor(35, 0xff0066))
            ->then
                ->output
                    ->isEqualTo("\033]4;35;ff0066\033\\");
    }

    public function case_set_style_b(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('b'))
            ->then
                ->output
                    ->isEqualTo("\033[1 q");
    }

    public function case_set_style_block(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('block'))
            ->then
                ->output
                    ->isEqualTo("\033[1 q");
    }

    public function case_set_style_▋(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('▋'))
            ->then
                ->output
                    ->isEqualTo("\033[1 q");
    }

    public function case_set_style_block_no_blink(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('block', false))
            ->then
                ->output
                    ->isEqualTo("\033[2 q");
    }

    public function case_set_style_u(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('u'))
            ->then
                ->output
                    ->isEqualTo("\033[2 q");
    }

    public function case_set_style_underline(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('underline'))
            ->then
                ->output
                    ->isEqualTo("\033[2 q");
    }

    public function case_set_style__(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('_'))
            ->then
                ->output
                    ->isEqualTo("\033[2 q");
    }

    public function case_set_style_underline_no_blink(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('underline', false))
            ->then
                ->output
                    ->isEqualTo("\033[3 q");
    }

    public function case_set_style_v(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('v'))
            ->then
                ->output
                    ->isEqualTo("\033[5 q");
    }

    public function case_set_style_vertical(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('vertical'))
            ->then
                ->output
                    ->isEqualTo("\033[5 q");
    }

    public function case_set_style_pipe(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('|'))
            ->then
                ->output
                    ->isEqualTo("\033[5 q");
    }

    public function case_set_style_vertical_no_blink(): void
    {
        $this
            ->given($this->constant->OS_WIN = false)
            ->when(SUT::setStyle('vertical', false))
            ->then
                ->output
                    ->isEqualTo("\033[6 q");
    }

    public function case_set_style_on_windows(): void
    {
        $this
            ->given($this->constant->OS_WIN = true)
            ->when(SUT::setStyle('b'))
            ->then
                ->output
                    ->isEmpty();
    }

    public function case_bip(): void
    {
        $this
            ->when(SUT::bip())
            ->then
                ->output
                    ->isEqualTo("\007");
    }
}
