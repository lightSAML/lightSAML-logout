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
use LightSaml\Logout\Builder\Action\Profile\SingleLogout\SloRequestActionBuilder;
use LightSaml\Logout\Profile\Profiles;

class SloRequestProfileBuilder extends AbstractProfileBuilder
{
    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SLO_SEND_LOGOUT_REQUEST;
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
        return new SloRequestActionBuilder($this->container);
    }
}
