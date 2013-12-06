<?php
namespace Widget\Bundle\Grid\Type;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Widget\Grid\AbstractGridCreator;
use Widget\Grid\AbstractType;
use Widget\Grid\GridBuilder;

/**
 * Class AbstractSymfonyType
 */
abstract class AbstractSymfonyType extends AbstractType
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
}