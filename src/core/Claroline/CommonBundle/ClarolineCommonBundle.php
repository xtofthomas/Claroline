<?php

namespace Claroline\CommonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ClarolineCommonBundle extends Bundle
{
    public function getInstallationIndex()
    {
        return 2;
    }
}