<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Extender;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\SecurityExtension\Configuration\SecurityBuilderInterface;
use Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig;
use Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Builder\OptionsBuilderInterface;
use Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Plugin\Security\Handler\AccessDeniedHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SecurityBuilderExtender implements SecurityBuilderExtenderInterface
{
    /**
     * @uses \Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter::PUBLIC_ACCESS
     *
     * @var string
     */
    protected const PUBLIC_ACCESS = 'PUBLIC_ACCESS';

    protected const string SERVICE_SECURITY_TOKEN_STORAGE = 'security.token_storage';

    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiConfig
     */
    protected AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig;

    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Builder\OptionsBuilderInterface
     */
    protected OptionsBuilderInterface $optionsBuilder;

    /**
     * @var \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    protected EventSubscriberInterface $switchUserEventSubscriber;

    /**
     * @var \Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Extender\SecurityBuilderAuthenticatorExtenderInterface
     */
    protected SecurityBuilderAuthenticatorExtenderInterface $securityBuilderAuthenticatorExtender;

    public function __construct(
        AgentSecurityMerchantPortalGuiConfig $agentSecurityMerchantPortalGuiConfig,
        OptionsBuilderInterface $optionsBuilder,
        EventSubscriberInterface $switchUserEventSubscriber,
        SecurityBuilderAuthenticatorExtenderInterface $securityBuilderAuthenticatorExtender
    ) {
        $this->agentSecurityMerchantPortalGuiConfig = $agentSecurityMerchantPortalGuiConfig;
        $this->optionsBuilder = $optionsBuilder;
        $this->switchUserEventSubscriber = $switchUserEventSubscriber;
        $this->securityBuilderAuthenticatorExtender = $securityBuilderAuthenticatorExtender;
    }

    public function extend(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface
    {
        $securityBuilder = $this->addFirewalls($securityBuilder);
        $securityBuilder = $this->extendMerchantUser($securityBuilder);
        $securityBuilder = $this->addAccessRules($securityBuilder);
        $securityBuilder = $this->addAccessDeniedHandler($securityBuilder, $container);
        $securityBuilder = $this->addSwitchUserEventSubscriber($securityBuilder);
        $securityBuilder = $this->securityBuilderAuthenticatorExtender->extend($securityBuilder, $container);

        return $securityBuilder;
    }

    public function extendAgent(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface
    {
        $securityBuilder = $this->addFirewalls($securityBuilder);
        $securityBuilder = $this->addAccessRules($securityBuilder);
        $securityBuilder = $this->addAccessDeniedHandler($securityBuilder, $container);
        $securityBuilder = $this->addSwitchUserEventSubscriber($securityBuilder);
        $securityBuilder = $this->securityBuilderAuthenticatorExtender->extend($securityBuilder, $container);

        return $securityBuilder;
    }

    public function extendMerchantUser(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        $securityBuilder->mergeFirewall($this->agentSecurityMerchantPortalGuiConfig->getMerchantUserSecurityFirewallName(), [
            'context' => $this->agentSecurityMerchantPortalGuiConfig->getSecurityFirewallName(),
            'switch_user' => [
                'parameter' => '_switch_user',
                'role' => $this->agentSecurityMerchantPortalGuiConfig->getRoleAllowedToSwitch(),
            ],
        ]);

        return $securityBuilder;
    }

    protected function addFirewalls(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        $securityBuilder->addFirewall(
            $this->agentSecurityMerchantPortalGuiConfig->getSecurityFirewallName(),
            $this->optionsBuilder->buildOptions(),
        );

        return $securityBuilder;
    }

    protected function addAccessRules(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        return $securityBuilder->addAccessRules([
            [
                $this->agentSecurityMerchantPortalGuiConfig->getRoutePatternAgentMerchantPortalLogin(),
                static::PUBLIC_ACCESS,
            ],
            [
                $this->agentSecurityMerchantPortalGuiConfig->getRoutePatternAgentMerchantPortal(),
                [
                    $this->agentSecurityMerchantPortalGuiConfig->getRoleMerchantAgent(),
                    $this->agentSecurityMerchantPortalGuiConfig->getRolePreviousAdmin(),
                ],
            ],
        ]);
    }

    protected function addAccessDeniedHandler(SecurityBuilderInterface $securityBuilder, ContainerInterface $container): SecurityBuilderInterface
    {
        $securityBuilder->addAccessDeniedHandler(
            $this->agentSecurityMerchantPortalGuiConfig->getSecurityFirewallName(),
            function () use ($container) {
                return new AccessDeniedHandler(
                    $container->get(static::SERVICE_SECURITY_TOKEN_STORAGE),
                    $this->agentSecurityMerchantPortalGuiConfig->getUrlLogin(),
                );
            },
        );

        return $securityBuilder;
    }

    protected function addSwitchUserEventSubscriber(SecurityBuilderInterface $securityBuilder): SecurityBuilderInterface
    {
        return $securityBuilder->addEventSubscriber(function () {
            return $this->switchUserEventSubscriber;
        });
    }
}
