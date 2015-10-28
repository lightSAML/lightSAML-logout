<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\NameID;

/**
 * Sets NameID of the outbounding AuthnRequest with values given in the LogoutContext SsoSessionState.
 */
class SetNameIdAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $logoutRequest = MessageContextHelper::asLogoutRequest($context->getOutboundContext());
        $ssoSessionState = $context->getLogoutSsoSessionState();

        $nameId = new NameID();
        $nameId->setValue($ssoSessionState->getNameId());
        $nameId->setFormat($ssoSessionState->getNameIdFormat());

        $logoutRequest->setNameID($nameId);
    }
}
