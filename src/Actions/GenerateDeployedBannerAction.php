<?php

namespace AdamGaskins\Deployed\Actions;

use Spatie\Browsershot\Browsershot;

class GenerateDeployedBannerAction
{
    public function execute($path, $data)
    {
        Browsershot::html(view('deployed::deployed')->with($data))
            ->deviceScaleFactor(3)
            ->select('#wrapper')
            ->waitUntilNetworkIdle()
            ->save($path);
    }
}
