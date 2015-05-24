<?php

/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 4. 19.
 * Time: 오후 9:54
 */
class Input
{

    var $argument = array();

    public function __CONSTRUCT()
    {

    }

    /**
     * @param $key
     * @param string $filter (기본 : addslashes, int: '숫자형인지 검사', array: '배열인지 검사',mail: '메일 유효성 검사',regx: '정규표현식으로 검사',url : url인지 검사,addslashes: 'addslashes',raw: '아무것도 안함')
     * @param null $default 없을때 기본값
     * @param bool $flag
     * @param bool $options
     * @return mixed|null
     */
    public function get($key, $filter = '', $default = null, $flag = false, $options = false)
    {

        if (empty($filter)) {
            $value = $this->striptagFilter($key);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }

        if ($filter == 'int') {
            $value = $this->intFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }

        if ($filter == 'array') {
            $value = $this->arrayFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else {
                return array();
            }
        }

        if ($filter == 'mail') {
            $value = $this->emailFilter($key);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }


        if ($filter == 'regx') {
            $value = $this->regxFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }

        if ($filter == 'url') {
            $value = $this->urlFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }

        if ($filter == 'addslashes') {
            $value = $this->addslashesFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }

        if ($filter == 'raw') {
            $value = $this->noFilter($key, $options);
            if (!empty($value)) {
                $this->argument[$key] = $value;
                return $value;
            } else if ($default) {
                $this->argument[$key] = $default;
                return $default;
            } else {
                return '';
            }
        }
    }

    /**
     * $_FILES 가져오기
     * @param string $key
     * @return array
     */
    public function file($key)
    {
        if (!empty($_FILES[$key])) {
            $this->argument[$key] = $_FILES[$key];
            return $_FILES[$key];
        } else {
            return array(
                'error' => 4,
                'msg' => 'Didn`t choice File'
            );
        }

    }

    /**
     * @param $key
     * @return string
     */
    private function noFilter($key)
    {
        if ($value = filter_input(INPUT_GET, $key)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key)) {
            return $value;
        } else {
            return '';
        }

    }

    /**
     * @param $key
     * @param $options
     * @return mixed|string
     */
    private function intFilter($key, $options)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT, $options)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT, $options)) {

            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @return array
     */
    private function arrayFilter($key)
    {
        if (isset($_GET[$key]) && !empty($_GET[$key])) {

            return $_GET[$key];

        } else if (isset($_POST[$key])) {

            return $_POST[$key];
        } else {
            return array();
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function emailFilter($key)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_EMAIL)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_EMAIL)) {

            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @param string $options 정규표현식
     * @return mixed|string
     */
    private function regxFilter($key, $options)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_EMAIL, array('regexp' => $options))) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_EMAIL, array('regexp' => $options))) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function urlFilter($key)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_URL)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_URL)) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function addslashesFilter($key)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_MAGIC_QUOTES)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_MAGIC_QUOTES)) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function striptagFilter($key)
    {
        if ($value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS)) {

            return $value;

        } else if ($value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS)) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param array $item
     * @return bool
     */
    public function checkRequiredItem(array $item)
    {
        foreach ($item as $key) {
            if (!array_key_exists($key, $this->argument) || empty($this->argument[$key])) {
                error_log($key);
                return false;
            }
        }
        return true;
    }
}