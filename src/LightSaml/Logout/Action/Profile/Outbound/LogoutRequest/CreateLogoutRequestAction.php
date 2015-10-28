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
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\LogoutRequest;

class CreateLogoutRequestAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $logoutRequest = new LogoutRequest();
        $context->getOutboundContext()->setMessage($logoutRequest);
    }
}
