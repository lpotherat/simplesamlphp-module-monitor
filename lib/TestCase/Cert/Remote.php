<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;

// We're cheating here, because this TestCase doesn't decent from Cert, like Data and File do.
final class Remote extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $connectString = null;
    private $context = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $hostname = $this->getInput('hostname');
        $port = $this->getInput('port');
        $this->setCategory($this->getInput('category'));
        $this->connectString = 'ssl://' . $hostname . ':' . $port;
        $this->context = stream_context_create(
            array(
                "ssl" => array(
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false
                )
            )
        );
    }

    protected function invokeTest()
    {
        $testsuite = $this->getTestSuite();
        $test = new TestCase\Network\ConnectUri(
            $testsuite,
            array(
                'uri' => $this->connectString,
                'context' => $this->context
            )
        );
        $state = $test->getState();

        if ($state === State::OK) { // Connection OK
            $connection = $test->getOutput('connection');
            $cert = stream_context_get_params($connection);
            if (isSet($cert['options']['ssl']['peer_certificate'])) {
                $test = new TestCase\Cert\Data(
                    $testsuite,
                    array(
                        'certData' => $cert['options']['ssl']['peer_certificate'],
                        'category' => $this->getCategory()
                    )
                );
                $this->setState($test->getState());
                $this->setMessages($test->getMessages());
            } else {
                $this->setState(State::SKIPPED);
                $this->addMessage(State::SKIPPED, $this->getCategory(), $this->connectString, 'Unable to capture peer certificate');
            }
        } else {
            $this->setState(State::FATAL);
            $this->setMessages($test->getMessages());
        }

        unset($test, $connection);
    }
}
