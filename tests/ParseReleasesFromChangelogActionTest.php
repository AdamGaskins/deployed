<?php

namespace AdamGaskins\Deployed\Tests;

use AdamGaskins\Deployed\Actions\GetChangelogContentsAction;
use AdamGaskins\Deployed\Actions\ParseReleasesFromChangelogAction;
use Mockery\MockInterface;

class ParseReleasesFromChangelogActionTest extends TestCase
{
    /** @test */
    public function it_extracts_headers()
    {
        $parsed = $this->parseMarkdown(<<<MD
            # Version 4.0.0
            # v4.0.0-beta
            # Release3.0.0
            # Update 2.0.0
            # awefjoiajwef1.0.0
            MD);

        foreach (['4.0.0', '4.0.0-beta', '3.0.0', '2.0.0', '1.0.0'] as $version) {
            $this->assertArrayHasKey($version, $parsed);
        }
    }

    /** @test */
    public function it_extracts_lists()
    {
        $parsed = $this->parseMarkdown(<<<MD
            # 2.0.0
            - Change 1
            - Change 2
            # 1.0.0
            - Change 3
            - Change 4
            MD);

        $this->assertSame([
            '2.0.0' => [
                [ 'type' => ParseReleasesFromChangelogAction::DEFAULT_TYPE, 'content' => 'Change 1' ],
                [ 'type' => ParseReleasesFromChangelogAction::DEFAULT_TYPE, 'content' => 'Change 2' ],
            ],
            '1.0.0' => [
                [ 'type' => ParseReleasesFromChangelogAction::DEFAULT_TYPE, 'content' => 'Change 3' ],
                [ 'type' => ParseReleasesFromChangelogAction::DEFAULT_TYPE, 'content' => 'Change 4' ],
            ]
        ], $parsed);
    }

    /** @test */
    public function it_extracts_lists_with_types()
    {
        $parsed = $this->parseMarkdown(<<<MD
            # 2.0.0
            - **FEATURE** Change 1
            - **BUG:** Change 2
            # 1.0.0
            - **DoCs** Change 3
            - **AJIW_a4j2** Change 4
            MD);

        $this->assertSame([
            '2.0.0' => [
                [ 'type' => 'feature', 'content' => 'Change 1' ],
                [ 'type' => 'bug', 'content' => 'Change 2' ],
            ],
            '1.0.0' => [
                [ 'type' => 'docs', 'content' => 'Change 3' ],
                [ 'type' => 'ajiw_a4j2', 'content' => 'Change 4' ],
            ]
        ], $parsed);
    }

    protected function parseMarkdown($str)
    {
        $this->mock(
            GetChangelogContentsAction::class,
            fn (MockInterface $mock) =>
            $mock->allows('execute')->andReturn($str)
        );

        return app()->make(ParseReleasesFromChangelogAction::class)
            ->execute();
    }
}
