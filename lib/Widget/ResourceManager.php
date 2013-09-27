<?php
namespace Widget;

/**
 * Class ResourceManager
 *
 * @package Widget
 */
class ResourceManager implements ResourceManagerInterface
{
    /**
     * @var array
     */
    protected $_javascriptFiles = array();

    /**
     * @var array
     */
    protected $_javascript = array();

    /**
     * @var array
     */
    protected $_styleSheetFiles = array();

    /**
     * @var array
     */
    protected $_stylesheet = array();


    /**
     * @param array|string $files
     */
    public function addJavascriptFile($files)
    {
        $this->_javascriptFiles = array_merge($this->_javascriptFiles, (array)$files);
    }

    /**
     * @param string $content
     */
    public function addJavascript($content)
    {
        $this->_javascript[] = $content;
    }

    /**
     * @param array|string $files
     */
    public function addStyleSheetFile($files)
    {
        $this->_styleSheetFiles = array_merge($this->_styleSheetFiles, (array)$files);
    }

    /**
     * @param string $content
     */
    public function addStyleSheet($content)
    {
        $this->_stylesheet[] = $content;
    }

    /**
     *  render
     */
    public function render()
    {
        $result = array();
        foreach ($this->_javascriptFiles as $file) {
            $result[] = '<script type="text/javascript" src="'.$file.'"></script>';
        }

        if (!empty($this->_javascript)) {
            $result[] = '<script type="text/javascript">';
            foreach ($this->_javascript as $file) {
                $result[] = $file;
            }
            $result[] = '</script>';
        }

        foreach ($this->_styleSheetFiles as $file) {
            $result[] = '<link rel="stylesheet" href="'.$file.'" />';
        }

        if (!empty($this->_stylesheet)) {
            $result[] = '<style>';
            foreach ($this->_stylesheet as $file) {
                $result[] = $file;
            }
            $result[] = '</style>';
        }
        return join("\n", $result);
    }

}