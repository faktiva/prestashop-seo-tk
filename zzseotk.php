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
 * @author	 ZiZuu.com <info@zizuu.com>
 * @license	http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
 * @source	 https://github.com/ZiZuu-store/PrestaShop_module-CleanURLs
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
		if (!$this->isCached('meta-hreflang.tpl', $this->getCacheId()))
			$this->context->smarty->assign(array(
					'link' => $this->context->link->getModuleLink($this->name, 'display')
				));

		return $this->_displayHreflang();
	}

	private function _displayHreflang()
	{
		if (!$this->isCached('meta-hreflang.tpl', $this->getCacheId()))
		{
			$this->context->smarty->assign(array(
				'languages' => $array(
					'it' => 'it_url',
					'en' => 'en_url',
					'it-it' => 'it-it_url',
				)
			));
		}

		return $this->display(__FILE__, 'meta-hreflang.tpl', $this->getCacheId());
	}

	private function _displayCanonical()
	{
		if (!$this->isCached('meta-canonical.tpl', $this->getCacheId()))
		{
			$this->context->smarty->assign(array(
				'link' => $this->context->link->getModuleLink($this->name, 'display')
			));
		}

		return $this->display(__FILE__, 'meta-canonica.tpl', $this->getCacheId());
	}
}
