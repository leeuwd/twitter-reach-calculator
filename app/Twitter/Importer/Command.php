<?php
declare(strict_types=1);

namespace App\Twitter\Importer;

use App\Console\Commands\ComputeReach;
use App\Twitter\Models\ReachResult;
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
     * @var double
     */
    public $startTime;

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
     * @return \Illuminate\Console\OutputStyle|\Symfony\Component\Console\Output\OutputInterface
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
        // Abort
        if (! $this->preCompute()) {
            return;
        }

        try {
            $result = $this->computeReach();

            // Print results; when non-default results
            // are found false is returned an we want
            // to stop processing
            if (! $this->printResult($result)) {
                return;
            }

            $this->postCompute();
        } catch (\Exception $e) {
            report($e);

            self::error($e->getMessage());
        }
    }

    /**
     * Pre-compute job; some tasks before
     * we do the actual computation.
     *
     * @return bool
     */
    protected function preCompute(): bool
    {
        // Print reports to construct
        self::title('Tweet reach computation.');

        // Confirmation question
        $confirmed = self::confirm('Do you want to compute the reach of the Tweet you provided?', true);

        // Now start counting
        $this->startTime = microtime(true);

        // Inform the importer we're running from CLI
        Importer::useProgressBar(true);
        Importer::setCommandHandler($this);

        // Return bool
        return $confirmed;
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
     * Compute reach, being returned as part of
     * ReachResult object.
     *
     * @return \App\Twitter\Models\ReachResult
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function computeReach(): ReachResult
    {
        return Importer::computeReach(static::$command->url);
    }

    /**
     * Compute reach, being returned as part of
     * ReachResult object.
     *
     * @param ReachResult $result
     * @return bool
     */
    protected function printResult(ReachResult $result): bool
    {
        // Tweet ID not valid
        if (! $result->tweetIdIsValid()) {
            self::error('The Tweet ID could no be extracted from the URL.');

            return false;
        }

        // Never retweeted
        if (! $result->hasRetweets()) {
            self::comment('The Tweet URL you provided was never retweeted. Bye.');

            return false;
        }

        // Print final result
        self::info($result->getReachDescription());

        // All good
        return true;
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

    /**
     * Post-compute job; print out process info.
     *
     * @param string $durationMessageFormat
     * @return void
     */
    protected function postCompute(string $durationMessageFormat = 'Computing this took a total of %.1f seconds.'): void
    {
        self::comment(\sprintf($durationMessageFormat, microtime(true) - $this->startTime));
    }
}
