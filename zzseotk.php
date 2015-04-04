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
	public function __construct()
	{
		$this->name = 'zzseotk';
		$this->tab = 'seo';
		$this->version = '0.1';
		$this->author = 'ZiZuu Store';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
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

		return (parent::install()
			&& $this->registerHook('header')
		);
	}
	
	public function uninstall()
	{
		return parent::uninstall();
	}		
	
	public function hookHeader()
	{
		$out = '';
		$out = $this->_displayHreflang().
		   	$this->_displayCanonical()
			;
		return $out;
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('meta-hreflang.tpl', $this->getCacheId());
		parent::_clearCache('meta-canonical.tpl', $this->getCacheId());
	}

	private function _displayHreflang()
	{
		if (!$this->isCached('meta-hreflang.tpl', $this->getCacheId()))
		{
			$this->context->smarty->assign(array(
				'languages' => Language::getLanguages(true, (int)Shop::getContextShopId()),
				'qs' => ($p=Tools::getValue('p')) ? '?p='.$p : '',
			));
		}

		return $this->display(__FILE__, 'meta-hreflang.tpl', $this->getCacheId());
	}

	private function _displayCanonical()
	{
		if (!$this->isCached('meta-canonical.tpl', $this->getCacheId()))
		{
			$this->context->smarty->assign(array(
				'nk' => 'XXX XXX'
			));
		}

		return $this->display(__FILE__, 'meta-canonical.tpl', $this->getCacheId());
	}
}
