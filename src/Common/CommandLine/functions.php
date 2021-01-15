<?php
declare(strict_types=1);

namespace Common\CommandLine;

use function Safe\fopen;

function stdout(string... $strings): void
{
    write_to(fopen('php://stdout', 'wb'), $strings);
}

function stderr(string... $strings): void
{
    write_to(fopen('php://stderr', 'wb'), $strings);
}

/**
 * @param resource $handle
 * @param array<string> $strings
 */
function write_to($handle, array $strings): void
{
    $timestamp = date('H:i:s') . ' ';
    foreach ($strings as $index => $string) {
        if ($index === 0) {
            $writeString = $timestamp . $string;
        } else {
            $writeString = indent($string, strlen($timestamp));
        }
        fwrite($handle, $writeString . "\n");
    }
}

function indent(string $string, int $indent): string
{
    $lines = explode("\n", $string);

    $indentedLines = array_map(function (string $line) use ($indent) {
        return str_repeat(' ', $indent) . $line;
    }, $lines);

    return implode("\n", $indentedLines);
}

function line(string... $strings) : string
{
    return implode(' ', $strings);
}

function make_green(string $string) : string
{
    return start_green() . $string . reset_color();
}

function make_red(string $string) : string
{
    return start_red() . $string . reset_color();
}

function make_cyan(string $string) : string
{
    return start_cyan() . $string . reset_color();
}

function make_magenta(string $string) : string
{
    return start_magenta() . $string . reset_color();
}

function make_yellow(string $string) : string
{
    return start_yellow() . $string . reset_color();
}

function reset_color() : string
{
    return "\033[0m";
}

function start_green() : string
{
    return "\033[32m";
}

function start_red() : string
{
    return "\033[31m";
}

function start_cyan() : string
{
    return "\033[36m";
}

function start_magenta() : string
{
    return "\033[35m";
}

function start_yellow() : string
{
    return "\033[33m";
}
