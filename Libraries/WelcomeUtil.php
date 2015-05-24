<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 4. 21.
 * Time: 오전 10:55
 */

class WelcomeUtil extends BaseUtil{

    /** @var  qna $qna_model */
    private $qna_model;

    /**
     * @param Qna $qna
     */
    public function setQnaModel(Qna $qna)
    {
        /** @var Qna qna_model */
        $this->qna_model = $qna;

    }
}