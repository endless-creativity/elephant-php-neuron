<?php

declare(strict_types=1);

use EndlessCreativity\ElephantPhpNeuron\DocxReader;

it('extracts plain text by default', function (): void {
    $text = DocxReader::getText(fixture('single-paragraph.docx'));

    expect($text)->not->toBeEmpty();
    expect(str_contains($text, '<'))->toBeFalse();
    expect(str_contains($text, '#'))->toBeFalse();
});

it('separates paragraphs with double newlines in text mode', function (): void {
    $text = DocxReader::getText(fixture('simple-list.docx'));

    expect($text)->toContain("\n\n");
});

it('returns markdown when requested', function (): void {
    $markdown = DocxReader::getText(
        fixture('simple-list.docx'),
        ['format' => DocxReader::FORMAT_MARKDOWN],
    );

    expect($markdown)->not->toBeEmpty();
});

it('rejects an unknown format', function (): void {
    DocxReader::getText(fixture('single-paragraph.docx'), ['format' => 'pdf']);
})->throws(InvalidArgumentException::class, "Unknown format 'pdf'");

it('handles an empty docx without errors', function (): void {
    $text = DocxReader::getText(fixture('empty.docx'));

    expect(trim($text))->toBe('');
});
