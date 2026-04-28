# elephant-php-neuron

A [Neuron AI](https://docs.neuron-ai.dev) `FileDataLoader` reader for `.docx`
documents, powered by [elephant-php](https://github.com/endless-creativity/elephant-php).

Drop it into a Neuron RAG pipeline and `.docx` files become embeddable
documents alongside the bundled PDF, HTML and plain-text readers.

## Installation

```bash
composer require endless-creativity/elephant-php-neuron
```

Requires PHP 8.2+. No external binaries needed (unlike `PdfReader`).

## Usage

```php
use NeuronAI\RAG\DataLoader\FileDataLoader;
use EndlessCreativity\ElephantPhpNeuron\DocxReader;

$documents = FileDataLoader::for(__DIR__.'/knowledge')
    ->addReader('docx', new DocxReader())
    ->getDocuments();

MyRAG::make()->addDocuments($documents);
```

Pass a directory and Neuron walks it, picking the right reader per
extension; pass a single file to ingest just that one.

### Output format

By default the reader returns plain text via
`Converter::extractRawText()` — paragraphs separated by `"\n\n"`, no
markup. This is usually what you want for embeddings: less syntactic
noise, more semantic signal per token.

If you'd rather preserve headings, lists and links — for example because
your splitter or post-processor relies on Markdown structure — request
Markdown explicitly through the reader options:

```php
FileDataLoader::for($path)
    ->addReader('docx', new DocxReader())
    ->getDocuments(['format' => DocxReader::FORMAT_MARKDOWN]);
```

The `$options` array is forwarded by `FileDataLoader` to every reader,
so the same flag is in effect for the whole loading pass.

## Limitations

- Only OOXML `.docx` is supported. Legacy binary `.doc` (Word 97–2003)
  is not handled by elephant-php and therefore not by this reader either.
- Images embedded in the document are dropped during text extraction.
  This is intentional for RAG — embeddings are text-only.
- Conversion warnings emitted by elephant-php (`Result::messages`) are
  currently silenced. If you need them, open an issue.

## License

BSD-2-Clause. See [LICENSE](LICENSE).
