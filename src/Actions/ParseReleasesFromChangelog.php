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
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\CommonMark\Node\Inline\DelimitedInterface;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Xml\XmlRenderer;

class ParseReleasesFromChangelog
{
    public function execute()
    {
        if (! file_exists(base_path('CHANGELOG.md'))) {
            throw new \Exception('CHANGELOG.md not found in root of project.');
        }

        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());

        $parser = new MarkdownParser($env);

        $document = $parser->parse(file_get_contents(base_path('CHANGELOG.md')));

//        dd((new XmlRenderer($env))->renderDocument($document));

        $releases = [];

        $currentRelease = null;

        foreach ($document->iterator() as $node) {
            /** @var Node $node */
            if ($node instanceof Heading) {
                $currentRelease = preg_replace('/[^0-9]*(.*)/', '$1', $this->nodeToString($node, true, false));
            }

            if ($currentRelease === null) {
                continue;
            }

            if ($node instanceof ListBlock) {
                $releases[$currentRelease] = $this->parseList($node);
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

            /** @var $item ListItem */
            $list[] = $this->parseListItem($item);
        }

        return $list;
    }

    public function parseListItem(ListItem $node)
    {
        $parsed = [ 'type' => null, 'content' => '' ];

        /** @var Strong $typeNode */
        $typeNode = (new Query())
            ->where(Query::type(Strong::class))
            ->findOne($node);

        if ($typeNode) {
            $parsed['type'] = Str::lower(trim($this->nodeToString($typeNode, true), ' :'));

            $typeNode->detach();
        }

        $parsed['content'] = $this->nodeToString($node);

        return $parsed;
    }

    public function nodeToString(?Node $node, $strict = false, $skipLinks = true)
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

            if ($strict) {
                continue;
            }

            if ($node instanceof DelimitedInterface) {
                $str .= $event->isEntering() ? $node->getOpeningDelimiter() : $node->getClosingDelimiter();
            }
        }

        return $str;
    }
}
