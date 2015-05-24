<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 4. 24.
 * Time: 오전 2:01
 */

class UserRepository {

    /**
     * @var
     */
    private $user_model;

    public function __CONSTRUCT(User $user_model)
    {
        $this->user_model = $user_model;
    }


    /**
     * @param int $id
     * @return HwMember
     */
    public function getUser($id)
    {
        return $this->user_model->find($id);
    }
}