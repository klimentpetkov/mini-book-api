<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MiniInstall extends Command
{
    /**
     * The name and signature of the console command.
     * Options:
     *  --fresh      Drop + migrate + seed
     *  --no-swagger Skip Swagger doc generation
     *
     * @var string
     */
    protected $signature = 'mini:install {--fresh : Drop + migrate:fresh before seeding} {--no-swagger : Skip Swagger generation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate DB, seed admin, setup Passport, generate Swagger.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('🔧 MiniBooks installation started…');

        $this->waitForDatabase();

        // Migrations
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
            $this->info('🗄️  migrate:fresh DONE.');
        } else {
            $this->callSilent('migrate', ['--force' => true]);
            $this->info('🗄️  migrate ready.');
        }

        // Seed always since it is a idempotent operation
        $this->call('db:seed', ['--force' => true]);
        $this->info('🌱 Seeders DONE (Admin + Roles/Permissions).');

        // Passport keys
        $priv = storage_path('oauth-private.key');
        $pub  = storage_path('oauth-public.key');

        if (!file_exists($priv) || !file_exists($pub)) {
            $this->call('passport:keys', ['--force' => true]);

            @chmod($priv, 0640);
            @chmod($pub, 0640);

            $this->info('🔑 Passport keys generated.');
        } else {
            $this->info('🔑 Passport keys already exist.');
        }

        // Personal Access Client for provider "users"
        /** @var object|null $client */
        $client = DB::table('oauth_clients')
            ->where('name', 'mini-books-personal')
            ->first();

        if (! $client) {
            $this->info('👤 Creating Personal Access Client…');

            Artisan::call('passport:client', [
                '--personal'       => true,
                '--name'           => 'mini-books-personal',
                '--provider'       => 'users',
                '--no-interaction' => true,
            ]);
        } else {
            $this->info('👤 Personal Access Client already exists.');
        }

        // Storage link
        if (!file_exists(public_path('storage'))) {
            $this->callSilent('storage:link');
            $this->info('🔗 public/storage link created.');
        }

        // Swagger (optional)
        if (!$this->option('no-swagger')) {
            try {
                $this->call('l5-swagger:generate');
                $this->info('📘 Swagger docs are generated.');
            } catch (\Throwable $e) {
                $this->warn('⚠️  Swagger generation failed: '.$e->getMessage());
            }
        }

        // Cache clear
        $this->callSilent('optimize:clear');
        $this->info('🧹 optimize:clear DONE.');

        $this->line('✅ MiniBooks is ready! Login with credentials admin@example.com / password');

        return self::SUCCESS;
    }

    /**
     * Waiting for DB appearance until tries are exhausted
     * @return void
     */
    protected function waitForDatabase(): void
    {
        $tries = 30;

        while ($tries-- > 0) {
            try {
                DB::connection()->getPdo();

                $this->info('✅ DB is present.');
                return;
            } catch (\Throwable $e) {
                $this->line('⏳ Waiting for DB…');
                sleep(2);
            }
        }

        $this->error('❌ DB is not reachable after enough tries.');
        exit(1);
    }
}
