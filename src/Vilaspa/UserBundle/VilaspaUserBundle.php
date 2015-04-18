<?php

namespace Vilaspa\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VilaspaUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
