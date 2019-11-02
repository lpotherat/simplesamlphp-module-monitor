<?php

namespace SimpleSAML\Module\Monitor\TestCase;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use Webmozart\Assert\Assert;

class Cert extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var array */
    private $certInfo = [];

    /** @var integer */
    private $expiration;

    /** @var integer|null */
    private $certExpirationWarning = null;


    /**
     * @var \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $category = $testData->getInputItem('category');
        $certData = $testData->getInputItem('certData');
        $certExpirationWarning = $testData->getInputItem('certExpirationWarning');

        Assert::string($category);
        Assert::isArray($certData);
        Assert::integer($certExpirationWarning);

        $this->setCategory($category);
        $this->setCertInfo($certData);
        $this->setCertExpirationWarning($certExpirationWarning);

        parent::initialize($testData);
    }


    /**
     * @return string
     */
    public function getSubject(): string
    {
        $certInfo = $this->getCertInfo();
        if (
            isset($certInfo['subject'])
            && !empty($certInfo['subject'])
            && array_key_exists('CN', $certInfo['subject'])
        ) {
            return 'CN=' . $certInfo['subject']['CN'];
        } elseif (isset($certInfo['serialNumber'])) {
            return 'SN=' . $certInfo['serialNumber'];
        } else {
            return 'UNKNOWN';
        }
    }


    /**
     * @param array $certInfo
     *
     * @return void
     */
    protected function setCertInfo(array $certInfo): void
    {
        $this->certInfo = $certInfo;
    }


    /**
     * @return array
     */
    protected function getCertInfo(): array
    {
        return $this->certInfo;
    }


    /**
     * @param int $certExpirationWarning
     *
     * @return void
     */
    protected function setCertExpirationWarning(int $certExpirationWarning): void
    {
        $this->certExpirationWarning = $certExpirationWarning;
    }


    /**
     * @return int|null
     */
    protected function getCertExpirationWarning(): ?int
    {
        return $this->certExpirationWarning;
    }


    /**
     * @return int
     */
    protected function getExpiration(): int
    {
        return $this->expiration;
    }


    /**
     * @param integer $expiration
     *
     * @return void
     */
    private function setExpiration(int $expiration): void
    {
        $this->expiration = $expiration;
    }


    /**
     * @return void
     */
    protected function calculateExpiration(): void
    {
        $certInfo = $this->getCertInfo();
        $expiration = (int)(($certInfo['validTo_time_t'] - time()) / 86400);
        $this->setExpiration($expiration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $this->calculateExpiration();

        $threshold = $this->getCertExpirationWarning();
        $expiration = $this->getExpiration();

        $days = abs($expiration);
        $daysStr = $days . ' ' . (($days === 1) ? 'day' : 'days');

        $testResult = new TestResult($this->getCategory(), $this->getSubject());

        if ($expiration < 0) {
            $testResult->setState(State::ERROR);
            $testResult->setMessage('Certificate has expired ' . $daysStr . ' ago');
        } elseif ($expiration <= $threshold) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage('Certificate will expire in ' . $daysStr);
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Certificate valid for another ' . $daysStr);
        }

        $testResult->addOutput($expiration, 'expiration');
        $this->setTestResult($testResult);
    }
}
