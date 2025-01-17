<?php

/**
 * Static Analysis Results Baseliner (sarb).
 *
 * (c) Dave Liddament
 *
 * For the full copyright and licence information please view the LICENSE file distributed with this source code.
 */

declare(strict_types=1);

namespace DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common;

class Location
{
    /**
     * @var RelativeFileName
     */
    private $relativeFileName;

    /**
     * @var LineNumber
     */
    private $lineNumber;
    /**
     * @var AbsoluteFileName
     */
    private $absoluteFileName;

    /**
     * @throws InvalidPathException
     */
    public static function fromAbsoluteFileName(
        AbsoluteFileName $absoluteFileName,
        ProjectRoot $projectRoot,
        LineNumber $lineNumber
    ): self {
        $relativeFileName = $projectRoot->getPathRelativeToRootDirectory($absoluteFileName);

        return new self($absoluteFileName, $relativeFileName, $lineNumber);
    }

    public static function fromRelativeFileName(
        RelativeFileName $relativeFileName,
        ProjectRoot $projectRoot,
        LineNumber $lineNumber
    ): self {
        $absoluteFileName = $projectRoot->getAbsoluteFileName($relativeFileName);

        return new self($absoluteFileName, $relativeFileName, $lineNumber);
    }

    private function __construct(
        AbsoluteFileName $absoluteFileName,
        RelativeFileName $relativeFileName,
        LineNumber $lineNumber
    ) {
        $this->relativeFileName = $relativeFileName;
        $this->lineNumber = $lineNumber;
        $this->absoluteFileName = $absoluteFileName;
    }

    public function getRelativeFileName(): RelativeFileName
    {
        return $this->relativeFileName;
    }

    public function getLineNumber(): LineNumber
    {
        return $this->lineNumber;
    }

    public function getAbsoluteFileName(): AbsoluteFileName
    {
        return $this->absoluteFileName;
    }

    /**
     * Used for ordering Locations, first by FileName then by line number.
     */
    public function compareTo(self $other): int
    {
        if ($this->relativeFileName->getFileName() !== $other->relativeFileName->getFileName()) {
            return $this->relativeFileName->getFileName() <=> $other->relativeFileName->getFileName();
        }

        return $this->lineNumber->getLineNumber() <=> $other->lineNumber->getLineNumber();
    }
}
