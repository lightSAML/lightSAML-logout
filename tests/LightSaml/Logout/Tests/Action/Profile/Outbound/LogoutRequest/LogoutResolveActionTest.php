<?php

namespace LightSaml\Logout\Tests\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\LogoutResolveAction;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;
use LightSaml\State\Sso\SsoSessionState;
use Psr\Log\LoggerInterface;

class LogoutResolveActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_logout_resolver_and_proceed_action()
    {
        new LogoutResolveAction(
            $this->getLoggerMock(),
            $this->getLogoutResolverMock(),
            $this->getActionMock()
        );
    }

    public function test_sets_all_sessions_terminated_flag_when_no_sessions()
    {
        $loggerMock = $this->getLoggerMock();
        $resolverMock = $this->getLogoutResolverMock();
        $proceedActionMock = $this->getActionMock();
        $proceedActionMock->expects($this->never())->method('execute');

        $context = $this->buildContext();
        $action = new LogoutResolveAction($loggerMock, $resolverMock, $proceedActionMock);
        $action->execute($context);

        $this->assertTrue($context->getLogoutContext()->areAllSsoSessionsTerminated());
    }

    public function test_sets_resolved_session_to_logout_context_and_calls_proceed_action()
    {
        $context = $this->buildContext();

        $loggerMock = $this->getLoggerMock();
        $resolverMock = $this->getLogoutResolverMock();
        $proceedActionMock = $this->getActionMock();

        $session = new SsoSessionState();

        $resolverMock->expects($this->once())
            ->method('resolve')
            ->with($context->getOwnEntityDescriptor()->getEntityID())
            ->willReturn($session)
        ;

        $proceedActionMock->expects($this->once())
            ->method('execute')
            ->with($context)
        ;

        $action = new LogoutResolveAction($loggerMock, $resolverMock, $proceedActionMock);
        $action->execute($context);

        $this->assertNotNull($context->getLogoutContext()->getSsoSessionState());
        $this->assertSame($session, $context->getLogoutContext()->getSsoSessionState());
    }

    /**
     * @param string $ownEntityId
     *
     * @return ProfileContext
     */
    private function buildContext($ownEntityId = 'own.entity.id')
    {
        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId));

        return $context;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Action\ActionInterface
     */
    private function getActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface
     */
    private function getLogoutResolverMock()
    {
        return $this->getMock(LogoutSessionResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}
