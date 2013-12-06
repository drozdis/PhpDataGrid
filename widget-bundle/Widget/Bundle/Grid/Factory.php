<?php
namespace Widget\Bundle\Grid;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Widget\Bundle\Grid\Creator\AbstractSymfonyType;
use Widget\Grid\Grid;
use Widget\Grid\GridBuilder;
use Widget\Grid\GridFactory;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param AbstractSymfonyType $type
     * @param string              $class
     * @param array               $options
     *
     * @return Grid
     */
    public function createGrid(AbstractSymfonyType $type, $class = 'grid', $options = array())
    {
        $type->setTranslator($this->translator);

        return GridFactory::createGrid($type, $class, $options);
    }

    /**
     * @param string|Grid $class
     *
     * @return GridBuilder
     */
    public function createBuilder($class)
    {
        return GridFactory::createBuilder($class);
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
} 