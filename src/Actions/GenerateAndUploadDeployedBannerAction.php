<?php

namespace AdamGaskins\Deployed\Actions;

use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class GenerateAndUploadDeployedBannerAction
{
    public function execute($data)
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();

        app()->make(GenerateDeployedBannerAction::class)
            ->execute($temporaryDirectory->path('screenshot.png'), $data);

        $image = Storage::putFile('deploys', $temporaryDirectory->path('screenshot.png'), 'public');

        $temporaryDirectory->delete();

        return $image;
    }
}
