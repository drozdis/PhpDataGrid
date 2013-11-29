<?php
namespace Widget\Bundle\Grid;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Widget\Bundle\Grid\Creator\SymfonyCreator;
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
     * @param SymfonyCreator $creator
     * @param string         $type
     * @param array          $options
     *
     * @return Grid
     */
    public function createGrid(SymfonyCreator $creator, $type = 'grid', $options = array())
    {
        $creator->setTranslator($this->translator);

        return GridFactory::createGrid($creator, $type, $options);
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @return GridBuilder
     */
    public function createBuilder($type = 'grid', $options = array())
    {
        return GridFactory::createBuilder($type, $options);
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
} 