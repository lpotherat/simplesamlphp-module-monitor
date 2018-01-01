<?php

namespace SimpleSAML\Module\monitor\TestCase\Module;

use \SimpleSAML\Module\monitor\TestData as TestData;

final class Php extends \SimpleSAML\Module\monitor\TestCase\Module
{
    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function initialize($testData = null)
    {
        $this->setCategory('Php');
        parent::initialize($testData);
    }
}
