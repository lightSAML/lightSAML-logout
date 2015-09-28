<?php

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
}
