<?php

/**
 * Copyright 2017 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Notice: this file is not the original SURFnet-file; it was rewriten to accomodate this project
 */

namespace SimpleSAML\Module\monitor\Controller;

use SimpleSAML\Module\monitor\HealthCheck\HealthCheckChain;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Display the health state of the application.
 *
 * The health controller is used to display the health of the application. Information is returned as a JSON response.
 * When one of the health checks (run by the HealthCheckerChain) fails, the DOWN message of that check is shown.
 */
class HealthController
{
    /**
     * @var HealthCheckChain
     */
    private $healthChecker;

    public function __construct(HealthCheckChain $healthChecker)
    {
        $this->healthChecker = $healthChecker;
    }

    public function healthAction()
    {
        $statusResponse = $this->healthChecker->check();
        return JsonResponse::create($statusResponse, $statusResponse->getStatusCode());
    }
}

