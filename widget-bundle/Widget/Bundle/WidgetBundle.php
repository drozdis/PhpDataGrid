<?php

namespace Widget\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Widget\Bundle\DependencyInjection\Compiler\FormPass;

class WidgetBundle extends Bundle
{
    /**
     * @inherit
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FormPass());
    }
}
