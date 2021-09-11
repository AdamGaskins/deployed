<?php

namespace AdamGaskins\Deployed\Actions;

class GetChangelogContentsAction
{
    public function execute()
    {
        if (! file_exists(base_path('CHANGELOG.md'))) {
            throw new \Exception('CHANGELOG.md not found in root of project.');
        }

        return file_get_contents(base_path('CHANGELOG.md'));
    }
}
