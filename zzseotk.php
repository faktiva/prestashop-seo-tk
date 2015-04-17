<?php

/**
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * DISCLAIMER
 * This code is provided as is without any warranty.
 * No promise of being safe or secure
 *
 * @author   ZiZuu.com <info@zizuu.com>
 * @license  http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
 * @link     source available at https://github.com/ZiZuu-store/
 */

if (!defined('_PS_VERSION_'))
	exit;
	
class zzSEOtk extends Module
{
	private $_controller;

	public function __construct()
	{
		$this->name = 'zzseotk';
		$this->author = 'ZiZuu Store';
		$this->tab = 'seo';
		$this->version = '0.1';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('ZiZuu SEO ToolKit');
		$this->description = $this->l('Handles a few basic SEO related improvements such as \'hreflang\' and \'canonical\'.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall "ZiZuu SEO ToolKit"?');
	}
		
	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() 
			&& $this->registerHook('header')
			&& Configuration::updateValue('ZZSEOTK_HREFLANG_ENABLED', true)
			&& Configuration::updateValue('ZZSEOTK_CANONICAL_ENABLED', false)
			&& Configuration::updateValue('ZZSEOTK_SEO_PAGINATION_FACTOR', 5) /* Let index only one "N" value among allowed (1, 2, 5). @see FrontController */
		;
	}
	
	public function uninstall()
	{
		return parent::uninstall() 
			&& Configuration::deleteByName('ZZSEOTK_HREFLANG_ENABLED')
			&& Configuration::deleteByName('ZZSEOTK_CANONICAL_ENABLED')
			&& Configuration::deleteByName('ZZSEOTK_SEO_PAGINATION_FACTOR')
		;
	}		
	
	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('meta-hreflang.tpl', $this->getCacheId($cache_id));
		parent::_clearCache('meta-canonical.tpl', $this->getCacheId($cache_id));
	}

	public function getContent()
	{
		$_html = '<div id="'.$this->name.'_config_intro" class="alert alert-info">'
			. '  <span class="module_name">'.$this->displayName.'</span>'
			. '  <div class="module_description">'.$this->description.'</div>'
			. '</div>';

		if (Tools::isSubmit('submitOptionsconfiguration'))
		{
			if (Tools::getValue('ZZSEOTK_HREFLANG_ENABLED'))
				Configuration::updateValue('ZZSEOTK_HREFLANG_ENABLED', (bool)Tools::getValue('ZZSEOTK_HREFLANG_ENABLED'));

			if (Tools::getValue('ZZSEOTK_CANONICAL_ENABLED'))
				Configuration::updateValue('ZZSEOTK_CANONICAL_ENABLED', (bool)Tools::getValue('ZZSEOTK_CANONICAL_ENABLED'));

			if (Tools::getValue('ZZSEOTK_SEO_PAGINATION_FACTOR'))
				Configuration::updateValue('ZZSEOTK_SEO_PAGINATION_FACTOR', (int)Tools::getValue('ZZSEOTK_SEO_PAGINATION_FACTOR'));
		}
	
		$_html .= $this->renderForm();

		return $_html;
	}

	public function renderForm()
	{
		$nb = (int)Configuration::get('PS_PRODUCTS_PER_PAGE');

		$this->fields_option = array(
			'hreflang' => array(
				'title' => $this->l('Internationalization'),
				'icon' => 'icon-flag',
				'fields' => array(
					'ZZSEOTK_HREFLANG_ENABLED' => array(
						'title' => $this->l('Enable "hreflang" meta tag'),
						'hint' => $this->l('Set "alternate / hreflang" meta tag into the head to handle the same content in different languages.'),
						'validation' => 'isBool',
						'cast' => 'boolval',
						'type' => 'bool',
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
			'canonical' => array(
				'title' => $this->l('Canonical URL'),
				'icon' => 'icon-link',
				'fields' => array(
					'ZZSEOTK_CANONICAL_ENABLED' => array(
						'title' => $this->l('Enable "canonical" meta tag'),
						'hint' => $this->l('Set "alternate / canonical" meta tag into the head to avoid content duplication issues in SEO.'),
						'validation' => 'isBool',
						'cast' => 'boolval',
						'type' => 'bool',
					),
					'ZZSEOTK_SEO_PAGINATION_FACTOR' => array(
						'title' => $this->l('Canonical pagination'),
						'hint' => $this->l('Select the value of items ("n") in pagination to be used as "canonical".'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'select',
						'list' => array(
							array(
								'value' => 1,
								'name' => $this->l('n = ') . $nb,
							),
							array(
								'value' => 2,
								'name' => $this->l('n = ') . (2 * $nb),
							),
							array(
								'value' => 5,
								'name' => $this->l('n = ') . (5 * $nb),
							),
						),
						'identifier' => 'value',
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);

        $helper = new HelperOptions($this);
        $helper->id = $this->id;
		$helper->module = $this;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->title = $this->displayName;

		return $helper->generateOptions($this->fields_option);
	}

	public function hookHeader()
	{
		$this->_controller = Dispatcher::getInstance()->getController();
		if (!empty(Context::getContext()->controller->php_self))
			$this->_controller = Context::getContext()->controller->php_self;

		$out = "\n"
			. $this->_displayHreflang()
			. $this->_displayCanonical();

		return $out;
	}

	private function _displayHreflang()
	{
		if (!Configuration::get('ZZSEOTK_HREFLANG_ENABLED'))
			return;

		$smarty = $this->context->smarty;
		if ('404'==$this->_controller)
		{
			$smarty->assign('nobots', true);
			return;
		}

		// horrible hack: Link::getLanguageLink() seems to return a QS only on some cases
		$qs = empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING'];

		foreach (Shop::getShopsCollection(true) as $shop)
		{
			$shop_context = $this->context->cloneContext();
			$shop_context->shop = $shop;
			$shops_data[$shop->id] = array(
				'context' => $shop_context,
				'languages' => Language::getLanguages(true, $shop->id),
			);
		}
		unset($shop_context);

		$smarty->assign(array(
			'qs' => $qs,
			'shops_data' => $shops_data,
			'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
			'default_shop' => (int)Configuration::get('PS_SHOP_DEFAULT'),
		));

		return $this->display(__FILE__, 'meta-hreflang.tpl');
	}

	private function _displayCanonical()
	{
		if (!Configuration::get('ZZSEOTK_CANONICAL_ENABLED'))
			return;

		$controller = $this->_controller;
		$link = $this->context->link;
		$smarty = $this->context->smarty;

		$nb = (int)Configuration::get('ZZSEOTK_SEO_PAGINATION_FACTOR') * max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));

		$p = (int)Tools::getValue('p', 1);
		$n = (int)Tools::getValue('n', $this->context->cookie->nb_item_per_page);
		
		if ($n!=$nb && $p>1)
			$smarty->assign(array('nobots' => true));
		else
		{
			$params['n'] = $nb;

			$qs = '?'.http_build_query($params, '', '&');

			switch ($controller)
			{
			case 'product':
			case 'cms':
				$qs = '';
			case 'category':
			case 'supplier':
			case 'manufacturer':
				if ($id=(int)Tools::getValue('id_'.$controller))
				{
					$getLinkFunc = 'get'.ucfirst($controller).'Link';
					$canonical = call_user_func(array($link, $getLinkFunc), $id);
				}
				break;

			case 'index':
				$qs = '';
			case 'search':
			case 'prices-drop':
				$canonical = $link->getPageLink($controller);
				$query = array();
				if ($tag = Tools::getValue('tag', ''))
					$query['tag'] = $tag;
				if ($sq = Tools::getValue('search_query', ''))
					$query['search_query'] = $sq;
				if (count($query)>0)
					$qs .=  '?'.http_build_query($query, '' , '&');
				break;

			default:
				//$canonical .= "TEST '$controller'";
				break;
			}

			if ($canonical)
			{
				$url = $canonical.$qs;
				if (!$this->isCached('meta-canonical.tpl', $this->getCacheId($url)))
				{
					$smarty->assign(array(
						'canonical_url' => $url,
					));
				}
			}
		}

		return $this->display(__FILE__, 'meta-canonical.tpl', $this->getCacheId($url));
	}

}
