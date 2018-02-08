<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Console\Commands\ComputeReach;
use \Illuminate\Console\OutputStyle;
use \Symfony\Component\Console\Helper\ProgressBar;

class Command
{
    /**
     * @var \App\Console\Commands\ComputeReach
     */
    public static $command;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    public static $bar;

    /**
     * Create a new instance.
     *
     * @param \App\Console\Commands\ComputeReach
     * @return void
     */
    public function __construct(ComputeReach $command)
    {
        static::$command = $command;
    }

    /**
     * Compute Tweet reach. Stepwise
     * CLI process.
     *
     * @return void
     */
    public function compute(): void
    {
        // CLI title
        self::title('Tweet reach computation.');

        // Quit early if provided URL is invalid
        if (! self::isValidUrl(static::$command->url)) {
            self::error('The Tweet URL you provided is not valid.');

            return;
        }

        // Confirm
        if (! self::confirm('Do you want to compute the reach of the Tweet you provided?', true)) {
            return;
        }

        static::$command->line('');
        $startTime = microtime(true);

        // todo: compute
        // todo: print result(s)

        // Final CLI output
        self::comment(PHP_EOL . \sprintf('Import took %.3f seconds.', microtime(true) - $startTime));
    }

    /**
     * Validate URL argument.
     *
     * @param string|null $url
     * @return bool
     */
    protected static function isValidUrl(?string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Write info line in terminal.
     *
     * @param string $string
     */
    protected static function info(string $string): void
    {
        static::$command->info($string);
    }

    /**
     * Write comment line in terminal.
     *
     * @param string $string
     */
    protected static function comment(string $string): void
    {
        static::$command->comment($string);
    }

    /**
     * Write error line in terminal.
     *
     * @param string $string
     * @param null|int|string $verbosity
     */
    protected static function error(string $string, $verbosity = null): void
    {
        static::$command->error($string, $verbosity);
    }

    /**
     * Ask question in terminal.
     *
     * @param string $question
     * @param bool $default
     * @return bool
     */
    protected static function confirm(string $question, bool $default = false): bool
    {
        return static::$command->confirm($question, $default);
    }

    /**
     * Write a string in an title box.
     *
     * @param  string $string
     * @return void
     */
    protected static function title(string $string): void
    {
        $line = str_repeat('*', \strlen($string) + 20);

        self::info(PHP_EOL . $line);
        self::info('*         ' . strtoupper($string) . '         *');
        self::info($line . PHP_EOL);
    }

    /**
     * Create CLI progress bar.
     *
     * @param int $max
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    protected static function getProgressBar(int $max = 0): ProgressBar
    {
        if (static::$bar === null) {
            static::$bar = self::getOutput()->createProgressBar($max);
            static::$bar::setFormatDefinition('importer', "<info>%message%</info>\n%current%/%max% \t[%bar%]\n");
            static::$bar->setFormat('importer');
        } else {
            // Sequential calls should restart
            static::$bar->start($max);
        }

        return static::$bar;
    }

    /**
     * Get CLI output style.
     *
     * @return \Illuminate\Console\OutputStyle
     */
    protected static function getOutput(): OutputStyle
    {
        static::$command->getOutput();
    }
}
