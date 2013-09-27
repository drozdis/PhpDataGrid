<?php
namespace Widget;

/**
 * @package Widget
 */
interface ResourceManagerInterface
{
    /**
     * @param array|string $files
     */
    public function addJavascriptFile($files);

    /**
     * @param string $content
     */
    public function addJavascript($content);

    /**
     * @param array|string $files
     */
    public function addStyleSheetFile($files);

    /**
     * @param string $content
     */
    public function addStyleSheet($content);

    /**
     *  render
     */
    public function render();
}