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

use LightSaml\Action\ActionInterface;
use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use Psr\Log\LoggerInterface;

class LogoutResolveAction extends AbstractProfileAction
{
    /** @var  LogoutSessionResolverInterface */
    protected $logoutSessionResolver;

    /** @var  ActionInterface */
    protected $logoutProceedAction;

    /**
     * @param LoggerInterface                $logger
     * @param LogoutSessionResolverInterface $logoutSessionResolver
     * @param ActionInterface                $logoutProceedAction
     */
    public function __construct(
        LoggerInterface $logger,
        LogoutSessionResolverInterface $logoutSessionResolver,
        ActionInterface $logoutProceedAction
    ) {
        parent::__construct($logger);

        $this->logoutSessionResolver = $logoutSessionResolver;
        $this->logoutProceedAction = $logoutProceedAction;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $ssoSessionState = $this->logoutSessionResolver->resolve($context->getOwnEntityDescriptor()->getEntityID());
        $logoutContext = $context->getLogoutContext();
        if ($ssoSessionState) {
            $logoutContext->setSsoSessionState($ssoSessionState);
            $this->logoutProceedAction->execute($context);
        } else {
            $logoutContext->setAllSsoSessionsTerminated(true);
        }
    }
}
