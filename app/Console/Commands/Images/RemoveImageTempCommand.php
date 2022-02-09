<?php

namespace App\Console\Commands\Images;

use App\Cores\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveImageTempCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image_s3:remove_temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command to remove dir temp aws';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(Image::class)->removeTemp();

        Log::info('Remove S3 temporary directory.');

        return true;
    }
}
