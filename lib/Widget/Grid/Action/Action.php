<?php
namespace Widget\Grid\Action;
use Widget\AbstractWidget;
use Widget\Helper;

/**
 * Grid action
 *
 * @package Widget\Grid\Action
 * @author drozd
 */
class Action extends AbstractWidget
{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_href = '';

    /**
     * @var string
     */
    protected $_icon = '';

    /**
     * @var \Widget\Grid\Grid
     */
    protected $_grid = null;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        Helper::setConstructorOptions($this, $options);
    }

    /**
     * @param $action
     * @return object|Action
     * @throws \Exception
     */
    public static function factory($action, $name)
    {
        if (is_array($action)) {
            if (!empty($action['class']) && class_exists($action['class'])) {
                $class = $action['class'];
            } else {
                $class = '\Widget\Grid\Action';
            }
            $action = new $class($action);

        } elseif (is_string($action)) {
            $action = new Action(array('title' => $action));
        } elseif (is_object($action)) {

        } else {
            throw new \Exception('Invalid action configuration');
        }

        if (!($action instanceof  \Widget\Grid\Action\Action)) {
            throw new \Exception('Class of action should be instanced from  \Widget\Grid\Action\Action');
        }

        $action->setName($name);
        return $action;
    }

    /**
     * @param \Widget\Grid\Grid $grid
     */
    public function setGrid($grid)
    {
        $this->_grid = $grid;
    }

    /**
     * @return \Widget\Grid\Grid
     */
    public function getGrid()
    {
        return $this->_grid;
    }

    /**
     * @param string $href
     * @return Action
     */
    public function setHref($href)
    {
        $this->_href = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getHref($row = null)
    {
        if ($row == null) {
            return $this->_href;
        }

        $href = $this->_href;
        if (!$href) {
            $arr = explode('?', str_replace('/view', '', $this->getGrid()->getBaseUrl()));
            $arr[0] = rtrim($arr[0],'/').'/'.$this->getName().'/id/'.Helper::getValue($row, $this->getGrid()->getStorage()->getIdField());
            $href = join('?', $arr);
        } else {
            if (preg_match_all('#{{([\d\w_]+)}}#', $href, $m)) {
                foreach ($m[1] as $key) {
                    $href = str_replace('{{'.$key.'}}', Helper::getValue($row, $key), $href);
                }
            }
        }
        $href = $href.(strpos($href, '?') === false ? '?' : '&').'return='.urlencode($this->getGrid()->getUrl());
        return $href;
    }

    /**
     * @param string $icon
     * @return Action
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_icon ? $this->_icon : $this->getName();
    }

    /**
     * @param string $title
     * @return Action
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }


    /**
     * object|array $row
     * @return Action
     */
    public function setCurrentRow($row)
    {
        $this->_row = $row;
        return $this;
    }

    /**
     * @return object|array
     */
    public function getCurrentRow()
    {
        return $this->_row;
    }

    /**
     * @inheritdoc
     */
    public function _initialHtml()
    {
        return '<a rel="nofollow" class="btn btn-mini btn-warning" data-role="tooltip" data-placement="top" title="'.$this->getTitle().'" href="'.$this->getHref($this->getCurrentRow()).'"><i class="icon-white icon-'.$this->getIcon().'"></i></a>';
    }
}
