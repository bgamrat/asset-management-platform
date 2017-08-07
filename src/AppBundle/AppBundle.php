<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use AppBundle\DependencyInjection\AppBundleExtension;

class AppBundle extends Bundle
{

    public function getContainerExtension()
    {
        if( null === $this->extension )
        {
            $this->extension = new AppBundleExtension();
        }


        return $this->extension;
    }

}
