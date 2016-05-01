<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Builder\Action\Profile\SingleLogout;

use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\DestinationAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSloAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\CreateLogoutRequestAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\LogoutResolveAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\ResolveLogoutPartyAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\SetNameIdAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\SetNotOnOrAfterAction;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\SetSessionIndexAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Action\Profile\Outbound\Message\SignMessageAction;
use LightSaml\Builder\Action\CompositeActionBuilder;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SloRequestActionBuilder extends AbstractProfileActionBuilder
{
    /**
     */
    protected function doInitialize()
    {
        $proceedActionBuilder = new CompositeActionBuilder();

        $proceedActionBuilder->add(new CreateLogoutRequestAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ), 100);
        $proceedActionBuilder->add(new MessageIdAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $proceedActionBuilder->add(new MessageVersionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::VERSION_20
        ));
        $proceedActionBuilder->add(new MessageIssueInstantAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider()
        ));
        $proceedActionBuilder->add(new ResolveLogoutPartyAction(
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ));
        $proceedActionBuilder->add(new ResolveEndpointSloAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));
        $proceedActionBuilder->add(new DestinationAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $proceedActionBuilder->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $proceedActionBuilder->add(new SetNameIdAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $proceedActionBuilder->add(new SetSessionIndexAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $proceedActionBuilder->add(new SetNotOnOrAfterAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider(),
            120
        ));
        $proceedActionBuilder->add(new SignMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureResolver()
        ));
        $proceedActionBuilder->add(new SendMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ));

        $this->add(new LogoutResolveAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getLogoutSessionResolver(),
            $proceedActionBuilder->build()
        ), 100);
    }
}
