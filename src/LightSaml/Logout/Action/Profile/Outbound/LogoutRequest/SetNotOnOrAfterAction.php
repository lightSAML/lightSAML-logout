<?php

namespace LightSaml\Logout\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Sets NotOnOrAfter attribute of the outbounding LogoutRequest to the value given by timeProvider plus secondsSkew
 */
class SetNotOnOrAfterAction extends AbstractProfileAction
{
    /** @var  TimeProviderInterface */
    protected $timeProvider;

    /** @var  int */
    protected $secondsSkew;

    /**
     * @param LoggerInterface       $logger
     * @param TimeProviderInterface $timeProvider
     * @param int                   $secondsSkew
     */
    public function __construct(LoggerInterface $logger, TimeProviderInterface $timeProvider, $secondsSkew)
    {
        parent::__construct($logger);
        $this->timeProvider = $timeProvider;
        $this->secondsSkew = $secondsSkew;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $logoutRequest = MessageContextHelper::asLogoutRequest($context->getOutboundContext());
        $logoutRequest->setNotOnOrAfter($this->timeProvider->getTimestamp() + $this->secondsSkew);
    }
}
