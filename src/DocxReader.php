<?php

declare(strict_types=1);

namespace EndlessCreativity\ElephantPhpNeuron;

use EndlessCreativity\ElephantPhp\Converter;
use NeuronAI\RAG\DataLoader\ReaderInterface;

/**
 * Neuron AI file reader for .docx documents, backed by elephant-php.
 *
 * Register against the `docx` extension on a FileDataLoader:
 *
 *     FileDataLoader::for($path)
 *         ->addReader('docx', new DocxReader())
 *         ->getDocuments();
 *
 * Supported `$options`:
 *   - 'format' => 'text' (default) | 'markdown'
 *     'text' uses {@see Converter::extractRawText()} (paragraphs separated by
 *     "\n\n"); ideal for embeddings.
 *     'markdown' preserves headings, lists, links — useful when the chunker
 *     should keep structural cues at the cost of more tokens.
 */
final class DocxReader implements ReaderInterface
{
    public const FORMAT_TEXT = 'text';

    public const FORMAT_MARKDOWN = 'markdown';

    /**
     * @param  array{format?: string}  $options
     */
    public static function getText(string $filePath, array $options = []): string
    {
        $format = $options['format'] ?? self::FORMAT_TEXT;
        $converter = new Converter();

        $result = match ($format) {
            self::FORMAT_MARKDOWN => $converter->convertToMarkdown($filePath),
            self::FORMAT_TEXT => $converter->extractRawText($filePath),
            default => throw new \InvalidArgumentException(
                "Unknown format '{$format}'. Use 'text' or 'markdown'."
            ),
        };

        return $result->value;
    }
}
