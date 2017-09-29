<?php

namespace Bkstg\FOSUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BkstgFOSUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
