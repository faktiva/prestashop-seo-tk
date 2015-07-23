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

if (!defined('_PS_VERSION_')) {
    exit;
}
    
class zzseotk extends Module
{
    private $_controller;

    private $_paginating_controllers = array(
        'best-sales',
        'category',
        'manufacturer',
        'manufacturer-list',
        'new-products',
        'prices-drop',
        'search',
        'supplier',
        'supplier-list',
    );

    private $_nobots_controllers = array(
        '404',
        'address',
        'addresses',
        'attachment',
        'authentication',
        'cart',
        'discount',
        'footer',
        'get-file',
        'guest-tracking',
        'header',
        'history',
        'identity',
        'images.inc',
        'init',
        'my-account',
        'order',
        'order-opc',
        'order-slip',
        'order-detail',
        'order-follow',
        'order-return',
        'order-confirmation',
        'pagination',
        'password',
        'pdf-invoice',
        'pdf-order-return',
        'pdf-order-slip',
        'product-sort',
        'search',
        'statistics',
    );

    public function __construct()
    {
        $this->name = 'zzseotk';
        $this->author = 'ZiZuu Store';
        $this->tab = 'seo';
        $this->version = '1.1.1';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6.0.9', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ZiZuu SEO ToolKit');
        $this->description = $this->l('Handles a few SEO related improvements, such as \'hreflang\', \'canonical\' and \'noindex\'.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall "ZiZuu SEO ToolKit"?');
    }
        
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook('header')
            && Configuration::updateValue('ZZSEOTK_HREFLANG_ENABLED', true)
            && Configuration::updateValue('ZZSEOTK_CANONICAL_ENABLED', false)
            && Configuration::updateValue('ZZSEOTK_NOBOTS_ENABLED', true)
        ;
    }
    
    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('ZZSEOTK_HREFLANG_ENABLED')
            && Configuration::deleteByName('ZZSEOTK_CANONICAL_ENABLED')
            && Configuration::deleteByName('ZZSEOTK_NOBOTS_ENABLED')
        ;
    }

    //FIXME	
    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache('meta-hreflang.tpl', $this->getCacheId($cache_id));
        parent::_clearCache('meta-canonical.tpl', $this->getCacheId($cache_id));
    }

    public function getContent()
    {
        $_html = '<div id="'.$this->name.'_config_intro" class="alert alert-info">'.
            '  <span class="module_name">'.$this->displayName.'</span>'.
            '  <div class="module_description">'.$this->description.'</div>'.
            '</div>';

        if (Tools::isSubmit('submitOptionsconfiguration')) {
            if (Tools::getValue('ZZSEOTK_HREFLANG_ENABLED')) {
                Configuration::updateValue('ZZSEOTK_HREFLANG_ENABLED', (bool)Tools::getValue('ZZSEOTK_HREFLANG_ENABLED'));
            }

            if (Tools::getValue('ZZSEOTK_CANONICAL_ENABLED')) {
                Configuration::updateValue('ZZSEOTK_CANONICAL_ENABLED', (bool)Tools::getValue('ZZSEOTK_CANONICAL_ENABLED'));
            }

            if (Tools::getValue('ZZSEOTK_NOBOTS_ENABLED')) {
                Configuration::updateValue('ZZSEOTK_NOBOTS_ENABLED', (bool)Tools::getValue('ZZSEOTK_NOBOTS_ENABLED'));
            }
        }
    
        $_html .= $this->renderForm();

        return $_html;
    }

    public function renderForm()
    {
        $this->fields_option = array(
            'hreflang' => array(
                'title' => $this->l('Internationalization'),
                'icon' => 'icon-flag',
                'fields' => array(
                    'ZZSEOTK_HREFLANG_ENABLED' => array(
                        'title' => $this->l('Enable "hreflang" meta tag'),
                        'hint' => $this->l('Set "hreflang" meta tag into the html head to handle the same content in different languages.'),
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
                        'hint' => $this->l('Set "canonical"meta tag into the html head to avoid content duplication issues in SEO.'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            'nobots' => array(
                'title' => $this->l('noindex,nofollow'),
                'icon' => 'icon-sitemap',
                'fields' => array(
                    'ZZSEOTK_NOBOTS_ENABLED' => array(
                        'title' => $this->l('Enable "noindex,nofollow" meta tag'),
                        'hint' => $this->l('Set "noindex,nofollow" meta tag into the html head to avoid search engine indicization of "private" pages. Public pages are not affected of course.'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
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
        if (!empty(Context::getContext()->controller->php_self)) {
            $this->_controller = Context::getContext()->controller->php_self;
        }

        if ($this->_handleNobots()) {
            // no need to add anything else as robots should ignore this page
            return;
        }

        $out = "\n"
            .$this->_displayHreflang()
            .$this->_displayCanonical()
        ;

        return $out;
    }

    private function _handleNobots()
    {
        if (Configuration::get('ZZSEOTK_NOBOTS_ENABLED')) {
            if (in_array($this->_controller, $this->_nobots_controllers)) {
                $this->context->smarty->assign('nobots', true);
                return true;
            }
        }
        return false;
    }

    private function _displayHreflang()
    {
        if (!Configuration::get('ZZSEOTK_HREFLANG_ENABLED')) {
            return;
        }

        $shop = Context::getContext()->shop;
        $proto = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://';
        $uri = ('index' == $this->_controller) ? '' : $_SERVER['REQUEST_URI'];
        $requested_URL = $proto.$shop->domain.$uri;
        //TODO PS1.6.1.0 $requested_URL = $shop->getBaseURL(true /* $auto_secure_mode */, false /* $add_base_uri */).$uri;
        if (Configuration::get('ZZSEOTK_CANONICAL_ENABLED')
            && strtok($requested_URL, '?') != $this->_getCanonicalLink(null, null, false)
        ) {
            return;
        }

        parse_str($_SERVER['QUERY_STRING'], $params);
        $qs = empty($_SERVER['QUERY_STRING']) ? '' : '?'.http_build_query($params, '', '&');
        foreach (Shop::getShops(true /* $active */, null /* $id_shop_group */, true /* $get_as_list_id */) as $shop_id) {
            foreach (Language::getLanguages(true /* $active */, $shop_id) as $language) {
                $url = $this->_getCanonicalLink($language['id_lang'], $shop_id, false /* $has_qs */).$qs;
                $shops_data[$shop_id][] = array(
                    'url' => $url,
                    'language' => array(
                        'id' => $language['id_lang'],
                        'code' => $language['language_code'],
                    ),
                );
            }
        }

        $this->context->smarty->assign(array(
            'shops_data' => $shops_data,
            'default_lang_id' => (int)Configuration::get('PS_LANG_DEFAULT'),
            'default_shop_id' => (int)Configuration::get('PS_SHOP_DEFAULT'),
        ));

        return $this->display(__FILE__, 'meta-hreflang.tpl');
    }

    private function _displayCanonical()
    {
        if (!Configuration::get('ZZSEOTK_CANONICAL_ENABLED')) {
            return;
        }


        $canonical = $this->_getCanonicalLink();

        if (!$this->isCached('meta-canonical.tpl', $this->getCacheId($canonical))) {
            $this->context->smarty->assign(array(
                'canonical_url' => $canonical,
            ));
        }

        return $this->display(__FILE__, 'meta-canonical.tpl', $this->getCacheId($canonical));
    }

    private function _getCanonicalLink($id_lang = null, $id_shop = null, $add_qs = true)
    {
        $link = $this->context->link;
        $controller = $this->_controller;
        $id = (int)Tools::getValue('id_'.$controller);
        $getLinkFunc = 'get'.ucfirst($controller).'Link';
        $params = array();

        if (!$link || !$controller) {
            return;
        }

        switch ($controller) {
            case 'product':
                // getProductLink($product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null, $ipa = 0, $force_routes = false, $relative_protocol = false)
                $canonical = $link->getProductLink($id, null, null, null, $id_lang, $id_shop);
                break;
            case 'category':
                // getCategoryLink($category, $alias = null, $id_lang = null, $selected_filters = null, $id_shop = null, $relative_protocol = false)
                $canonical = $link->getCategoryLink($id, null, $id_lang, Tools::getValue('selected_filters', null), $id_shop);
                break;
            case 'cms':
                // getCMSLink($cms, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                $canonical = $link->getCmsLink($id, null, null, $id_lang, $id_shop);
                break;

            case 'cms-category':
                // getCMSCategoryLink($cms_category, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
            case 'supplier':
                // getSupplierLink($supplier, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
            case 'manufacturer':
                // getManufacturerLink ($manufacturer, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                $canonical = $link->{$getLinkFunc}($id);
                break;

            case 'search':
                if ($tag = Tools::getValue('tag')) {
                    $params['tag'] = $tag;
                }
                if ($sq = Tools::getValue('search_query')) {
                    $params['search_query'] = $sq;
                }
            case 'products-comparison':
                if ($ids_str = Tools::getValue('compare_product_list')) {
                    // use an ordered products' list as canonical param 
                    $ids = explode('|', $ids_str);
                    sort($ids, SORT_NUMERIC);
                    $params['compare_product_list'] = implode('|', $ids);
                }

            default:
                // getPageLink($controller, $ssl = null, $id_lang = null, $request = null, $request_url_encode = false, $id_shop = null, $relative_protocol = fa    lse)
                $canonical = $link->getPageLink($controller, null, $id_lang, null, false, $id_shop);
                break;
        }

        if ('index' == $controller) {
            $canonical = rtrim($canonical, '/');
        }
        // retain pagination for controllers supportin it, remove p=1
        if (($p = Tools::getValue('p')) && $p>1 && in_array($controller, $this->_paginating_controllers)) {
            $params['p'] = $p;
        }

        if ($add_qs && count($params)>0) {
            $canonical .= '?'.http_build_query($params, '', '&');
        }
        
        return $canonical;
    }
}
