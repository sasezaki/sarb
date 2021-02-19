# Static Analysis Results Baseliner (SARB)

[![Code quality](https://github.com/DaveLiddament/sarb/workflows/Full%20checks/badge.svg)](https://github.com/DaveLiddament/sarb) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DaveLiddament/sarb/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DaveLiddament/sarb/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/dave-liddament/sarb/v/stable)](https://packagist.org/packages/dave-liddament/sarb)
[![Total Downloads](https://poser.pugx.org/dave-liddament/sarb/downloads)](https://packagist.org/packages/dave-liddament/sarb)
[![Type coverage](https://shepherd.dev/github/DaveLiddament/sarb/coverage.svg)](https://shepherd.dev/github/DaveLiddament/sarb/coverage.svg)
[![Code Coverage](https://codecov.io/gh/DaveLiddament/sarb/branch/master/graph/badge.svg)](https://codecov.io/gh/DaveLiddament/sarb)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/DaveLiddament/sarb/master)](https://infection.github.io)
[![License](https://poser.pugx.org/dave-liddament/sarb/license)](https://packagist.org/packages/dave-liddament/sarb)

**This is still in beta.**

 * [Why SARB](#why-sarb)
 * [Requirements](#requirements)
 * [Installing](#installing)
 * [Using SARB](#using-sarb)
 * [Examples](#examples)
 * [Further reading](#further-reading)

## Why SARB?

If you've tried to introduce advanced static analysis tools (e.g.
[Psalm](https://getpsalm.org), [PHPStan](https://github.com/phpstan/phpstan))
to legacy projects the tools have probably reported thousands of problems.
It's unrealistic to fix all but the most critical ones before continuing development.

SARB is used to create a baseline of these results. As work on the project
progresses SARB can takes the latest static analysis results, removes
those issues in the baseline and report the issues raised since the baseline.
SARB does this, in conjunction with git, by tracking lines of code between commits.
Currently SARB only supports git but it is possible to [add support for other SCMs](docs/NewHistoryAnalyser.md).

SARB is written in PHP, however it can be used to baseline results for any language and any static analysis tool.


#### Why not SARB?

SARB should not be used on greenfield projects. If you're lucky enough to work on a greenfield project make sure you fix all problems raised by static analysis as you go along.

## Requirements

Currently SARB only supports projects that use [git](https://git-scm.com/).

SARB requires PHP >= 7.1 to run. The project being analysed does not need to run PHP 7.1 or even be a PHP project at all.

## Installing

You can either add directly to the project you wish to run analysis on:

```
composer require --dev dave-liddament/sarb
```

Or you can install SARB globally (e.g. if you want to use it on a non PHP project):

```
composer global require dave-liddament/sarb
```

If you install globally make sure the composer bin directory is in your path.


## Using SARB

If you're using version 0.x see the [old documentation](docs/version0/README.md) 


#### 1. Make sure the current git commit is the one to be used in the baseline

When creating the baseline, SARB needs to know the git commit SHA of the baseline.
Make sure your code is in the state you want it to be in for the baseline and that the current commit represents that state.


#### 2. Create the baseline

Run the static analyser of choice and pipe the results into SARB:

E.g. using [Psalm's](https://psalm.dev) JSON output:

```shell
vendor/bin/psalm --output-format=json | vendor/bin/sarb create-baseline --format="psalm-json" psalm.baseline
```

This creates a baseline file called `psalm.baseline`. You'll want to check this in to your repository.



#### 3. Update code and then use SARB to remove baseline results

Continue coding. Then rerun static analyser and pipe results into SARB:

```shell
vendor/bin/psalm --output-format=json | vendor/bin/sarb remove-baseline psalm.baseline
```

### Running SARB from a global installation

If you are running SARB from a global installation you will need to specify the root of the project (where the `.git` directory lives).
The above would become:

```shell
psalm --output-format=json | sarb create-baseline --project-root=/path/to/project/root --format="psalm-json" psalm.baseline
```

### Supported tools

To see a list of supported tools and formats use:
```
vendor/bin/sarb list-static-analysis-tools
```

How to create and remove baseline for each supported tool:

#### [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
```shell
vendor/bin/phpcs src --report=json | vendor/bin/sarb create --input-format="phpcodesniffer-json" phpcs.baseline
vendor/bin/phpcs src --report=json | vendor/bin/sarb remove phpcs.baseline
```

#### [Phan](https://github.com/phan/phan)
```shell
vendor/bin/phan -m json | vendor/bin/sarb create --input-format="phan-json" phan.baseline
vendor/bin/phan -m json | vendor/bin/sarb remove phan.baseline
```

### [Exakat](https://www.exakat.io/)
```shell
php exakat.phar report -p <project> -format sarb | vendor/bin/sarb create --input-format="exakat-sarb" exakat.baseline
php exakat.phar report -p <project> -format sarb | vendor/bin/sarb remove phan.baseline
```

### [PHPMD](https://github.com/phpmd/phpmd)
```shell
vendor/bin/phpmd src json <ruleset> | vendor/bin/sarb create --input-format="phpmd-json" phpmd.baseline
vendor/bin/phpmd src json <ruleset> | vendor/bin/sarb remove phpmd.baseline
```

#### [Psalm](https://psalm.dev)
```shell
vendor/bin/psalm --output-format=json | vendor/bin/sarb create --input-format="psalm-json" psalm.baseline
vendor/bin/psalm --output-format=json | vendor/bin/sarb remove psalm.baseline
```

#### [PHPStan](https://phpstan.org)
```shell
vendor/bin/phpstan --format=json | vendor/bin/sarb create --input-format="phpstan-json" phpstan.baseline
vendor/bin/phpstan --format=json | vendor/bin/sarb remove phpstan.baseline
```

## My tool isn't supported...

That's no problem there are 3 methods to [integrate a static analysis tool](docs/CustomInputFormats.md) with SARB.


## Output formats 

The format for showing issues after the baseline is removed can be specified using `--output-format` option. 
Possible values are: `table`, `text` or `json`.


## Further Reading
 
 * [How SARB works](docs/HowSarbWorks.md)
 * [Adding support for new static analysis tools / format](docs/NewResultsParser.md)
 * [Adding support for SCMs other than git](docs/NewHistoryAnalyser.md)
 * [How to contribute](docs/Contributing.md)
 * [Unified Diff Terminology](docs/UnifiedDiffTerminology.md)
 * [SARB format](docs/SarbFormat.md)


## Authors

 * [Dave Liddament](https://www.daveliddament.co.uk) [@daveliddament](https://twitter.com/daveliddament)
 * [Community contributors](https://github.com/daveliddament/sarb/graphs/contributors)
