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

interface LogoutSessionResolverInterface
{
    /**
     * @param string $ownEntityId
     *
     * @return SsoSessionState|null
     */
    public function resolve($ownEntityId);

    /**
     * @param string $entityId
     * @param string $nameId
     * @param string $nameIdFormat
     * @param string $sessionIndex
     *
     * @return int Number of sso sessions terminated for given arguments
     */
    public function terminateSession($entityId, $nameId, $nameIdFormat, $sessionIndex = null);
}
