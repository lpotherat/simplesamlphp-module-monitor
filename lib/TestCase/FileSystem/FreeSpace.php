<?php

namespace SimpleSAML\Modules\Monitor\TestCase\FileSystem;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class FreeSpace extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /** @var string */
    private $path = '';


    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData)
    {
        $this->setPath($testData->getInputItem('path'));
        $this->setCategory($testData->getInputItem('category'));
        parent::initialize($testData);
    }


    /**
     * @param string $path
     * @return void
     */
    private function setPath($path)
    {
        assert(is_string($path));
        $this->path = $path;
    }


    /**
     * @return string
     */
    private function getPath()
    {
        assert(is_string($this->path));
        return $this->path;
    }


    /**
     * @return void
     */
    public function invokeTest()
    {
        $path = $this->getPath();
        $testResult = new TestResult($this->getCategory(), $path);

        $size = disk_total_space($path);
        $free = disk_free_space($path);
        if ($size !== false && $free !== false) {
            $free = round(100 - ((($size - $free) / $size) * 100));

            if ($free >= 15) {
                $testResult->setMessage($free.'% free space');
                $testResult->setState(State::OK);
            } else if ($free < 5) {
                $testResult->setMessage('Critical: '.$free.'% free space');
                $testResult->setState(State::ERROR);
            } else {
                $testResult->setMessage($free.'% free space');
                $testResult->setState(State::WARNING);
            }
            $testResult->addOutput($free, 'free_percentage');
        } else {
            $testResult->setMessage('Error collecting disk usage');
            $testResult->setState(State::FATAL);
        }
        $this->setTestResult($testResult);
    }
}
