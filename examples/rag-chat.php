<?php

declare(strict_types=1);

/**
 * Manual end-to-end test: load .docx files via DocxReader and answer a
 * question with a Neuron AI RAG agent.
 *
 * Required env vars:
 *   OPENAI_API_KEY  — used for both chat and embeddings
 *
 * Run from the package root after `composer install`:
 *
 *     OPENAI_API_KEY=... php examples/rag-chat.php /path/to/docs "Your question here"
 *
 * With no args it falls back to tests/fixtures and a default question.
 */

require __DIR__.'/../vendor/autoload.php';

use EndlessCreativity\ElephantPhpNeuron\DocxReader;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\RAG\DataLoader\FileDataLoader;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\MemoryVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

$path = $argv[1] ?? __DIR__.'/../tests/fixtures';
$question = $argv[2] ?? 'Summarise the documents in one paragraph.';

$openaiKey = getenv('OPENAI_API_KEY') ?: '';

if ($openaiKey === '') {
    fwrite(STDERR, "OPENAI_API_KEY must be set.\n");
    exit(1);
}

final class DocxRagAgent extends RAG
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: getenv('OPENAI_API_KEY') ?: '',
            model: 'gpt-4o-mini',
        );
    }

    protected function embeddings(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(
            key: getenv('OPENAI_API_KEY') ?: '',
            model: 'text-embedding-3-small',
            dimensions: 1536,
        );
    }

    protected function vectorStore(): VectorStoreInterface
    {
        return new MemoryVectorStore(topK: 4);
    }
}

$documents = FileDataLoader::for($path)
    ->addReader('docx', new DocxReader())
    ->getDocuments();

printf("Loaded %d document chunk(s) from %s\n", count($documents), $path);

if ($documents === []) {
    fwrite(STDERR, "No documents found — nothing to embed.\n");
    exit(1);
}

$agent = DocxRagAgent::make();
$agent->addDocuments($documents);

$answer = $agent->chat(new UserMessage($question))->getMessage();

echo "\nQ: ".$question."\n";
echo 'A: '.($answer->getContent() ?? '')."\n";
