<?php

namespace LightSaml\Logout\Resolver\Logout;

use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class LogoutSessionResolver implements LogoutSessionResolverInterface
{
    /** @var  SsoStateStoreInterface */
    protected $ssoStateStore;

    /**
     * @param SsoStateStoreInterface $ssoStateStore
     */
    public function __construct(SsoStateStoreInterface $ssoStateStore)
    {
        $this->ssoStateStore = $ssoStateStore;
    }

    /**
     * @param string $ownEntityId
     *
     * @return SsoSessionState|null
     */
    public function resolve($ownEntityId)
    {
        $ssoState = $this->ssoStateStore->get();

        $result = $this->getSpSession($ssoState, $ownEntityId);
        if ($result) {
            return $result;
        }

        $result = $this->getIdpSession($ssoState, $ownEntityId);

        return $result;
    }

    /**
     * @param SsoState $ssoState
     * @param string   $ownEntityId
     *
     * @return SsoSessionState|null
     */
    protected function getSpSession(SsoState $ssoState, $ownEntityId)
    {
        $spSessions = $ssoState->filter($ownEntityId, null, null, null, null);

        return array_shift($spSessions);
    }

    /**
     * @param SsoState $ssoState
     * @param string   $ownEntityId
     *
     * @return SsoSessionState|null
     */
    protected function getIdpSession(SsoState $ssoState, $ownEntityId)
    {
        $idpSessions = $ssoState->filter(null, $ownEntityId, null, null, null);

        return array_shift($idpSessions);
    }
}
