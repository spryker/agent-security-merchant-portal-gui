<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Expander;

use Generated\Shared\Transfer\MerchantUserCriteriaTransfer;
use Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MerchantUserCriteriaExpander implements MerchantUserCriteriaExpanderInterface
{
    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig
     */
    protected AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->agentSecurityMerchantPortalGuiConfig = $agentSecurityMerchantPortalGuiConfig;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function expand(MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer): MerchantUserCriteriaTransfer
    {
        if (
            $this->authorizationChecker->isGranted($this->agentSecurityMerchantPortalGuiConfig->getRoleMerchantAgent())
            && $this->authorizationChecker->isGranted($this->agentSecurityMerchantPortalGuiConfig->getRoleAllowedToSwitch())
        ) {
            $merchantUserCriteriaTransfer = $merchantUserCriteriaTransfer->setStatus(null)->setMerchantStatus(null);
        }

        return $merchantUserCriteriaTransfer;
    }
}
