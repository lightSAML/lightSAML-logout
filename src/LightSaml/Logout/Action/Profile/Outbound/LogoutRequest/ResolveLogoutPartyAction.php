<?php

/*
 * This file is part of the LightSAML-Logout package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Logout\Action\Profile\Outbound\LogoutRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;

class ResolveLogoutPartyAction extends AbstractProfileAction
{
    /** @var EntityDescriptorStoreInterface */
    private $idpEntityDescriptorStore;

    /** @var EntityDescriptorStoreInterface */
    private $spEntityDescriptorStore;

    /** @var TrustOptionsStoreInterface */
    protected $trustOptionsProvider;

    /**
     * @param EntityDescriptorStoreInterface $idpEntityDescriptorStore
     * @param EntityDescriptorStoreInterface $spEntityDescriptorStore
     * @param TrustOptionsStoreInterface     $trustOptionsProvider
     */
    public function __construct(
        EntityDescriptorStoreInterface $idpEntityDescriptorStore,
        EntityDescriptorStoreInterface $spEntityDescriptorStore,
        TrustOptionsStoreInterface $trustOptionsProvider
    ) {
        $this->idpEntityDescriptorStore = $idpEntityDescriptorStore;
        $this->spEntityDescriptorStore = $spEntityDescriptorStore;
        $this->trustOptionsProvider = $trustOptionsProvider;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $partyContext = $context->getPartyEntityContext();

        $partyEntityDescriptor = $this->getPartyEntityDescriptor($context);
        $partyContext
            ->setEntityId($partyEntityDescriptor->getEntityID())
            ->setEntityDescriptor($partyEntityDescriptor);

        $trustOptions = $this->trustOptionsProvider->get($partyContext->getEntityDescriptor()->getEntityID());
        if (null === $trustOptions) {
            $trustOptions = new TrustOptions();
        }
        $partyContext->setTrustOptions($trustOptions);
    }

    private function getPartyEntityDescriptor(ProfileContext $context)
    {
        $ssoSessionState = $context->getLogoutSsoSessionState();
        $ownEntityId = $context->getOwnEntityDescriptor()->getEntityID();
        $partyId = $ssoSessionState->getOtherPartyId($ownEntityId);

        $partyEntityDescriptor = $this->findParty($partyId, [$this->idpEntityDescriptorStore, $this->spEntityDescriptorStore]);

        if ($partyEntityDescriptor) {
            return $partyEntityDescriptor;
        }

        throw new LightSamlContextException($context, sprintf('Unknown party "%s"', $partyId));
    }

    /**
     * @param string                           $entityId
     * @param EntityDescriptorStoreInterface[] $entityDescriptorStores
     *
     * @return \LightSaml\Model\Metadata\EntityDescriptor|null
     */
    private function findParty($entityId, array $entityDescriptorStores)
    {
        foreach ($entityDescriptorStores as $entityDescriptorStore) {
            $entityDescriptor = $entityDescriptorStore->get($entityId);
            if ($entityDescriptor) {
                return $entityDescriptor;
            }
        }

        return;
    }
}
