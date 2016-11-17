<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Resolver\Logout;

use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class LogoutSessionResolver implements LogoutSessionResolverInterface
{
    /** @var SsoStateStoreInterface */
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

    public function terminateSession($entityId, $nameId, $nameIdFormat, $sessionIndex = null)
    {
        $ssoState = $this->ssoStateStore->get();

        $count = 0;

        $ssoState->modify(function (SsoSessionState $session) use ($entityId, $nameId, $nameIdFormat, &$count) {
            if (($session->getIdpEntityId() == $entityId || $session->getSpEntityId() == $entityId) &&
                $session->getNameId() == $nameId &&
                $session->getNameIdFormat() == $nameIdFormat
            ) {
                ++$count;

                return false;
            }

            return true;
        });

        $this->ssoStateStore->set($ssoState);

        return $count;
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
