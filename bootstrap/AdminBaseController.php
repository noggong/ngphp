<?php

/**
 * 관리자 컨트롤러
 * Class AdminBaseController
 */
abstract class AdminBaseController extends BaseController{

	protected $page_keyword;
	protected $page_title;
	protected $page_desc;
	protected $page_sub_title;
	protected $form_value;
	/**
	 * @param Input $input
	 * @param App $app
	 */
	public function __CONSTRUCT(Input $input, App $app)
	{

		/** 어드민에서는 global header 와 global footer 가 다르다. */
		$this->setHeader('include.head')->setFooter('include.tail');

		$this->setAdminPage();
		parent::__CONSTRUCT($input, $app);

		/** 어드민 user 체크 */
		if (!$this->checkAdminUser()) {
			header('Location: /admin/');
		};
	}


	/**
	 * 어드민 로그인 체크
	 * @return bool
	 */
	private function checkAdminUser()
	{
		if ($this->user->isAdmin() === false && !$this->user->isCompany()) {
			return false;
		}

		return true;
	}

	/**
	 * js 로드
	 */
	public function loadJs()
	{
		$fp = fopen($this->app->home . '/AdminLoadJs', 'r');
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
		$fp = fopen($this->app->home . '/AdminLoadCss', 'r');
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

	/**
	 * @return string
	 */
	public function getSeoMetaHtml()
	{
		$html = '';

		if (!empty($this->app->site_config['seo_meta']['title'])) {
			$html .= '<meta name="title" content="' . $this->app->site_config['seo_meta']['title'] . '" />' . "\n";
		}
		if (!empty($this->app->site_config['seo_meta']['subject'])) {
			$html .= '<meta name="subject" content="' . $this->app->site_config['seo_meta']['subject'] . '" />' . "\n";
		}
		if (!empty($this->app->site_config['seo_meta']['description'])) {
			$html .= '<meta name="description" content="' . $this->app->site_config['seo_meta']['description'] . '" />' . "\n";
		}
		if (!empty($this->app->site_config['seo_meta']['keywords'])) {
			$html .= '<meta name="keywords" content="' . $this->app->site_config['seo_meta']['keywords'] . '" />' . "\n";
		}
		if (!empty($this->app->site_config['seo_meta']['copyright'])) {
			$html .= '<meta name="copyright" content="' . $this->app->site_config['seo_meta']['copyright'] . '" />' . "\n";
		}

		$html .= '<meta name="robots" content="noindex" />' . "\n";

		/** 캐쉬설정 아직 미적용 */
//		$html .= '<meta http-equiv="cache-control" content="No-Cache" />';
//		$html .= '<meta http-equiv="pragma" content="No-Cache" />';
//		$html .= '<meta http-equiv="Last-Modified" content="Wed,25 Dec 2013 14:20:00" />';
		return $html;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function getFormValue($key)
	{
        if (empty($this->form_value)) {
            return '';
        }

        if (empty($this->form_value[$key])) {
            return '';
		}
		return $this->form_value[$key];
	}

}