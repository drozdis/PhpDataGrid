<?php
namespace Widget\Grid\Extension;

use Widget\AbstractRenderer;
use Widget\Grid\Storage\AbstractStorage;
use Widget\Helper;

/**
 * Class Paginator
 *
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class PaginationRenderer extends AbstractRenderer
{
    /**
     * Максимальное к-во на странице
     *
     * @var string
     */
    const MAX_ON_PAGE = 200;

    /**
     * @var string
     */
    protected $url = null;

    /**
     * Элементов на странице
     *
     * @var Integer
     */
    protected $onPage = 20;

    /**
     * @var array
     */
    protected $onPageList = array(20, 50, 100, 200);

    /**
     * Номер текущей страници
     *
     * @var Integer
     */
    protected $page = 1;

    /**
     * Страниц в диапазоне
     *
     * @var Integer
     */
    protected $pageRange = 5;

    /**
     * Страниц
     *
     * @var Integer
     */
    protected $pageCount = 0;

    /**
     * Список страниц
     *
     * @var array
     */
    protected $pages = null;

    /**
     * Список доп параметров, которые будут передаватся в шаблон
     *
     * @var array
     */
    protected $params = array();

    /**
     * Пареметр
     *
     * @var string
     */
    protected $pageKey = 'page';

    /**
     * @var \Widget\Grid\Storage\AbstractStorage
     */
    protected $storage = null;

    /**
     * @param AbstractStorage $storage
     * @param array           $options
     */
    public function __construct(AbstractStorage $storage, $options = array())
    {
        $this->storage = $storage;
        Helper::setConstructorOptions($this, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'Extension/pagination.html.twig';
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = Helper::mergeOptions($this->params, $params, true);

        return $this;
    }

    /**
     * К-во страниц в диапазоне
     *
     * @param Integer $pageRange
     *
     * @return $this
     */
    public function setPageRange($pageRange)
    {
        $this->pageRange = $pageRange;

        return $this;
    }

    /**
     * Текущий номер страницы
     *
     * @return Integer
     */
    public function getPage()
    {
        return $this->normalizePageNumber($this->page);
    }

    /**
     * Текущий номер страницы
     *
     * @param Integer $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $this->normalizePageNumber($page);

        return $this;
    }

    /**
     * Базовый урл
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->url;
    }

    /**
     * Ключ в урле который отвечает за страницу (/news/page/2)
     *
     * @param string $name
     */
    public function setPageKey($name)
    {
        $this->pageKey = $name;
    }

    /**
     * К-во элементов
     *
     * @return integer
     */
    public function getCount()
    {
        if (!$this->pageCount) {
            $this->pageCount = $this->calculatePageCount();
        }

        return $this->pageCount;
    }

    /**
     * К-во на странице
     *
     * @param Integer $onPage
     *
     * @return $this
     */
    public function setOnPage($onPage)
    {
        $this->onPage = $onPage < self::MAX_ON_PAGE ? $onPage : self::MAX_ON_PAGE;

        return $this;
    }

    /**
     * К-во на странице
     *
     * @return integer
     */
    public function getOnPage()
    {
        return $this->onPage;
    }

    /**
     * @param array $onPageList
     *
     * @return $this
     */
    public function setOnPageList($onPageList)
    {
        $this->onPageList = $onPageList;

        return $this;
    }

    /**
     * @return array
     */
    public function getOnPageList()
    {
        return $this->onPageList;
    }

    /**
     * Получение списка элементов
     *
     * @param integer $pageNumber
     *
     * @return array
     */
    public function getItemsByPage($pageNumber = null)
    {
        $pageNumber = $this->normalizePageNumber($pageNumber ? $pageNumber : $this->getPage());
        $offset     = ($pageNumber - 1) * $this->getOnPage();

        return $this->storage->getItems($offset, $this->getOnPage());
    }

    /**
     * Формирование URL страницы с параметрами
     *
     * @param int $page
     *
     * @return string
     */
    public function url($page)
    {
        if (preg_match('#^http#', $this->url)) {
            $url = trim($this->url, '/') . '/';
        } else {
            $url = '/' . trim($this->url, '/') . '/';
        }

        $params = array(array($this->pageKey => $page));

        $get = null;
        foreach ($params as $arr) {
            foreach ($arr as $key => $values) {
                foreach ((array) $values as $value) {
                    if (!is_null($get)) {
                        $get .= '&amp;';
                    }
                    $get .= $key . '=' . $value;
                }
            }
        }

        if (strpos($url, '?') === false) {
            $url = !empty($get) ? rtrim($url, '/') . '?' . ltrim($get, '&') : rtrim($url, '/');
        } else {
            $url = !empty($get) ? rtrim($url, '/') . '&' . ltrim($get, '&') : rtrim($url, '/');
        }

        return $url;
    }

    /**
     * К-во страниц
     *
     * @return Integer
     */
    protected function calculatePageCount()
    {
        return intval(ceil($this->storage->getCount() / $this->getOnPage()));
    }

    /**
     * Creates the page collection.
     *
     * @return stdClass
     */
    protected function createPages()
    {
        $currentPageNumber       = $this->getPage();
        $pageCount               = $this->calculatePageCount();
        $pages                   = new \stdClass();
        $pages->pageCount        = $pageCount;
        $pages->itemCountPerPage = $this->getOnPage();
        $pages->first            = array('url' => $this->url(1), 'number' => 1);
        $pages->current          = $currentPageNumber;
        $pages->last             = array("url" => $this->url($pageCount), "number" => $pageCount);
        $pages->onPage           = $this->getOnPage();
        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = array("url" => $this->url($currentPageNumber - 1), "number" => $currentPageNumber - 1);
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = array("url" => $this->url($currentPageNumber + 1), "number" => $currentPageNumber + 1);
        }

        $pages->pagesInRange = $this->getPages();
        foreach ($pages->pagesInRange as &$onePage) {
            $page['number'] = $onePage;
            $page['url']    = $this->url($onePage);
            $onePage        = $page;
        }

        $pages->firstPageInRange = min($pages->pagesInRange);
        $pages->lastPageInRange  = max($pages->pagesInRange);

        return $pages;
    }

    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @return array
     */
    protected function getPages()
    {
        $pageNumber = $this->getPage();
        $pageCount  = $this->calculatePageCount();
        if ($this->pageRange > $pageCount) {
            $this->pageRange = $pageCount;
        }

        $delta = ceil($this->pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $this->pageRange) {
            $lowerBound = $pageCount - $this->pageRange + 1;
            $upperBound = $pageCount;
        } else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $this->pageRange;
        }

        if ($lowerBound == 2 && $upperBound - $lowerBound == $this->pageRange - 1) {
            --$upperBound;
        }

        if ($lowerBound == $pageCount - $this->pageRange && $lowerBound > 1) {
            ++$lowerBound;
        }

        return $this->getPagesInRange($lowerBound, $upperBound);
    }

    /**
     * Returns the page collection.
     *
     * @return $this
     */
    public function generatePages()
    {
        if ($this->pages === null) {
            $this->pages = $this->createPages();
        }

        return $this;
    }

    /**
     * Returns a subset of pages within a given range.
     *
     * @param integer $lowerBound Lower bound of the range
     * @param integer $upperBound Upper bound of the range
     *
     * @return array
     */
    public function getPagesInRange($lowerBound, $upperBound)
    {
        $lowerBound = $this->normalizePageNumber($lowerBound);
        $upperBound = $this->normalizePageNumber($upperBound);

        $pages = array();

        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        return $pages;
    }

    /**
     * Brings the page number in range of the paginator.
     *
     * @param integer $pageNumber
     *
     * @return integer
     */
    protected function normalizePageNumber($pageNumber)
    {
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        $pageCount = $this->getCount();
        if ($pageCount > 0 && $pageNumber > $pageCount) {
            $pageNumber = $pageCount;
        }

        return $pageNumber;
    }

    /**
     * @return string
     */
    protected function initialHtml()
    {
        $this->generatePages();

        $params = $this->params;
        foreach ($this->pages as $k => $v) {
            $params[$k] = $v;
        }

        return $this->getRendererEngine()->render($this->getTemplate(), $params + array('element' => $this));
    }
}
