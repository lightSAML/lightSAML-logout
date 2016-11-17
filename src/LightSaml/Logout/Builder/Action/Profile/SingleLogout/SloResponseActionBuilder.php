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

use LightSaml\Action\Profile\FlushRequestStatesAction;
use LightSaml\Action\Profile\Inbound\Message\EntityIdFromMessageIssuerAction;
use LightSaml\Action\Profile\Inbound\Message\IssuerValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\MessageSignatureValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\ReceiveMessageAction;
use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\InResponseToValidatorAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\StatusAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\Logout\Action\Profile\Inbound\LogoutResponse\RemoveSsoSessionFromStoreAction;
use LightSaml\SamlConstants;

class SloResponseActionBuilder extends AbstractProfileActionBuilder
{
    protected function doInitialize()
    {
        $this->add(new ReceiveMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 100);

        // Response validation
        $this->add(new IssuerValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getNameIdValidator(),
            SamlConstants::NAME_ID_FORMAT_ENTITY
        ), 200);
        $this->add(new EntityIdFromMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ));
        $this->add(new InResponseToValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
        $this->add(new StatusAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageSignatureValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureValidator()
        ));
        $this->add(new RemoveSsoSessionFromStoreAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore(),
            $this->buildContainer->getServiceContainer()->getLogoutSessionResolver()
        ));
        $this->add(new FlushRequestStatesAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
    }
}
