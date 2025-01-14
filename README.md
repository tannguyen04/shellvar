<p align="center">
  <a href="" rel="noopener">
  <img width=200px height=200px src="https://placehold.jp/000000/ffffff/200x200.png?text=Shellvar&css=%7B%22border-radius%22%3A%22%20100px%22%7D" alt="Shellvar logo"></a>
</p>

<h1 align="center">Shellvar</h1>

<div align="center">

[![GitHub Issues](https://img.shields.io/github/issues/AlexSkrypnyk/shellvar.svg)](https://github.com/AlexSkrypnyk/shellvar/issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/AlexSkrypnyk/shellvar.svg)](https://github.com/AlexSkrypnyk/shellvar/pulls)
[![Tests](https://github.com/AlexSkrypnyk/shellvar/actions/workflows/test-php.yml/badge.svg)](https://github.com/AlexSkrypnyk/shellvar/actions/workflows/test-php.yml)
[![codecov](https://codecov.io/gh/AlexSkrypnyk/shellvar/graph/badge.svg?token=7WEB1IXBYT)](https://codecov.io/gh/AlexSkrypnyk/shellvar)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/AlexSkrypnyk/shellvar)
![LICENSE](https://img.shields.io/github/license/AlexSkrypnyk/shellvar)
![Renovate](https://img.shields.io/badge/renovate-enabled-green?logo=renovatebot)

</div>

---

<p align="center">
    <br>
    Utility to work with shell variables. 
</p>

## Features

- Extract variables:
  - Scan a file or a directory containing shell scripts and extract found
    variables with comments and assigned values.
  - Filter variables: exclude local, exclude by prefix, exclude from a list in
    file.
  - Format output as a CSV, Markdown table or Markdown blocks defined in the
    template.
  - Extend filters and formatters with custom classes.

## Installation

    composer require alexskrypnyk/shellvar

## Usage

By default, variable names, descriptions (taken from the comments) and their
values are printed to STDOUT in the CSV format. You can also change the output
format to Markdown table or Markdown blocks.

Given the following shell script (see [extended example](tests/Fixtures/test-data.sh) used in tests):

```bash
# Assignment to scalar value.
VAR1=val1
# Assignment to another variable.
VAR2="${VAR1}"
# Parameter expansion.
VAR3=${val3:-abc}
# Parameter expansion with a default value using
# another variable $VAR3.
#
# Continuation of the multi-line comment.
VAR4=${val4:-$VAR3}
```

### Default CSV output

```bash
./vendor/bin/shellvar extract path/to/script.sh
```

```csv
Name;"Default value";Description
VAR1;val1;"Assignment to scalar value."
VAR2;VAR1;"Assignment to another variable."
VAR3;abc;"Parameter expansion."
VAR4;VAR3;"Parameter expansion with a default value using another variable $VAR3.

Continuation of the multi-line comment."
```

### Markdown table

```bash
./vendor/bin/shellvar extract --format=md-table path/to/script.sh
```

```markdown
| Name   | Default value | Description                                                                                                                      |
|--------|---------------|----------------------------------------------------------------------------------------------------------------------------------|
| `VAR1` | `val1`        | Assignment to scalar value.                                                                                                      |
| `VAR2` | `VAR1`        | Assignment to another variable.                                                                                                  |
| `VAR3` | `abc`         | Parameter expansion.                                                                                                             |
| `VAR4` | `VAR3`        | Parameter expansion with a default value using<br />another variable `$VAR3`.<br /><br />Continuation of the multi-line comment. |
```

which renders as

| Name   | Default value | Description                                                                                                                      |
|--------|---------------|----------------------------------------------------------------------------------------------------------------------------------|
| `VAR1` | `val1`        | Assignment to scalar value.                                                                                                      |
| `VAR2` | `VAR1`        | Assignment to another variable.                                                                                                  |
| `VAR3` | `abc`         | Parameter expansion.                                                                                                             |
| `VAR4` | `VAR3`        | Parameter expansion with a default value using<br />another variable `$VAR3`.<br /><br />Continuation of the multi-line comment. |


### Markdown blocks

```bash
./vendor/bin/shellvar extract --format=md-blocks path/to/script.sh
```

```markdown
### `VAR1`

Assignment to scalar value.

Default value: `val1`

### `VAR2`

Assignment to another variable.

Default value: `VAR1`

### `VAR3`

Parameter expansion.

Default value: `abc`

### `VAR4`

Parameter expansion with a default value using<br />another variable `$VAR3`.

Continuation of the multi-line comment.

Default value: `VAR3`

```

which renders as

<table><tr><td>

### `VAR1`

Assignment to scalar value.

Default value: `val1`

### `VAR2`

Assignment to another variable.

Default value: `VAR1`

### `VAR3`

Parameter expansion.

Default value: `abc`

### `VAR4`

Parameter expansion with a default value using<br />another variable `$VAR3`.

Continuation of the multi-line comment.

Default value: `VAR3`

</td></tr></table>

### Advanced: Markdown blocks with links and custom template

```bash
./vendor/bin/shellvar extract --format=md-blocks --md-link-vars --md-block-template-file=path/to/template.md path/to/script.sh
```

```markdown
### `VAR1`

Assignment to scalar value.

Default value: `val1`

Path: `1.sh`

Paths: `1.sh`

### `VAR2`

Assignment to another variable.

Default value: `VAR1`

Path: `1.sh`

Paths: `1.sh`

### `VAR3`

Parameter expansion.

Default value: `abc`

Path: `1.sh`

Paths: `1.sh`

### `VAR4`

Parameter expansion with a default value using<br />another variable [`$VAR3`](#VAR3).

Continuation of the multi-line comment.

Default value: `VAR3`

Path: `1.sh`

Paths: `1.sh`

```

which renders as

<table><tr><td>

### `VAR1`

Assignment to scalar value.

Default value: `val1`

Path: `1.sh`

Paths: `1.sh`

### `VAR2`

Assignment to another variable.

Default value: `VAR1`

Path: `1.sh`

Paths: `1.sh`

### `VAR3`

Parameter expansion.

Default value: `abc`

Path: `1.sh`

Paths: `1.sh`

### `VAR4`

Parameter expansion with a default value using<br />another variable [`$VAR3`](#VAR3).

Continuation of the multi-line comment.

Default value: `VAR3`

Path: `1.sh`

Paths: `1.sh`

</td></tr></table>

## Options

There are options for different phases: extraction, filtering and formatting.

"Multiple values allowed" means that you can specify the option multiple times
like so: `--exclude-prefix=VAR1 --exclude-prefix=VAR2` etc.

### Extraction

| Name                               | Description                                                                          | Default value |
|------------------------------------|--------------------------------------------------------------------------------------|---------------|
| `paths`                            | File or directory to scan. Multiple files separated by space.                        |               |
| `--skip-text=SKIP`                 | Skip variable extraction if the comment has this specified text.                     | `@skip`       |
| `--skip-description-prefix=PREFIX` | Skip description lines that start with the provided prefix. Multiple values allowed. |               |

### Filtering

| Name                       | Description                                                                                                   | Default value |
|----------------------------|---------------------------------------------------------------------------------------------------------------|---------------|
| `--exclude-local`          | Remove local variables.                                                                                       |               |
| `--exclude-prefix=PREFIX`  | Exclude variables that start with the provided prefix. Multiple values allowed                                |               |                                                                                                               |
| `--exclude-from-file=FILE` | A path to a file that contains variables to be excluded from the extraction process. Multiple values allowed. |               |

### Format

| Name                               | Description                                                                                                                                                                                               | Default value                                                    |
|------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------|
| `--format=FORMAT`                  | The output format: CSV (`csv`), Markdown table (`md-table`), Markdown blocks (`md-blocks`).                                                                                                               | `csv`                                                            |
| `--sort`                           | Sort variables in ascending order by name.                                                                                                                                                                |                                                                  |
| `--unset`                          | A string to represent a value for variables that are defined but have no set value.                                                                                                                       | `UNSET`                                                          |
| `--fields=FIELDS`                  | Semicolon-separated list of fields. Each field is a key-label pair in the format "key=label". If label is omitted, key is used as label.                                                                  | `name=Name;default_value="Default value;description=Description` |
| `--path-strip-prefix=PREFIX`       | Strip the provided prefix from the path.                                                                                                                                                                  |                                                                  |
| `--csv-separator=SEPARATOR`        | Separator for the CSV output format.                                                                                                                                                                      | `;`                                                              |
| `--md-link-vars`                   | Link variables within usages to their definitions in the Markdown output format.                                                                                                                          |                                                                  |
| `--md-link-vars-anchor-case`       | The case of the anchors when linking variables. Can be one of "preserve", "lower" or "upper".                                                                                                             | `preserve`                                                       |
| `--md-no-inline-code-wrap-vars`    | Do not wrap variables to show them as inline code in the Markdown output format.                                                                                                                          |                                                                  |
| `--md-no-inline-code-wrap-numbers` | Do not wrap numbers to show them as inline code in the Markdown output format.                                                                                                                            |                                                                  |
| `--md-inline-code-extra-file=FILE` | A path to a file that contains additional strings to be formatted as inline code in the Markdown output format. Multiple values allowed.                                                                  |                                                                  |
| `--md-block-template-file=FILE`    | A path to a Markdown template file used for Markdown blocks (md-blocks) output format. `{{ name }}`, `{{ description }}`, `{{ default_value }}` and `{{ path }}` tokens can be used within the template.  |                                                                  |

## Maintenance

    composer install
    composer lint
    composer lint:fix
    composer test
