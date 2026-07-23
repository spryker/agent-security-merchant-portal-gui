<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Dependency\Facade;

use Generated\Shared\Transfer\MerchantUserCriteriaTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;

interface AgentSecurityMerchantPortalGuiToMerchantUserFacadeInterface
{
    public function authenticateMerchantUser(MerchantUserTransfer $merchantUserTransfer): void;

    public function findMerchantUser(MerchantUserCriteriaTransfer $merchantUserCriteriaTransfer): ?MerchantUserTransfer;
}
