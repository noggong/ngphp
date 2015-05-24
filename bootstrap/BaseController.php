<?php

abstract class BaseController
{

    const EOL = "\n";

    /**
     * @var App
     */
    protected $app;

    /**
     * @var
     */
    private $head_html;

    /**
     * @var
     */
    private $foot_html;

    /**
     * @var
     */
    private $og_meta;

    /**
     * @var
     */
    private $controller_title;

    /**
     * view에서 key 가 변수명으로 쓰일 변수들 목록
     */
    public $argument = array();

    /**
     * Input
     */

    protected $input;

    protected $user;

    private $is_admin_page = false;
    /**
     * @var array
     */
    private $seo_meta = array(
        'title' => '',
        'subject' => '',
        'description' => '',
        'keywords' => '',
        'copyright' => '',
        'robots' => ''
    );

    /**
     * @var 접속 기기 가 모바일인지 체크
     */
    private $is_mobile;

    /**
     * @var 접속 기기 가 모바일인지 체크
     */
    public $user_agent;
    /**
     * @var String
     */
    public $navigation_string;

    /**
     * @param Input $input
     * @param App $app
     */
    public function __CONSTRUCT(Input $input, App $app)
    {

        $this->app = $app;
        $this->input = $input;

        if (empty($this->head_html)) {
            $this->head_html = $this->app->site_config['header'];
        };

        if (empty($this->foot_html)) {
            $this->foot_html = $this->app->site_config['footer'];
        };

        $this->controller_title = $this->app->site_config['default_title'];

        /** @var bool is_mobile 모바일 체크*/
        $this->user_agent = new UserAgentHelper();
        $this->is_mobile = $this->user_agent->isMobile();

        if ($this->app->site_config['use_user']) {
            /** user Object 설정 */
            /** @var User $user */
            $user_model = new User();
            $user_repo = new UserRepository($user_model);
            $this->user = new UserHelper($user_repo);
        }
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return $this->is_mobile;
    }
    /**
     * @return UserHelper
     */
    public function getUserHelper()
    {
        return $this->user;
    }

    /**
     * @todo $get_raw 에 대한 처리 아직 안됨.
     * @param $file
     * @param $mobile_file
     * @param bool $get_raw 화면 출력이 아닌 view 를 변수로 받고자 할때.
     */
    protected function display($file = '', $mobile_file = '', $get_raw = false)
    {

        //argument 에 있는 변수는 템플릿 안에서 바로 사용 할수 있도록 함.
        extract($this->argument);

        /**
         * 모바일일때 노출되는 html파일을 정의하면 해당 파일을 로드한다.
         */
        if ($this->isMobile() && !empty($mobile_file)) {
            $file = $mobile_file;
        }
        $content_path = $this->getTemplatePath($file);
         /**
          * 웹으로 접근했지만 웹에 대한 페이지가 없으면 무조건 모바일 뷰를 전송한다.
          * 혹은 대응되는 모바일 페이지가 없으면 웹용으로 전송한다.
          */
        if (!$this->isMobile() && empty($file)) {
            $this->is_mobile = true;
        } else if ($this->isMobile() && !file_exists($content_path)) {
            $this->is_mobile = false;
        }


        if ($this->head_html) {
            $header_path = $this->getTemplatePath($this->head_html);

            if (file_exists($header_path)) {
                require $header_path;
            }
        }


        /** @var  $content_path 위의 로직에서 불러야 할 파일이 바뀌었을수 있으므로 재정의*/
        if ($this->isMobile() && !empty($mobile_file)) {
            $file = $mobile_file;
        }
        $content_path = $this->getTemplatePath($file);
        if (file_exists($content_path)) {

            require $content_path;
        }

        if ($this->foot_html) {
            $footer_path = $this->getTemplatePath($this->foot_html);
            if (file_exists($footer_path)) {require $footer_path;
            }
        }

        /**
         * 변수로 받아야 할일이 생긴다면 이곳에 작성
         */
        if ($get_raw) {

        }
    }

    /**
     * 에러페이지 출력
     * @param string $comment
     * @param string $file
     */
    protected function displayError($comment = '', $file = '')
    {
        //argument 에 있는 변수는 템플릿 안에서 바로 사용 할수 있도록 함.
        extract($this->argument);

        if ($this->head_html) {

            $header_path = $this->getTemplatePath($this->head_html);

            if (file_exists($header_path)) {
                require $header_path;
            }
        }

        $content_path = $this->getTemplatePath($file);
        if (file_exists($content_path)) {

            require $content_path;
        } else {

            require $this->getTemplatePath('include.error');
        }


        if ($this->foot_html) {
            $footer_path = $this->getTemplatePath($this->foot_html);
            if (file_exists($footer_path)) {
                require $footer_path;
            }
        }

        exit;
    }

    /**
     * 특정 페이지로 redirect 한다. 자동으로 exit가 호출되어 이 메소드 이후는 실행되지 않는다.
     *
     * @brief 특정 페이지로 redirect 한다.
     * @param string $url redirect 할 URL
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }


    /**
     * JSON형식의 response 출력
     * @param array|mixed $mixed json 으로 변환후 노출 시킬 array
     * @param string $contentType 콘텐츠 타입
     */
    protected function displayJson($mixed, $content_type = 'application/json')
    {
//        @header('Content-type: ' . $content_type);
        echo json_encode($mixed) . "\r\n";
        exit;
    }


    /**
     * BINARY resoonse 출력
     *
     * @brief 특정 페이지로 redirect 한다.
     * @param $url string redirect 할 URL
     */
    protected function displayImage($file, $ext = '')
    {
        $i = 0;
        if (!file_exists($file)) {
            $this->displayImage($file, $ext);
            if ($i == 3) {
                exit;
            }
            $i++;
        }

        $finfo = exif_imagetype($file);


        $file_size = filesize($file);

        if ($ext == "pdf") {
            $ctype = "application/pdf";
        } else if ($ext == "exe") {
            $ctype = "application/octet-stream";
        } else if ($ext == "zip") {
            $ctype = "application/zip";
        } else if ($ext == "doc") {
            $ctype = "application/msword";
        } else if ($ext == "xls") {
            $ctype = "application/vnd.ms-excel";
        } else if ($ext == "ppt") {
            $ctype = "application/vnd.ms-powerpoint";
        } else if ($ext == "gif" || $finfo == 1) {
            $ctype = "image/gif";
        } else if ($ext == "png" || $finfo == 2) {
            $ctype = "image/png";
        } else if ($ext == "jpeg" || $ext == "jpg" || $finfo == 3) {
            $ctype = "image/jpeg";
        } else {
            $ctype = "application/force-download";
        }

        @header('Content-type: ' . $ctype);
        @header('Content-Length: ' . $file_size);
        echo file_get_contents($file) . "\r\n";
        exit;
    }

    /**
     * @brief 파일 경로, 이름으로 파일을 다운로드함
     */
    protected function downloadFile($path, $name)
    {
        if (!file_exists($path)) {
            return false;
        }
        $fileSize = filesize($path);

        header('Content-type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment;filename="' . $name . '"');
        header('Connection: close');

        set_time_limit(0);

        $fd = fopen($path, 'r');

        while (!feof($fd)) {
            echo fread($fd, 1024 * 10);
            flush();
        }

        fclose($fd);
        exit;
    }


    /**
     * 특정 메시지를 altert하고 특정 페이지로 redirect 한다.
     * (자동으로 exit가 호출되어 이 메소드 이후는 실행되지 않는다.)
     *
     * @brief 특정 메시지를 altert하고 특정 페이지로 redirect 한다.
     * @param $alertMesg string alert할 메시지.
     * @param $url string redirect 할 URL.
     */
    protected function alertAndRedirect($alertMesg, $url)
    {
        $script = '<html><body>'
            . '<script type="text/javascript" language="JavaScript">' . self::EOL
            . '//<![CDATA[' . self::EOL
            . 'alert("' . str_replace(array('"', '\\'), array('\"', '\\\\'), $alertMesg) . '");' . self::EOL
            . 'location.href="' . $url . '";' . self::EOL
            . '//]]>' . self::EOL
            . '</script>' . self::EOL
            . '</body></html>';

        echo $script;

        exit;
    }


    /**
     * 특정 메시지를 altert하고 특정 페이지로 redirect 한다.
     * (자동으로 exit가 호출되어 이 메소드 이후는 실행되지 않는다.)
     *
     * @brief 특정 메시지를 altert하고 특정 페이지로 redirect 한다.
     * @param $alertMesg string alert할 메시지.
     * @param $url string redirect 할 URL.
     */
    public function alertAndScript($alertMesg, $script = '')
    {
        $script = '<script type="text/javascript" language="JavaScript">' . self::EOL
            . '//<![CDATA[' . self::EOL
            . 'alert("' . str_replace(array('"', "\n"), array('\"', '\\n'), $alertMesg) . '");' . self::EOL
            . $script . self::EOL
            . '//]]>' . self::EOL
            . '</script>' . self::EOL;

        echo $script;

        exit;
    }


    /**
     * 스크립트 실행
     * @param string $script 실행될 스크립트
     */
    protected function displayScript($script)
    {
        $script = '<script type="text/javascript" language="JavaScript">' . self::EOL
            . '//<![CDATA[' . self::EOL
            . $script . self::EOL
            . '//]]>' . self::EOL
            . '</script>' . self::EOL;

        echo $script;
    }


    /**
     * @param $path
     * @return $this
     */
    protected function setHeader($path)
    {
        $this->head_html = $path;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    protected function setFooter($path)
    {

        $this->foot_html = $path;
        return $this;
    }

    /**
     * @param $str
     * @return $this
     */
    protected function setTitle($str)
    {

        $this->controller_title = $str;
        return $this;
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaTitle($value)
    {
        $this->seo_meta['title'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return (!empty($this->seo_meta['title'])) ? $this->seo_meta['title'] : $this->app->site_config['seo_meta']['title'];
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaSubject($value)
    {
        $this->seo_meta['subject'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaSubject()
    {
        return (!empty($this->seo_meta['subject'])) ? $this->seo_meta['subject'] : $this->app->site_config['seo_meta']['subject'];
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaDescription($value)
    {
        $this->seo_meta['description'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return (!empty($this->seo_meta['description'])) ? $this->seo_meta['description'] : $this->app->site_config['seo_meta']['description'];
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaKeywords($value)
    {
        $this->seo_meta['keywords'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return (!empty($this->seo_meta['keywords'])) ? $this->seo_meta['keywords'] : $this->app->site_config['seo_meta']['keywords'];
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaCopyright($value)
    {
        $this->seo_meta['copyright'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaCopyright()
    {
        return (!empty($this->seo_meta['copyright'])) ? $this->seo_meta['copyright'] : $this->app->site_config['seo_meta']['copyright'];
    }

    /**
     * @param string $value
     * @return BaseController
     */
    public function setMetaRobots($value)
    {
        $this->seo_meta['robots'] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaRobots()
    {
        return (!empty($this->seo_meta['robots'])) ? $this->seo_meta['robots'] : $this->app->site_config['seo_meta']['robots'];
    }

    /**
     * @return string
     */
    public function title()
    {

        return $this->controller_title;
    }

    /**
     * @param $template_name
     * @param bool $ignore_mobile
     * @return string
     */
    private function getTemplatePath($template_name, $ignore_mobile = false)
    {

        if ($this->is_admin_page){
            return $this->app->home . '/view/admin/' . $this->replaceFromDotToSlash($template_name) . '.html';
        } else if ($this->isMobile() && !$ignore_mobile) {
            return $this->app->home . '/view/mobile/' . $this->replaceFromDotToSlash($template_name) . '.html';
        } else {
            return $this->app->home . '/view/web/' . $this->replaceFromDotToSlash($template_name) . '.html';
        }
    }

    /**
     * @param $template_name
     * @param $ext
     * @return string
     */
    private function getAbsolutePath($template_name, $ext)
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . $this->replaceFromDotToSlash($template_name) . '.' . $ext;
    }

    /**
     * @param $template_name
     * @param bool $ignore_mobile
     */
    public function requreTemplate($template_name, $ignore_mobile = false)
    {
        extract($this->argument);
        $file_path = $this->getTemplatePath($template_name, $ignore_mobile);
        if (file_exists($file_path)) {
            require $file_path;
        };
    }

    /**
     * @param $template_name
     */
    public function requreAbsoluteTemplate($template_name, $ext = 'html')
    {
        extract($this->argument);
        /**legacy 소스에서 필요한 변수 */
        global $logCheck, $currUrl, $depth1;
        /**
         * 추후에 없애거나 수정해야할 설정 변수
         * 기존 소스와 맞추기 위해 삽입
         */
        $file_path = $this->getAbsolutePath($template_name, $ext);

        if (file_exists($file_path)) {
            require $file_path;
        };
    }

    /**
     * @param $str
     * @return mixed
     */
    private function replaceFromDotToSlash($str)
    {
        return str_replace('.', '/', $str);
    }

    /**
     * @return string
     */
    public function getSeoMetaHtml()
    {
        $html = '';

        $title = (!empty($this->seo_meta['title'])) ? $this->seo_meta['title'] : $this->app->site_config['seo_meta']['title'];
        $subject = (!empty($this->seo_meta['subject'])) ? $this->seo_meta['subject'] : $this->app->site_config['seo_meta']['subject'];
        $description = (!empty($this->seo_meta['description'])) ? $this->seo_meta['description'] : $this->app->site_config['seo_meta']['description'];
        $keywords = (!empty($this->seo_meta['keywords'])) ? $this->seo_meta['keywords'] : $this->app->site_config['seo_meta']['keywords'];
        $copyright = (!empty($this->seo_meta['copyright'])) ? $this->seo_meta['copyright'] : $this->app->site_config['seo_meta']['copyright'];
        $robots = (!empty($this->seo_meta['robots'])) ? $this->seo_meta['robots'] : $this->app->site_config['seo_meta']['robots'];

        $html .= '<meta name="title" content="' . $title . '" />' . self::EOL;
        $html .= '<meta name="subject" content="' . $subject . '" />' . self::EOL;
        $html .= '<meta name="description" content="' . $description . '" />' . self::EOL;
        $html .= '<meta name="keywords" content="' . $keywords . '" />' . self::EOL;
        $html .= '<meta name="copyright" content="' . $copyright . '" />' . self::EOL;
        $html .= '<meta name="robots" content="' . $robots . '" />' . self::EOL;

        /** 캐쉬설정 아직 미적용 */
//		$html .= '<meta http-equiv="cache-control" content="No-Cache" />';
//		$html .= '<meta http-equiv="pragma" content="No-Cache" />';
//		$html .= '<meta http-equiv="Last-Modified" content="Wed,25 Dec 2013 14:20:00" />';
        return $html;
    }

    /**
     * @return string
     */
    public function getOgMetaHtml()
    {
        $html = '';

        if (!empty($this->og_meta['title'])) {

            $html .= '<meta property="og:title" content="' . $this->og_meta['title'] . '" />' . self::EOL;
        }
        if (!empty($this->og_meta['type'])) {

            $html .= '<meta property="og:type" content="' . $this->og_meta['type'] . '" />' . self::EOL;
        }
        if (!empty($this->og_meta['url'])) {

            $html .= '<meta property="og:url" content="' . $this->og_meta['url'] . '" />' . self::EOL;
        }
        if (!empty($this->og_meta['image'])) {

            $html .= '<meta property="og:image" content="' . $this->og_meta['image'] . '" />' . self::EOL;
        }
        if (!empty($this->og_meta['site_name'])) {

            $html .= '<meta property="og:site_name" content="' . $this->og_meta['site_name'] . '" />' . self::EOL;
        }
        if (!empty($this->og_meta['description'])) {

            $html .= '<meta property="og:description" content="' . $this->og_meta['description'] . '" />' . self::EOL;
        }

        return $html;
    }

    /**
     * @param $array
     * @return $this
     */
    public function setOgMeta($array)
    {
        $this->og_meta = $array;
        return $this;
    }

    /**
     * js 로드
     */
    public function loadJs()
    {
        $file = 'LoadJs';
        if ($this->isMobile()) {
            $file = 'Mobile' . $file;
        }
        $fp = fopen($this->app->home . '/' . $file, 'r');
        $js_src_arr = array();
        while ($src = fgets($fp)) {
            $js_src_arr[] = $src;
        }

        foreach ($js_src_arr as $src) {
            $src = str_replace(self::EOL, '', $src);


            if ($this->getSrc($src)) {
                echo '<script src="' . $src . '.js"/></script>' . self::EOL;;
            }
        }
    }

    /**
     * css 로드
     */
    public function loadCss()
    {
        $file = 'LoadCss';
        if ($this->isMobile()) {
            $file = 'Mobile' . $file;
        }
        $fp = fopen($this->app->home . '/' . $file, 'r');
        $css_src_arr = array();
        while ($src = fgets($fp)) {
            $css_src_arr[] = $src;
        }

        foreach ($css_src_arr as $src) {
            $src = str_replace(self::EOL, '', $src);
            if ($this->getSrc($src)) {
                echo '<link rel="stylesheet" href="' . $src . '.css"/>' . self::EOL;
            }
        }
    }


    protected function getSrc($src)
    {
        $src = trim($src);
        if (substr($src, 0, 1) == '#') {
            return false;
        } else if (empty($src)) {
            return false;
        }
        return $src;
    }


    /**
     * @param $util
     * @param array $models
     * @return BaseUtil
     */
    public function loadUtil($util, $models = array())
    {

        $util = str_split($util);
        $util_name = strtoupper(array_shift($util)) . implode('', $util);


        /**
         * Util initialize
         */
        $util_full_name = $util_name . 'Util';
        $util_obj = new $util_full_name($this->app, $this->user, $this);

        if (!empty($models)) {
            foreach ($models as $model_name) {

                if (class_exists($model_name)) {
                    $model = new $model_name();
                    $method_name = 'set' . $model_name . 'Model';

                    if (!method_exists($util_obj, $method_name)) {
                        error_log('Can`t find Call method For model "' . $method_name . '"');
                        exit;
                    }
                    $util_obj->$method_name($model);
                } else {
                    error_log('Can`t find Model Class : ' . $model_name);
                }
            }
        }

        return $util_obj;
    }

    /**
     * @param array $array
     */
    public function setNavigation($array = array())
    {
        $nav_arr = array();
        $nav_arr[] = '<a href="/" class="home">홈</a>';
        foreach ($array as $menu) {
            $temp_str = '';
            if (isset($menu['link']) && !empty($menu['txt'])) {
                $temp_str = '<a href="%s"><span>%s</span></a>';
                $nav_arr[] = sprintf($temp_str, $menu['link'], $menu['txt']);
            } else {
                $temp_str = '<span>%s</span>';
                $nav_arr[] = sprintf($temp_str, $menu['txt']);
            }
        }

        $this->navigation_string = implode(' &gt; ', $nav_arr);

    }

    /**
     * 동적으로 argument 추가
     * @param $key
     * @param $name
     */
    public function dynamicAddArgument($key, $name)
    {
        $this->argument[$key] = $name;
    }

    /**
     * 어드민 페이지 임을 설정
     */
    protected function setAdminPage()
    {
        $this->is_admin_page = true;
    }
}