<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Twitter\Importer\Command as CommandHandler;
use Illuminate\Console\Command;

class ComputeReach extends Command
{
    /**
     * Required argument.
     */
    private const URL_ARGUMENT = 'url';

    /**
     * Tweet URL argument value;
     *
     * @var string|null
     */
    public $url;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tweet:reach {' . self::URL_ARGUMENT . ' : Single Tweet URL}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute the reach of a Tweet.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Set argument
        $this->url = $this->argument(self::URL_ARGUMENT);

        // Handler takes care of computation
        (new CommandHandler($this))->compute();
    }
}
