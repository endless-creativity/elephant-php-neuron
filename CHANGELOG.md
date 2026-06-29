# Changelog

All notable changes to this project will be documented in this file. The
format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
once it reaches 1.0.

## [Unreleased]

## [0.1.2] — 2026-06-29

### Changed

- Composer constraint for `endless-creativity/elephant-php` widened to
  `^0.2 || ^0.3 || ^0.4`. The 0.4.0 release adds an `ignoreHiddenText`
  option that is **on by default**, so runs marked `w:vanish` are now
  omitted from both text and Markdown output. `DocxReader` keeps this
  default deliberately: hidden text should not leak into RAG ingestion.
  0.4.1 is a PHPStan-only fix with no behavioural change. The
  `Converter::extractRawText` / `convertToMarkdown` calls used by
  `DocxReader` are unchanged, so the reader works against any of the
  0.2–0.4 branches.

## [0.1.1] — 2026-04-28

### Changed

- Composer constraint for `endless-creativity/elephant-php` widened to
  `^0.2 || ^0.3`. The 0.3.0 release expands the upstream public API
  (`idPrefix`, `ignoreEmptyParagraphs`, `prettyPrint`,
  `transformDocument`, checkbox form fields, `numStyleLink` chasing)
  and tightens XML/image security. None of these changes affect the
  `Converter::extractRawText` and `convertToMarkdown` calls used by
  `DocxReader`, so the reader works unchanged against either branch.

## [0.1.0] — 2026-04-28

### Added
- `DocxReader` implementing Neuron AI's `ReaderInterface` for `.docx`
  files, backed by elephant-php.
- Optional `'format' => 'markdown'` for callers that want structural
  cues preserved during ingestion. Defaults to plain text.
