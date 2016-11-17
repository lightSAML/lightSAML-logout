<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Builder\Profile\WebBrowserSlo;

use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Logout\Builder\Action\Profile\SingleLogout\SloResponseActionBuilder;
use LightSaml\Logout\Profile\Profiles;

class SloResponseProfileBuilder extends AbstractProfileBuilder
{
    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SLO_RECEIVE_LOGOUT_RESPONSE;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_NONE;
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        return new SloResponseActionBuilder($this->container);
    }
}
