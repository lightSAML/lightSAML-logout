<?php

namespace LightSaml\Logout\Tests\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\SetNotOnOrAfterAction;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;

class SetNotOnOrAfterActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_time_provider_and_integer()
    {
        new SetNotOnOrAfterAction(
            $this->getLoggerMock(),
            $this->getTimeProviderMock(),
            100
        );
    }

    public function test_sets_not_on_or_after_to_outbound_logout_request()
    {
        $timeProviderMock = $this->getTimeProviderMock();
        $action = new SetNotOnOrAfterAction($this->getLoggerMock(), $timeProviderMock, $skew = 100);

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOutboundContext()->setMessage($logoutRequest = new LogoutRequest());

        $timeProviderMock->expects($this->once())
            ->method('getTimestamp')
            ->willReturn($baseTimestamp = 1445953125);

        $action->execute($context);

        $expectedTimestamp = $baseTimestamp + $skew;

        $this->assertEquals($expectedTimestamp, $logoutRequest->getNotOnOrAfterTimestamp());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Provider\TimeProvider\TimeProviderInterface
     */
    private function getTimeProviderMock()
    {
        return $this->getMock(TimeProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}
