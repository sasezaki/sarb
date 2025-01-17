<?php

declare(strict_types=1);

namespace DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Unit\Plugins\ResultsParsers\SarbJsonResultsParser;

use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\ProjectRoot;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\File\InvalidContentTypeException;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\ResultsParser\AnalysisResults;
use DaveLiddament\StaticAnalysisResultsBaseliner\Plugins\ResultsParsers\SarbJsonResultsParser\SarbJsonResultsParser;
use DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Helpers\AssertFileContentsSameTrait;
use DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Helpers\AssertResultMatch;
use DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Helpers\ResourceLoaderTrait;
use DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Unit\Plugins\ResultsParsers\ExpectParseExceptionWithResultTrait;
use PHPUnit\Framework\TestCase;

class SarbJsonResultsParserTest extends TestCase
{
    use AssertFileContentsSameTrait;
    use AssertResultMatch;
    use ExpectParseExceptionWithResultTrait;
    use ResourceLoaderTrait;

    /**
     * @var AnalysisResults
     */
    private $analysisResults;

    /**
     * @var SarbJsonResultsParser
     */
    private $sarbJsonResultsParser;
    /**
     * @var ProjectRoot
     */
    private $projectRoot;

    protected function setUp(): void
    {
        $this->projectRoot = ProjectRoot::fromProjectRoot('/vagrant/static-analysis-baseliner', '/home');

        $this->sarbJsonResultsParser = new SarbJsonResultsParser();
    }

    public function testConversion(): void
    {
        $fileContents = $this->getResource('sarb/sarb.json');
        $this->analysisResults = $this->sarbJsonResultsParser->convertFromString($fileContents, $this->projectRoot);
        $this->assertCount(3, $this->analysisResults->getAnalysisResults());

        $result1 = $this->analysisResults->getAnalysisResults()[0];
        $result2 = $this->analysisResults->getAnalysisResults()[1];
        $result3 = $this->analysisResults->getAnalysisResults()[2];

        $this->assertMatch($result1,
            'src/Domain/ResultsParser/AnalysisResults.php',
            67,
            'MismatchingDocblockParamType'
        );
        $this->assertSame(
            "Parameter \$array has wrong type 'array<mixed, mixed>', should be 'int'",
            $result1->getMessage()
        );

        $this->assertMatch($result2,
            'src/Domain/Utils/JsonUtils.php',
            29,
            'MixedAssignment'
        );

        $this->assertMatch($result3,
            'src/Plugins/PsalmJsonResultsParser/PsalmJsonResultsParser.php',
            90,
            'MixedAssignment'
        );
    }

    public function testTypeGuesser(): void
    {
        $this->assertFalse($this->sarbJsonResultsParser->showTypeGuessingWarning());
    }

    /**
     * @psalm-return array<int,array{string, int}>
     */
    public function invalidFileProvider(): array
    {
        return [
            ['sarb/sarb-invalid-missing-description.json', 1],
            ['sarb/sarb-invalid-missing-file.json', 2],
            ['sarb/sarb-invalid-missing-line.json', 2],
            ['sarb/sarb-invalid-missing-type.json', 3],
        ];
    }

    /**
     * @dataProvider invalidFileProvider
     */
    public function testInvalidFileFormat(string $fileName, int $resultWithIssue): void
    {
        $fileContents = $this->getResource($fileName);
        $this->expectParseAtLocationExceptionForResult($resultWithIssue);
        $this->sarbJsonResultsParser->convertFromString($fileContents, $this->projectRoot);
    }

    public function testInvalidJsonInput(): void
    {
        $fileContents = $this->getResource('invalid-json.json');
        $this->expectException(InvalidContentTypeException::class);
        $this->sarbJsonResultsParser->convertFromString($fileContents, $this->projectRoot);
    }
}
