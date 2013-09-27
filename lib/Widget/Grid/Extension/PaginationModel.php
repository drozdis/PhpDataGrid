<?php
namespace Widget\Grid\Extension;

use Widget\AbstractWidget;
use Widget\Grid\Storage\AbstractStorage;
use Widget\Helper;
use Widget\RenderInterface;

/**
 * Class Paginator
 *
 * @package Widget\Grid\Extension
 */
class PaginationModel implements RenderInterface
{

    /**
     * Максимальное к-во на странице
     * @var String
     */
    const MAX_ON_PAGE = 200;
    
	/**
	 * @var String
	 */ 
    protected $_url = null;
    
	/**
     * Элементов на странице     
     * @var Integer
     */
    protected $_on_page = 24;
     
    /**
     * Номер текущей страници     
     * @var Integer
     */ 
    protected $_page = 1;
    
    /**
     * Страниц в диапазоне    
     * @var Integer
     */
    protected $_page_range = 5;
    
    /**
     * Страниц 
     * @var Integer
     */
    protected $_page_count = 0;
    
    /**
     * Список страниц     
     * @var Array
     */
    protected $_pages = null;

    /**
     * Список доп параметров, которые будут передаватся в шаблон   
     * @var Array
     */
    protected $_params = array();
    
    /**
     * Список доп параметров, которые будут передаватся в URL     
     * @var Array
     */
    protected $_optional = null;

    /**
     * Пареметр
     * @var String
     */
    protected $_page_key = 'page';
    
    /**
     * Суфикс к урл, например #suffix 
     * @var String
     */
    protected $_url_suffix = '';
    
    /**
     * @var \Widget\Grid\Storage\AbstractStorage
     */
    protected $_storage = null;

    /**
     * @param AbstractStorage $storage
     * @param array $options
     */
    public function __construct(AbstractStorage $storage, $options = array())
    {
    	$this->_storage = $storage;
    	Helper::setConstructorOptions($this, $options);
    }
    
	/**
	 * @return Array
	 */
	public function getOptional() 
	{
		return $this->_optional;
	}
	
	/**
	 * @param Array $_optional
	 * @return Paginator
	 */
	public function setOptional($optional) 
	{
		$this->_optional = $optional;
		return $this;
	}
	
	/**
	 * @return Array
	 */
	public function getParams() 
	{
		return $this->_params;
	}
	
	/**
	 * @param Array $_params
	 * @return Paginator
	 */
	public function setParams($params) 
	{
		$this->_params = Helper::mergeOptions($this->_params, $params, true);
		return $this;
	}

	/**
	 * К-во страниц в диапазоне
	 * @param Integer $pageRange
	 * @return Paginator
	 */
 	public function setPageRange($pageRange)
    {
    	$this->_page_range = $pageRange;
    	return $this;
    }
    
    /**
     * К-во страниц в диапазоне
     * @return Integer
     */
	public function getPageRange()
    {
    	return $this->_page_range;
    }

    /**
     * Текущий номер страницы
     * @return Integer
     */
	public function getPage()
    {
    	return $this->_normalizePageNumber($this->_page);
    }
    
    /**
     * Текущий номер страницы
     * @param Integer $page
     * @return Paginator
     */
    public function setPage($page)
    {
    	$this->_page = $this->_normalizePageNumber($page);    
    	return $this;	
    }
    
    /**
     * Базовый урл
     * @param String $url
     * @return Paginator
     */
    public function setUrl($url)
    {    	
        $this->_url = $url;
        return $this;
    }

    /**
     * Ключ в урле который отвечает за страницу (/news/page/2)
	 * @return String
	 */
	public function getPageKey() 
	{
		return $this->_page_key;
	}
    
	/**
	 * Ключ в урле который отвечает за страницу (/news/page/2)
	 * @param String $name
	 */
	public function setPageKey($name) 
	{
		$this->_page_key = $name;
	}
	
	/**
	 * @return String
	 */
	public function getUrlSuffix() 
	{
		return $this->_url_suffix;
	}
	
	/**
	 * @param String $name
	 */
	public function setUrlSuffix($suffix) 
	{
		$this->_url_suffix = $suffix;
	}
 
    /**
     * К-во элементов
     * @return integer
     */    
	public function getCount()
    {
    	if (!$this->_page_count) {
            $this->_page_count = $this->_calculatePageCount();
        }
        return $this->_page_count;
    }
    
    /**
     * К-во на странице
     * @param Integer $onPage
     * @return Paginator
     */
	public function setOnPage($onPage)
	{
		$this->_on_page = $onPage < self::MAX_ON_PAGE ? $onPage : self::MAX_ON_PAGE;
		return $this;
	}
	
	/**
	 * К-во на странице
     * @return integer
     */
    public function getOnPage()
    {
    	return $this->_on_page;
    }

	/**
     * Получение списка элементов
     * @return Array
     */
    public function getItemsByPage($pageNumber = null)
    {
        $pageNumber = $this->_normalizePageNumber($pageNumber ? $pageNumber : $this->getPage());
        $offset = ($pageNumber - 1) * $this->getOnPage();

        return $this->_storage->getItems($offset, $this->getOnPage());
    }
    
    /**
     * Формирование URL страницы с параметрами
     *
     * @param int $page
     * @return String
     */
	public function url($page)
    {
    	$url = '/';
		if ($this->_url && trim($this->_url)) {
			$url = '/'.trim($this->_url, '/').'/';
		}
		
		$params = array(array($this->_page_key => $page));
		if (!empty($this->_optional)) {		
			foreach ($this->_optional as $k => $v) {
				$params[] = array($k => $v);
			}
		}
				
		$get = null;
		foreach($params as $arr) {
			foreach($arr as $key => $values) {
				foreach ((array)$values as $value) {
					if (!is_null($get)) {
						$get .= '&amp;';
					}
					$get .= $key.'='.$value;
				}
			}
		}
		
		if (strpos($url, '?') === false) {
			$url = !empty($get) ? rtrim($url,'/').'?'.ltrim($get,'&') : rtrim($url,'/');
		} else {
			$url = !empty($get) ? rtrim($url,'/').'&'.ltrim($get,'&') : rtrim($url,'/'); 
		}
		
		return $url.($this->_url_suffix ? '#'.$this->_url_suffix : '');
    }
          
	/**
     * К-во страниц
     * @return Integer
     */
    protected function _calculatePageCount()
    {    	
        return intval(ceil($this->_storage->getCount() / $this->getOnPage()));
    }
                          
    /**
     * Creates the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return stdClass
     */
    protected function _createPages()
    {        
        $currentPageNumber = $this->getPage();
        $pageCount         = $this->_calculatePageCount();
        $pages = new \stdClass();
        $pages->pageCount        = $pageCount;
        $pages->itemCountPerPage = $this->getOnPage();
        $pages->first            = array('url' => $this->url(1), 'number' => 1);
        $pages->current          = $currentPageNumber;
        $pages->last             = array("url"=>$this->url($pageCount),"number"=>$pageCount);    
        $pages->on_page          = $this->getOnPage();   		
        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = array("url"=>$this->url($currentPageNumber - 1),"number"=>$currentPageNumber - 1);
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = array("url"=>$this->url($currentPageNumber + 1),"number"=>$currentPageNumber + 1);
        }
     
        $pages->pagesInRange = $this->_getPages();             
        foreach ($pages->pagesInRange as &$_page) {        	
        	$page['number'] = $_page;
        	$page['url'] = $this->url($_page);
        	$_page = $page;
        }
                
        $pages->firstPageInRange = min($pages->pagesInRange);
        $pages->lastPageInRange  = max($pages->pagesInRange);
			
        return $pages;
    }   

	/**
     * Returns an array of "local" pages given a page number and range.
     * 
     * @param  Zend_Paginator $paginator
     * @param  integer $pageRange (Optional) Page range
     * @return array
     */
    protected function _getPages()
    {       
        $pageNumber = $this->getPage();
        $pageCount  = $this->_calculatePageCount();
        $pageRange  = $this->getPageRange();           
        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }
        
        $delta = ceil($pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount; 
        } else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }
            
            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1; 
            $upperBound = $offset + $pageRange;
        }
                
        if ($lowerBound == 2 && $upperBound - $lowerBound == $pageRange - 1) {
        	--$upperBound;
        }
        
        if ($lowerBound == $pageCount - $pageRange && $lowerBound > 1) {
        	++$lowerBound;
        }
        
        return $this->_getPagesInRange($lowerBound, $upperBound);
    }
    
    /**
     * Returns the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return Paginator
     */
 	public function generatePages()
    {
        if ($this->_pages === null) {
            $this->_pages = $this->_createPages();
        }
        
        return $this;
    }
 
    /**
     * Returns a subset of pages within a given range.
     *
     * @param  integer $lowerBound Lower bound of the range
     * @param  integer $upperBound Upper bound of the range
     * @return array
     */
    public function _getPagesInRange($lowerBound, $upperBound)
    {
        $lowerBound = $this->_normalizePageNumber($lowerBound);
        $upperBound = $this->_normalizePageNumber($upperBound);
        
        $pages = array();
        
        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }
             
        return $pages;
    }
    
	/**
     * Brings the page number in range of the paginator.
     *
     * @param  integer $pageNumber
     * @return integer
     */
    protected function _normalizePageNumber($pageNumber)
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
     * @inheritdoc
     */
    public function render()
    {
        $this->generatePages();

        $params = $this->_params;
        foreach ($this->_pages as $k=>$v) {
            $params[$k] = $v;
        }

        $html = '';
        if (!empty($params['pageCount']) && $params['pageCount'] > 1) {
            $html .=
                '<div class="pagination pagination-centered">'.
                    '<ul>';
                    if (isset($params['previous'])) {
                        $html .= '<li><a href="'.$params['previous']['url'].'">«</a></li>';
                    } else {
                        $html .= '<li class="disabled"><a href="javascript:void(0)">«</a></li>';
                    }

                    if ($params['first']['number'] != $params['firstPageInRange']['number']) {
                        $html .= '<li class="'.($params['current'] == $params['first']['number'] ? 'active' : '').'"><a href="'.$params['first']['url'].'">'.$params['first']['number'].'</a></li>';
                    }

                    if (!in_array($params['first']['number'], array($params['firstPageInRange']['number'],$params['firstPageInRange']['number']-1))) {
                        $html .= '<li class="disabled"><a href="javascript:void(0)">...</a></li>';
                    }

                    foreach ($params['pagesInRange'] as $page) {
                        $html .= '<li class="'.($params['current'] == $page['number'] ? 'active' : '').'"><a href="'.$page['url'].'">'.$page['number'].'</a></li>';
                    }

                    if (!in_array($params['last']['number'], array($params['lastPageInRange']['number'], $params['lastPageInRange']['number']+1))) {
                        $html .= '<li class="disabled"><a href="javascript:void(0)">...</a></li>';
                    }

                    if ($params['last']['number'] != $params['lastPageInRange']['number']) {
                        $html .= '<li class="'.($params['current'] == $params['last']['number'] ? 'active' : '').'"><a href="'.$params['last']['url'].'">'.$params['last']['number'].'</a></li>';
                    }

                    if (isset($params['next'])) {
                        $html .= '<li><a href="'.$params['next']['url'].'">»</a></li>';
                    } else {
                        $html .= '<li class="disabled"><a href="javascript:void(0)">»</a></li>';
                    }
                $html .= '</ul>';
            $html .= '</div>';
        }

        return $html;
    }
}