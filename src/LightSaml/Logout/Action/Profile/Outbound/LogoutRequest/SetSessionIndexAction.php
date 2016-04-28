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
 * Sets SessionIndex of the outbounding AuthnRequest with values given in the LogoutContext SsoSessionState.
 */
class SetSessionIndexAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $logoutRequest = MessageContextHelper::asLogoutRequest($context->getOutboundContext());
        $ssoSessionState = $context->getLogoutSsoSessionState();

        $logoutRequest->setSessionIndex($ssoSessionState->getSessionIndex());
    }
}
