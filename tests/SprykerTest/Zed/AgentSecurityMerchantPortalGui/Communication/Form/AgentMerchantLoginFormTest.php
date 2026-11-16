<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AgentSecurityMerchantPortalGui\Communication\Form;

use Codeception\Test\Unit;
use Spryker\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiDependencyProvider;
use Spryker\Zed\AgentSecurityMerchantPortalGui\Communication\Form\AgentMerchantLoginForm;
use Spryker\Zed\SecurityMerchantPortalGuiExtension\Dependency\Plugin\MerchantPortalLoginFormExpanderPluginInterface;
use SprykerTest\Zed\AgentSecurityMerchantPortalGui\AgentSecurityMerchantPortalGuiCommunicationTester;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AgentSecurityMerchantPortalGui
 * @group Communication
 * @group Form
 * @group AgentMerchantLoginFormTest
 * Add your own group annotations below this line
 */
class AgentMerchantLoginFormTest extends Unit
{
    protected const string FIELD_CAPTCHA = 'captcha';

    protected AgentSecurityMerchantPortalGuiCommunicationTester $tester;

    public function testBuildFormAddsFieldOfMerchantPortalAgentLoginFormExpanderPluginBeforeLoginButton(): void
    {
        // Arrange
        $this->tester->setDependency(AgentSecurityMerchantPortalGuiDependencyProvider::PLUGINS_MERCHANT_PORTAL_AGENT_LOGIN_FORM_EXPANDER, [
            $this->createCaptchaFieldExpanderPlugin(),
        ]);

        // Act
        $form = $this->createAgentMerchantLoginForm();

        // Assert
        $this->assertSame(
            [AgentMerchantLoginForm::FIELD_USERNAME, AgentMerchantLoginForm::FIELD_PASSWORD, static::FIELD_CAPTCHA, AgentMerchantLoginForm::BUTTON_LOGIN],
            array_keys($form->all()),
        );
    }

    public function testBuildFormKeepsLoginFieldsWhenNoMerchantPortalAgentLoginFormExpanderPluginIsRegistered(): void
    {
        // Act
        $form = $this->createAgentMerchantLoginForm();

        // Assert
        $this->assertSame(
            [AgentMerchantLoginForm::FIELD_USERNAME, AgentMerchantLoginForm::FIELD_PASSWORD, AgentMerchantLoginForm::BUTTON_LOGIN],
            array_keys($form->all()),
        );
    }

    /**
     * @return \Symfony\Component\Form\FormInterface<mixed>
     */
    protected function createAgentMerchantLoginForm(): FormInterface
    {
        $agentMerchantLoginFormMock = $this->getMockBuilder(AgentMerchantLoginForm::class)
            ->onlyMethods(['getFactory', 'getConfig'])
            ->getMock();
        $agentMerchantLoginFormMock->expects($this->atLeastOnce())
            ->method('getFactory')
            ->willReturn($this->tester->getFactory());
        $agentMerchantLoginFormMock->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn($this->tester->getModuleConfig());

        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addType($agentMerchantLoginFormMock)
            ->getFormFactory()
            ->create($agentMerchantLoginFormMock::class);
    }

    protected function createCaptchaFieldExpanderPlugin(): MerchantPortalLoginFormExpanderPluginInterface
    {
        $merchantPortalLoginFormExpanderPluginMock = $this->createMock(MerchantPortalLoginFormExpanderPluginInterface::class);
        $merchantPortalLoginFormExpanderPluginMock->expects($this->once())
            ->method('expand')
            ->willReturnCallback(function (FormBuilderInterface $builder): FormBuilderInterface {
                return $builder->add(static::FIELD_CAPTCHA, TextType::class);
            });

        return $merchantPortalLoginFormExpanderPluginMock;
    }
}
