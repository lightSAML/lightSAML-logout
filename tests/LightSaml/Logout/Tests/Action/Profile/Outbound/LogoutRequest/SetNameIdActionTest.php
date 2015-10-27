<?php

namespace LightSaml\Logout\Tests\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Action\Profile\Outbound\LogoutRequest\SetNameIdAction;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Profile\Profiles;
use LightSaml\State\Sso\SsoSessionState;
use Psr\Log\LoggerInterface;

class SetNameIdActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new SetNameIdAction($this->getLoggerMock());
    }

    public function test_sets_name_id_to_outbound_logout_request()
    {
        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOutboundContext()->setMessage($logoutRequest = new LogoutRequest());
        $context->getLogoutContext()->setSsoSessionState(
            (new SsoSessionState())
            ->setNameId($nameId = 'name.id')
            ->setNameIdFormat($nameIdFormat = 'name.id.format')
        );

        $action = new SetNameIdAction($this->getLoggerMock());
        $action->execute($context);

        $this->assertNotNull($logoutRequest->getNameID());
        $this->assertEquals($nameId, $logoutRequest->getNameID()->getValue());
        $this->assertEquals($nameIdFormat, $logoutRequest->getNameID()->getFormat());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }
}
