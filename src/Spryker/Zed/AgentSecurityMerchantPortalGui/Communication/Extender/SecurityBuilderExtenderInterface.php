<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Extender;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\SecurityExtension\Configuration\SecurityBuilderInterface;

interface SecurityBuilderExtenderInterface
{
    public function extend(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface;

    public function extendAgent(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface;

    public function extendMerchantUser(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface;
}
