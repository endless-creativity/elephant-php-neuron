<?php

declare(strict_types=1);

/**
 * End-to-end example: load a directory of .docx files into a Neuron AI
 * RAG agent, using elephant-php under the hood.
 *
 * Run from the package root after `composer install`:
 *
 *     php examples/load-knowledge.php /path/to/docs
 */

require __DIR__.'/../vendor/autoload.php';

use EndlessCreativity\ElephantPhpNeuron\DocxReader;
use NeuronAI\RAG\DataLoader\FileDataLoader;

$path = $argv[1] ?? __DIR__.'/../tests/fixtures';

$documents = FileDataLoader::for($path)
    ->addReader('docx', new DocxReader())
    ->getDocuments();

printf("Loaded %d document chunks from %s\n", count($documents), $path);

foreach ($documents as $i => $document) {
    printf(
        "  [%d] sourceType=%s sourceName=%s length=%d\n",
        $i,
        $document->sourceType ?? 'n/a',
        $document->sourceName ?? 'n/a',
        mb_strlen($document->content ?? ''),
    );
}
