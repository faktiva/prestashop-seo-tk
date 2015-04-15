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
		$this->description = $this->l('Handles a few basic SEO related improvements such as "hreflang" and "canonical".');

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
		$this->_html = '';

		if (Tools::isSubmit('submit_'.$this->name))
		{
			if (Configuration::updateValue('ZZSEOTK_SEO_PAGINATION_FACTOR', (int)(Tools::getValue('seo_pagination_factor')))
				&& Configuration::updateValue('ZZSEOTK_HREFLANG_ENABLED', (bool)(Tools::getValue('hreflang_enabled')))
				&& Configuration::updateValue('ZZSEOTK_CANONICAL_ENABLED', (bool)(Tools::getValue('canonical_enabled'))))
				$this->_html .= $this->displayConfirmation($this->l('All values updated'));
			else
				$this->_html .= $this->displayError($this->l('An error occurred updating values.'));
		}

		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function renderForm()
	{
		$nb = (int)Configuration::get('PS_PRODUCTS_PER_PAGE');

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->displayName,
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('Set SEO related "meta" tags into the head to handle content duplication and language issues.'),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Enable "hreflang" meta tag'),
						'name' => 'hreflang_enabled',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled'),
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enable "canonical" meta tag'),
						'name' => 'canonical_enabled',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled'),
							)
						),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Canonical pagination'),
						'name' => 'seo_pagination_factor',
						'desc' => $this->l('Select the value of items, "n", to be made "canonical".'),
						'options' => array(
							'query' => array(
								array(
									'id' => 1,
									'name' => (1 * $nb) . ' ' . $this->l('per page'),
								),
								array(
									'id' => 2,
									'name' => (2 * $nb) . ' ' . $this->l('per page'),
								),
								array(
									'id' => 5,
									'name' => (5 * $nb) . ' ' . $this->l('per page'),
								),
							),
							'id' => 'id',
							'name' => 'name',
						)
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submit_'.$this->name,
				)
			),
		);

		$helper = new HelperForm();
		$helper->fields_value['seo_pagination_factor'] = Configuration::get('ZZSEOTK_SEO_PAGINATION_FACTOR');
		$helper->fields_value['hreflang_enabled'] = Configuration::get('ZZSEOTK_HREFLANG_ENABLED');
		$helper->fields_value['canonical_enabled'] = Configuration::get('ZZSEOTK_CANONICAL_ENABLED');

		return $helper->generateForm(array($fields_form));
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

		foreach (Shop::getShops(true, null, true) as $shop_id)
		{
			$shop_context = $this->context->cloneContext();
			$shop_context->shop = new Shop((int)$shop_id);
			$shops_data[$shop_id] = array(
				'context' => $shop_context,
				'languages' => Language::getLanguages(true, $shop_id),
			);
		}
		unset($shop_context);

		$smarty->assign(array(
			'qs' => $qs,
			'shops_data' => $shops_data,
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
