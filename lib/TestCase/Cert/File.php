<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use SimpleSAML\Module\monitor\TestData;
use Webmozart\Assert\Assert;

final class File extends Data
{
    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certFile = $testData->getInputItem('certFile');
        Assert::string($certFile);

        $certData = @file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
