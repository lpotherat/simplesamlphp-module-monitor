<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use SimpleSAML\Module\monitor\TestData;
use Webmozart\Assert\Assert;

class Data extends \SimpleSAML\Module\monitor\TestCase\Cert
{
    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certData = $testData->getInputItem('certData');
        Assert::string($certData);

        $certData = openssl_x509_parse($certData) ?: [];
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
