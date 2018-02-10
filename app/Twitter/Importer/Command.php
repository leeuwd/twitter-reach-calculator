<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Console\Commands\ComputeReach;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Helper\ProgressBar;

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
     * Create CLI progress bar.
     *
     * @param int $max
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public static function getProgressBar(int $max = 0): ProgressBar
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
        return static::$command->getOutput();
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

        // Confirm
        if (! self::confirm('Do you want to compute the reach of the Tweet you provided?', true)) {
            return;
        }

        static::$command->line('');
        $startTime = microtime(true);

        // Compute reach
        Importer::useProgressBar(true);
        Importer::setCommandHandler($this);

        try {
            $result = Importer::computeReach(static::$command->url);

            // Tweet URL not valid (probably Tweet ID not in)
            if (! $result->tweetUrlValid) {
                self::error('The Tweet URL you provided is not valid.');

                return;
            }

            // Tweet ID not valid
            if (! $result->tweetIdValid) {
                self::error('The Tweet ID could no be extracted.');

                return;
            }

            // Never retweeted
            if (! $result->hasRetweets) {
                self::comment('The Tweet URL you provided was never retweeted. Bye.');

                return;
            }
        } catch (\Exception $e) {
            report($e);

            self::error($e->getMessage());

            return;
        }

        // Print results
        self::info(\sprintf('The Tweet\'s had a reach of %s (%d).', $result->getHumanizedReachMetric(), $result->reach));
        self::comment(\sprintf('Computing this took a total of %.1f seconds.', microtime(true) - $startTime));
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
     * Write info line in terminal.
     *
     * @param string $string
     */
    protected static function info(string $string): void
    {
        static::$command->info($string);
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
     * Write comment line in terminal.
     *
     * @param string $string
     */
    protected static function comment(string $string): void
    {
        static::$command->comment($string);
    }
}
