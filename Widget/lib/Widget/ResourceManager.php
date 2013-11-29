<?php
namespace Widget;

/**
 * Class ResourceManager
 */
class ResourceManager implements ResourceManagerInterface
{
    /**
     * @var array
     */
    protected $javascriptFiles = array();

    /**
     * @var array
     */
    protected $javascript = array();

    /**
     * @var array
     */
    protected $styleSheetFiles = array();

    /**
     * @var array
     */
    protected $stylesheet = array();

    /**
     * @param array|string $files
     */
    public function addJavascriptFile($files)
    {
        $this->javascriptFiles = array_merge($this->javascriptFiles, (array) $files);
    }

    /**
     * @param string $content
     */
    public function addJavascript($content)
    {
        $this->javascript[] = $content;
    }

    /**
     * @param array|string $files
     */
    public function addStyleSheetFile($files)
    {
        $this->styleSheetFiles = array_merge($this->styleSheetFiles, (array) $files);
    }

    /**
     * @param string $content
     */
    public function addStyleSheet($content)
    {
        $this->stylesheet[] = $content;
    }

    /**
     *  render
     */
    public function render()
    {
        $result = array();
        foreach ($this->javascriptFiles as $file) {
            $result[] = '<script type="text/javascript" src="' . $file . '"></script>';
        }

        if (!empty($this->javascript)) {
            $result[] = '<script type="text/javascript">';
            foreach ($this->javascript as $file) {
                $result[] = $file;
            }
            $result[] = '</script>';
        }

        foreach ($this->styleSheetFiles as $file) {
            $result[] = '<link rel="stylesheet" href="' . $file . '" />';
        }

        if (!empty($this->stylesheet)) {
            $result[] = '<style>';
            foreach ($this->stylesheet as $file) {
                $result[] = $file;
            }
            $result[] = '</style>';
        }

        return join("\n", $result);
    }

}
