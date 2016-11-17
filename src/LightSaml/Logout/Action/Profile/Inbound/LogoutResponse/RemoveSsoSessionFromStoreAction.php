<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Action\Profile\Inbound\LogoutResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use LightSaml\State\Request\RequestStateParameters;
use LightSaml\Store\Request\RequestStateStoreInterface;
use Psr\Log\LoggerInterface;

class RemoveSsoSessionFromStoreAction extends AbstractProfileAction
{
    /** @var RequestStateStoreInterface */
    private $requestStore;

    /** @var LogoutSessionResolverInterface */
    private $logoutResolver;

    /**
     * @param LoggerInterface                $logger
     * @param RequestStateStoreInterface     $requestStore
     * @param LogoutSessionResolverInterface $logoutResolver
     */
    public function __construct(LoggerInterface $logger, RequestStateStoreInterface $requestStore, LogoutSessionResolverInterface $logoutResolver)
    {
        parent::__construct($logger);

        $this->requestStore = $requestStore;
        $this->logoutResolver = $logoutResolver;
    }

    protected function doExecute(ProfileContext $context)
    {
        $logoutResponse = MessageContextHelper::asLogoutResponse($context->getInboundContext());
        $id = $logoutResponse->getInResponseTo();
        $requestState = $this->requestStore->get($id);
        $partyEntityId = $requestState->getParameters()->get(RequestStateParameters::PARTY);
        if ($partyEntityId && $logoutResponse->getIssuer() && $partyEntityId != $logoutResponse->getIssuer()->getValue()) {
            $message = sprintf(
                'LogoutRequest sent to %s but LogoutResponse for that request was issued by %s',
                $partyEntityId,
                $logoutResponse->getIssuer()->getValue()
            );
            $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this, [
                'sent_to' => $partyEntityId,
                'received_from' => $logoutResponse->getIssuer()->getValue(),
            ]));
            throw new LightSamlContextException($context, $message);
        }

        $nameId = $requestState->getParameters()->get(RequestStateParameters::NAME_ID);
        $nameIdFormat = $requestState->getParameters()->get(RequestStateParameters::NAME_ID_FORMAT);
        $sessionIndex = $requestState->getParameters()->get(RequestStateParameters::SESSION_INDEX);

        $numberOfTerminatedSessions = $this->logoutResolver->terminateSession(
            $logoutResponse->getIssuer()->getValue(),
            $nameId,
            $nameIdFormat,
            $sessionIndex
        );

        $this->logger->debug(
            sprintf(
                'Processing LogoutResponse from %s for %s in format %s and session index %s resulted in termination of %s sso session from the store',
                $partyEntityId,
                $nameId,
                $nameIdFormat,
                $sessionIndex,
                $numberOfTerminatedSessions
            ),
            LogHelper::getActionContext($context, $this)
        );
    }
}
