<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;

class GenerateRootPassword extends Command
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-root-password
    {--minLength=8 : The min length of the password}
    {--maxLength=20 : The max length of the password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a random password and save it to the .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minLength = $this->option('minLength');
        $maxLength = $this->option('maxLength');
        $faker = Faker::create();
        $password = $faker->password($minLength, $maxLength);

        // Update or add the password in the .env file
        $this->setEnv('ROOT_USER_PASSWORD', $password);

        $this->info('Random password generated and added to .env file: ' . $password);
    }

    /**
     * Set or update an environment variable in the .env file.
     */
    protected function setEnv($key, $value)
    {
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            $content = File::get($envPath);

            if (strpos($content, "$key=") !== false) {
                // If the key exists, replace it
                $content = preg_replace("/^$key=.*/m", "$key=$value", $content);
            } else {
                // If the key doesn't exist, add it to the end
                $content .= "\n$key=$value\n";
            }

            File::put($envPath, $content);
        }
    }
}
