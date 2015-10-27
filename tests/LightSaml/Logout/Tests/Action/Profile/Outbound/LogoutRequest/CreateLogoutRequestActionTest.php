<?php

namespace LightSaml\Logout\Tests\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\CreateLogoutRequestAction;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Profile\Profiles;
use Psr\Log\LoggerInterface;

class CreateLogoutRequestActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new CreateLogoutRequestAction($this->getLoggerMock());
    }

    public function test_creates_logout_request_as_outbound_message()
    {
        $action = new CreateLogoutRequestAction($this->getLoggerMock());
        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $action->execute($context);

        $this->assertInstanceOf(LogoutRequest::class, $context->getOutboundMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}
