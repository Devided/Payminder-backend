<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Message\Node\Node;

interface ActionInterface
{
    /**
     * @return Node
     */
    public function createNode();

    /**
     * Validate the action parameters
     *
     * @return bool
     */
    public function isValid();
}
