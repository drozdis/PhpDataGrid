<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния"
 * 
 * @package Widget\Grid\State
 * @author drozd
 */
class DbAdapter extends AbstractAdapter
{	
	/**
	 * @inheritdoc
	 */
	protected function _init()
	{
        #состояния пользователя
        $model = A1_Core::model('user/user');
        $model -> setId(A1_Core_User::userExist());
        $model -> selColumns(array('entity_id', 'state'));
        $user = $model -> toRow();
        $state = array();
        if (!empty($user['state'])) {
            $state = unserialize($user['state']);
        }
        $this->_userState = $state;
	}

    /**
     * @inheritdoc
     */
	public function getState() 
	{		
		return !empty($this->_userState[$this->_name]) ? $this->_userState[$this->_name] : array();
	}

    /**
     * @inheritdoc
     */
	public function setState($save) 
	{
		$this->_userState[$this->_name] = $save;
		A1_Core::model('user/user')->save(array('entity_id' => A1_Core_User::userExist(), 'state' => serialize($this->_userState)), true);
		return $this;
	}
}