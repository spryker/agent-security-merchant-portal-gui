<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Extender;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\SecurityExtension\Configuration\SecurityBuilderInterface;
use Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig;
use Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Checker\SymfonyVersionCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

class SecurityBuilderAuthenticatorExtender implements SecurityBuilderAuthenticatorExtenderInterface
{
    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig
     */
    protected AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig;

    /**
     * @var \Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface
     */
    protected AuthenticatorInterface $agentMerchantLoginFormAuthenticator;

    /**
     * @var \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface
     */
    protected AuthenticationSuccessHandlerInterface $authenticationSuccessHandler;

    /**
     * @var \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface
     */
    protected AuthenticationFailureHandlerInterface $authenticationFailureHandler;

    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Checker\SymfonyVersionCheckerInterface
     */
    protected SymfonyVersionCheckerInterface $symfonyVersionChecker;

    public function __construct(
        AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig,
        AuthenticatorInterface $agentMerchantLoginFormAuthenticator,
        AuthenticationSuccessHandlerInterface $authenticationSuccessHandler,
        AuthenticationFailureHandlerInterface $authenticationFailureHandler,
        SymfonyVersionCheckerInterface $symfonyVersionChecker
    ) {
        $this->agentSecurityMerchantPortalGuiConfig = $agentSecurityMerchantPortalGuiConfig;
        $this->agentMerchantLoginFormAuthenticator = $agentMerchantLoginFormAuthenticator;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->authenticationFailureHandler = $authenticationFailureHandler;
        $this->symfonyVersionChecker = $symfonyVersionChecker;
    }

    public function extend(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface
    {
        if ($this->symfonyVersionChecker->isSymfonyVersion5()) {
            $securityBuilder = $this->addAuthenticationSuccessHandler($securityBuilder);
            $securityBuilder = $this->addAuthenticationFailureHandler($securityBuilder);

            return $securityBuilder;
        }

        $container->set($this->agentSecurityMerchantPortalGuiConfig->getSecurityAgentMerchantPortalLoginFormAuthenticatorName(), function () {
            return $this->agentMerchantLoginFormAuthenticator;
        });

        return $securityBuilder;
    }

    protected function addAuthenticationSuccessHandler(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        $securityBuilder->addAuthenticationSuccessHandler($this->agentSecurityMerchantPortalGuiConfig->getSecurityFirewallName(), function () {
            return $this->authenticationSuccessHandler;
        });

        return $securityBuilder;
    }

    protected function addAuthenticationFailureHandler(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        $securityBuilder->addAuthenticationFailureHandler($this->agentSecurityMerchantPortalGuiConfig->getSecurityFirewallName(), function () {
            return $this->authenticationFailureHandler;
        });

        return $securityBuilder;
    }
}
