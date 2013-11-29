<?php
namespace Widget\Bundle\Grid\Creator;

use Widget\Grid\AbstractGridCreator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Widget\Grid\GridBuilder;

/**
 * Class SymfonyCreator
 */
class SymfonyCreator extends AbstractGridCreator
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param GridBuilder $builder
     */
    public function buildGrid(GridBuilder $builder)
    {

    }
}