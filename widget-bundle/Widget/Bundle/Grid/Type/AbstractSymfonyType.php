<?php
namespace Widget\Bundle\Grid\Type;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Widget\Grid\AbstractType;

/**
 * Class AbstractSymfonyType
 */
abstract class AbstractSymfonyType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}