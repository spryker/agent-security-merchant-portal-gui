<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Dependency\Client;

interface AgentSecurityMerchantPortalGuiToSessionClientInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function set(string $name, $value);

    /**
     * @return mixed
     */
    public function remove(string $name);
}
