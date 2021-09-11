<?php

namespace AdamGaskins\Deployed\Actions;

use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Inline\DelimitedInterface;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Parser\MarkdownParser;

class ParseReleasesFromChangelogAction
{
    public const DEFAULT_TYPE = null;

    public function execute()
    {
        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());

        $parser = new MarkdownParser($env);

        $document = $parser->parse(
            app()->make(GetChangelogContentsAction::class)->execute()
        );

        $releases = [];

        $currentRelease = null;

        foreach ($document->iterator() as $node) {
            if ($node instanceof Heading) {
                $currentRelease = preg_replace('/[^0-9]*(.*)/', '$1', $this->nodeToString($node, true, false));
                $releases[$currentRelease] = [];
            }

            if ($currentRelease === null) {
                continue;
            }

            if ($node instanceof ListBlock) {
                $releases[$currentRelease] = array_merge($releases[$currentRelease], $this->parseList($node));
            }
        }

        return $releases;
    }

    public function parseList(ListBlock $node)
    {
        $list = [];

        foreach ($node->iterator() as $item) {
            if (! $item instanceof ListItem) {
                continue;
            }

            $list[] = $this->parseListItem($item);
        }

        return $list;
    }

    public function parseListItem(ListItem $node)
    {
        $parsed = [ 'type' => self::DEFAULT_TYPE, 'content' => '' ];


        $paragraph = $node->firstChild();
        $firstNode = optional($paragraph)->firstChild();

        if ($firstNode && $firstNode instanceof Strong) {
            $parsed['type'] = Str::lower(trim($this->nodeToString($firstNode, true), ' :'));

            $firstNode->detach();
        }

        $md = new GithubFlavoredMarkdownConverter();

        $parsed['content'] = $md->convertToHtml($this->nodeToString($node))->getContent();

        $parsed['content'] = trim(Str::replace([ '<p>', '</p>' ], '', $parsed['content']));

        return $parsed;
    }

    public function nodeToString(?Node $node, $noFormatting = false, $skipLinks = true)
    {
        if ($node === null) {
            return '';
        }

        $str = '';

        $walker = $node->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node instanceof Text) {
                $str .= $node->getLiteral();
            }

            if ($node instanceof Code) {
                $str .= '`' . $node->getLiteral() . '`';
            }

            if ($skipLinks) {
                if ($node instanceof Link) {
                    do {
                        $event2 = $walker->next();
                    } while ($event2->getNode() !== $node);
                }
            }

            if ($noFormatting) {
                continue;
            }

            if ($node instanceof DelimitedInterface) {
                $str .= $event->isEntering() ? $node->getOpeningDelimiter() : $node->getClosingDelimiter();
            }
        }

        return $str;
    }
}
