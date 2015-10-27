<?php

namespace LightSaml\Logout\Tests\Resolver\Logout;

use LightSaml\Logout\Resolver\Logout\LogoutSessionResolver;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class LogoutSessionResolverTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_state_store()
    {
        new LogoutSessionResolver($this->getStateStoreMock());
    }

    public function test_returns_null_on_empty_sessions()
    {
        $ssoState = new SsoState();
        $resolver = new LogoutSessionResolver($this->getStateStoreStub($ssoState));
        $this->assertNull($resolver->resolve('own.entity.id'));
    }

    public function resolution_provider()
    {
        return [
            [[], null, null],

            [[true], 0, 'getIdpEntityId'],
            [[false, true, false, true], 1, 'getIdpEntityId'],
            [[false, false, true, false, true], 2, 'getIdpEntityId'],
            [[false, false, false, true, false, true], 3, 'getIdpEntityId'],

            [[false], 0, 'getSpEntityId'],
            [[false, false], 0, 'getSpEntityId'],
            [[false, false, false], 0, 'getSpEntityId'],
        ];
    }

    /**
     * @dataProvider resolution_provider
     */
    public function test_resolution(array $arrIsIdp, $expectedSessionIndex, $property)
    {
        $ssoState = $this->buildSsoState($ownEntityId = 'own.entity.id', $arrIsIdp);
        $allSessions = $ssoState->getSsoSessions();
        $resolver = new LogoutSessionResolver($this->getStateStoreStub($ssoState));
        $session = $resolver->resolve($ownEntityId);
        if (null === $expectedSessionIndex) {
            $this->assertNull($session);
        } else {
            $this->assertEquals($ownEntityId, $session->{$property}());
            $this->assertSame($allSessions[$expectedSessionIndex], $session);
        }
    }

    /**
     * @param string $ownEntityId
     * @param array  $arrIsIdp
     *
     * @return SsoState
     */
    private function buildSsoState($ownEntityId, array $arrIsIdp)
    {
        $ssoState = new SsoState();
        foreach ($arrIsIdp as $isIdp) {
            $otherEntityId = 'other.'.mt_rand(1000, 9999);
            if ($isIdp) {
                $ssoState->addSsoSession((new SsoSessionState())->setIdpEntityId($ownEntityId)->setSpEntityId($otherEntityId));
            } else {
                $ssoState->addSsoSession((new SsoSessionState())->setIdpEntityId($otherEntityId)->setSpEntityId($ownEntityId));
            }
        }

        return $ssoState;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\Sso\SsoStateStoreInterface
     */
    private function getStateStoreStub(SsoState $state)
    {
        $mock = $this->getMock(SsoStateStoreInterface::class);
        $mock->expects($this->any())
            ->method('get')
            ->willReturn($state);

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\Sso\SsoStateStoreInterface
     */
    private function getStateStoreMock()
    {
        return $this->getMock(SsoStateStoreInterface::class);
    }
}
