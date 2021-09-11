<?php

namespace AdamGaskins\Deployed\Actions;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use SlackPhp\BlockKit\Formatter;
use SlackPhp\BlockKit\Surfaces\Message;

class SendDeployedNotificationAction
{
    public function execute($bannerUrl, $data)
    {
        $fmt = Formatter::new();

        $msg = Message::new()
            ->setExtra('text', $fmt->sub('{appName} v{appVersion} has deployed.', $data))
            ->tap(fn (Message $msg) => $msg->newImage()
                ->url($bannerUrl)
                ->altText($fmt->sub('{appName} Release Notes', $data)));

        $actions = $msg->newActions();

        foreach (config('deployed.links') as $text => $url) {
            $actions->newButton()
                ->actionId(Str::slug($text))
                ->text($fmt->sub($text, $data))
                ->url($fmt->sub($url, $data));
        }

        app()->make(Client::class)
            ->post(config('deployed.slack.webhook'), [ 'json' => $msg->toArray() ]);
    }
}
