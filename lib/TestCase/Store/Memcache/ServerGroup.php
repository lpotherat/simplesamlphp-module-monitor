<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use Webmozart\Assert\Assert;

final class ServerGroup extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var array */
    private $results = [];

    /** @var string */
    private $group;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->results = $testData->getInputItem('results');
        $this->group = $testData->getInputItem('group');

        Assert::isArray($this->results);
        Assert::string($this->group);

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult('Memcache Server Group Health', 'Group ' . $this->group);

        $states = [];
        foreach ($this->results as $result) {
            $states[] = $result->getState();
        }
        $state = min($states);
        if ($state !== max($states)) {
            $state = State::WARNING;
        }
        $testResult->setState($state);

        if ($state === State::OK) {
            $testResult->setMessage('Group is healthy');
        } elseif ($state === State::WARNING) {
            $testResult->setMessage('Group is crippled');
        } else {
            $testResult->setMessage('Group is down');
        }

        $this->setTestResult($testResult);
    }
}
