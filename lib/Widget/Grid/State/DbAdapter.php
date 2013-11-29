<?php
namespace Widget\Grid\State;

/**
 * Клас "Сохранение состояния"
 *
 * @package Widget\Grid\State
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class DbAdapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        //состояния пользователя
        $model = A1_Core::model('user/user');
        $model->setId(A1_Core_User::userExist());
        $model->selColumns(array('entity_id', 'state'));
        $user = $model->toRow();
        $state = array();
        if (!empty($user['state'])) {
            $state = unserialize($user['state']);
        }
        $this->userState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return !empty($this->userState[$this->name]) ? $this->userState[$this->name] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function setState($save)
    {
        $this->userState[$this->name] = $save;
        A1_Core::model('user/user')->save(array('entity_id' => A1_Core_User::userExist(), 'state' => serialize($this->userState)), true);

        return $this;
    }
}
