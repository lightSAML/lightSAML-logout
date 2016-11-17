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
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use Psr\Log\LoggerInterface;

class LogoutResolveAction extends AbstractProfileAction
{
    /** @var LogoutSessionResolverInterface */
    protected $logoutSessionResolver;

    /** @var ActionInterface */
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
        $logoutContext = $context->getLogoutContext();
        $ssoSessionState = $logoutContext->getSsoSessionState();
        if ($ssoSessionState) {
            $this->logger->debug(
                'SSO session already set',
                LogHelper::getActionContext($context, $this, array(
                    'sso_session' => $ssoSessionState,
                ))
            );
        } else {
            $this->logger->debug(
                'SSO session not defined, about to resolve it',
                LogHelper::getActionContext($context, $this, array())
            );
            $ssoSessionState = $this->logoutSessionResolver->resolve($context->getOwnEntityDescriptor()->getEntityID());
        }

        if ($ssoSessionState) {
            $this->logger->debug(
                'SSO session resolved and being used for logout profile',
                LogHelper::getActionContext($context, $this, array(
                    'sso_session' => $ssoSessionState,
                ))
            );
            $logoutContext->setSsoSessionState($ssoSessionState);
            $this->logoutProceedAction->execute($context);
        } else {
            $this->logger->debug(
                'There is no SSO session for logout',
                LogHelper::getActionContext($context, $this, array())
            );
            $logoutContext->setAllSsoSessionsTerminated(true);
        }
    }
}
