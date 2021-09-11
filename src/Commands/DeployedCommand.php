<?php

namespace AdamGaskins\Deployed\Commands;

use AdamGaskins\Deployed\Actions\GenerateAndUploadDeployedBannerAction;
use AdamGaskins\Deployed\Actions\ParseReleasesFromChangelogAction;
use AdamGaskins\Deployed\Actions\SendDeployedNotificationAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeployedCommand extends Command
{
    public $signature = 'deployed {--notify}';

    public $description = 'Sends a slack notification for the latest deployment.';

    public function handle()
    {
        $releases = app()->make(ParseReleasesFromChangelogAction::class)->execute();

        if (! array_key_exists(config('app.version'), $releases)) {
            $this->info('No release notes found in CHANGELOG.md for v' . config('app.version'));
            return;
        }

        $data = [
            'appName' => config('app.name'),
            'appVersion' => config('app.version'),
            'appUrl' => config('app.url'),
            'appLogo' => config('deployed.logo'),
            'notes' => $releases[config('app.version')]
        ];

        $bannerUrl = app()->make(GenerateAndUploadDeployedBannerAction::class)
            ->execute($data);

        $bannerUrl = Storage::url($bannerUrl);

        $this->info('Deployment banner uploaded: ' . $bannerUrl);

        if (! $this->option('notify')) {
            $this->info('Rerun this command with --notify to send a slack notification.');
            return;
        }

        app()->make(SendDeployedNotificationAction::class)
            ->execute($bannerUrl, $data);
    }
}
