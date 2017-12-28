<?php

namespace SimpleSAML\Module\monitor\TestCase\Metadata;

use \SimpleSAML\Module\monitor\State as State;

final class Expiration extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $entityId = null;
    private $metadata = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->entityId = $this->getInput('entityId');
        $this->metadata = $this->getInput('metadata');
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        if (array_key_exists('expire', $this->metadata)) {
            $expiration = $this->metadata['expire'];
            if ($expiration <= time()) {
                $this->setState(State::ERROR);
                $this->addMessage(State::ERROR, 'Metadata expiration', $this->entityId, 'Metadata has expired');
            } else {
                $this->setState(State::OK);
                $this->addMessage(State::OK, 'Metadata expiration', $this->entityId, 'Metadata will expire on ' . strftime('%c', $expiration));
            }
        } else {
            $this->setState(State::OK);
            $this->addMessage(State::OK, 'Metadata expiration', $this->entityId, 'Metadata never expires');
        }
    }
}

