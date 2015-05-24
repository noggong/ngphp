<?php

class WelcomeController extends BaseController
{


    public function getMain()
    {

        /** @var QnaUtil $qna_util */
//        $qna_util = $this->loadUtil('Qna', array('Qna'));

        $this->display('main');
    }


}
