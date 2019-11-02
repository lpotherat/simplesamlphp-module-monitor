<?php

namespace SimpleSAML\Module\Monitor\TestCase\Store\Memcache;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use Webmozart\Assert\Assert;

final class ServerGroup extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var array */
    private $results = [];

    /** @var string */
    private $group;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
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
