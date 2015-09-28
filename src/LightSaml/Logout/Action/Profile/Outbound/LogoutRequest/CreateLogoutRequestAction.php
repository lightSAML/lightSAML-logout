<?php

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
