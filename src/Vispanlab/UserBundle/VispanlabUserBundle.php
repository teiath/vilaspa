<?php

namespace Vispanlab\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VispanlabUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
