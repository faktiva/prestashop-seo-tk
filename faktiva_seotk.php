<?php

/*
 * This file is part of the "Prestashop SEO ToolKit" module.
 *
 * @author   Emiliano 'AlberT' Gabrielli <albert@faktiva.com>
 * @source   https://github.com/faktiva/prestashop-seo-tk
 * @license  MIT
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-2017 Emiliano Gabrielli
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

if (!defined('_PS_VERSION_')) {
    return;
}

if (version_compare(PHP_VERSION, '5.3.0', '>=')) { // Namespaces support is required
    include_once __DIR__ . '/tools/debug.php';
}

/**
 * Class Faktiva_Seotk
 */
class Faktiva_Seotk extends Module
{
    private $_controller;

    /**
     * @var string[]
     */
    private static $_paginating_controllers = array(
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

    /**
     * @var string[]
     */
    private static $_nobots_controllers = array(
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
        'pagenotfound',
        'pagination',
        'password',
        'pdf-invoice',
        'pdf-order-return',
        'pdf-order-slip',
        'product-sort',
        'search',
        'statistics',
    );

    /**
     * Faktiva_Seotk constructor.
     */
    public function __construct()
    {
        $this->name = 'faktiva_seotk';
        $this->author = 'Faktiva';
        $this->tab = 'seo';
        $this->version = '1.4.4';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.5.0.1', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Faktiva SEO ToolKit');
        $this->description = $this->l('Handles a few SEO related improvements, such as \'hreflang\', \'canonical\' and \'noindex\'.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall "Faktiva SEO ToolKit"?');
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook('header')
            && Configuration::updateValue('FKVSEOTK_HREFLANG_ENABLED', false)
            && Configuration::updateValue('FKVSEOTK_CANONICAL_ENABLED', false)
            && Configuration::updateValue('FKVSEOTK_NOBOTS_ENABLED', false)
        ;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('FKVSEOTK_HREFLANG_ENABLED')
            && Configuration::deleteByName('FKVSEOTK_CANONICAL_ENABLED')
            && Configuration::deleteByName('FKVSEOTK_NOBOTS_ENABLED')
        ;
    }

    /**
     * @param $template
     * @param null $cache_id
     * @param null $compile_id
     *
     * FIXME
     */
    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache('meta-hreflang.tpl', $this->getCacheId($cache_id));
        parent::_clearCache('meta-canonical.tpl', $this->getCacheId($cache_id));
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $_html = '<div id="'.$this->name.'_config_intro" class="alert alert-info">'.
            '  <span class="module_name">'.$this->displayName.'</span>'.
            '  <div class="module_description">'.$this->description.'</div>'.
            '</div>';

        if (Tools::isSubmit('submitOptionsconfiguration')) {
            if (null!==Tools::getValue('FKVSEOTK_HREFLANG_ENABLED')) {
                Configuration::updateValue('FKVSEOTK_HREFLANG_ENABLED', (bool)Tools::getValue('FKVSEOTK_HREFLANG_ENABLED'));
            }

            if (null!==Tools::getValue('FKVSEOTK_CANONICAL_ENABLED')) {
                Configuration::updateValue('FKVSEOTK_CANONICAL_ENABLED', (bool)Tools::getValue('FKVSEOTK_CANONICAL_ENABLED'));
            }

            if (null!==Tools::getValue('FKVSEOTK_NOBOTS_ENABLED')) {
                Configuration::updateValue('FKVSEOTK_NOBOTS_ENABLED', (bool)Tools::getValue('FKVSEOTK_NOBOTS_ENABLED'));
            }
        }

        $_html .= $this->renderForm();

        return $_html;
    }

    /**
     * @return mixed
     */
    public function renderForm()
    {
        $this->fields_option = array(
            'hreflang' => array(
                'title' => $this->l('Internationalization'),
                'icon' => 'icon-flag',
                'fields' => array(
                    'FKVSEOTK_HREFLANG_ENABLED' => array(
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
                    'FKVSEOTK_CANONICAL_ENABLED' => array(
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
                'title' => $this->l('"nobots"'),
                'icon' => 'icon-sitemap',
                'fields' => array(
                    'FKVSEOTK_NOBOTS_ENABLED' => array(
                        'title' => $this->l('Enable "noindex" meta tag'),
                        'hint' => $this->l('Set "noindex" meta tag into the html head to avoid search engine indicization of "private" pages. Public pages are not affected of course.'),
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

    /**
     * @return string
     */
    public function hookHeader()
    {
        $this->_controller = Dispatcher::getInstance()->getController();
        if (!empty($this->context->controller->php_self)) {
            $this->_controller = $this->context->controller->php_self;
        }

        if ($this->_handleNobots()) {
            // no need to add anything else as robots should ignore this page
            return '';
        }

        $out = "\n"
            .$this->_displayHreflang()
            .$this->_displayCanonical()
        ;

        return $out;
    }

    /**
     * @return bool
     */
    private function _handleNobots()
    {
        if (Configuration::get('FKVSEOTK_NOBOTS_ENABLED')) {
            if (in_array($this->_controller, self::$_nobots_controllers, true)
                || Tools::getValue('selected_filters')
                || Tools::getValue('orderby')
                || Tools::getValue('orderway')
            ) {
                $this->context->smarty->assign('nobots', true);

                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function _displayHreflang()
    {
        if (!Configuration::get('FKVSEOTK_HREFLANG_ENABLED')) {
            return '';
        }

        $shop = $this->context->shop;
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            $requested_URL = $shop->getBaseURL(true /* $auto_secure_mode */, false /* $add_base_uri */) . $_SERVER['REQUEST_URI'];
        } else {
            $proto = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://';
            $domain = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? $shop->domain_ssl : $shop->domain;
            $requested_URL = $proto . $domain . $_SERVER['REQUEST_URI'];
        }

        if (Configuration::get('FKVSEOTK_CANONICAL_ENABLED') && !$this->_isCanonicalRequest($requested_URL)) {
            return ''; // skip if actual page is not the canonical page
        }

        foreach (Shop::getShops(true /* $active */, null /* $id_shop_group */, true /* $get_as_list_id */) as $shop_id) {
            foreach (Language::getLanguages(true /* $active */, $shop_id) as $language) {
                $url = $this->_getCanonical($language['id_lang'], $shop_id, true /* $has_qs */);
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

    /**
     * @return string
     */
    private function _displayCanonical()
    {
        if (!Configuration::get('FKVSEOTK_CANONICAL_ENABLED')) {
            return '';
        }

        $canonical = $this->_getCanonical();

        if (!$this->isCached('meta-canonical.tpl', $this->getCacheId($canonical))) {
            $this->context->smarty->assign(array(
                'canonical_url' => $canonical,
            ));
        }

        return $this->display(__FILE__, 'meta-canonical.tpl', $this->getCacheId($canonical));
    }

    /**
     * @param array $params
     * @param null $id_lang
     * @param null $id_shop
     *
     * @return string
     */
    private function _getCanonicalByLink(array &$params, $id_lang = null, $id_shop = null)
    {
        $link = $this->context->link;
        $controller = $this->_controller;
        $module = Tools::getValue('module');
        $id = (int)Tools::getValue('id_'.$controller);
        $getLinkFunc = 'get'.ucfirst($controller).'Link';

        if (!$link || !$controller) {
            return '';
        }

        switch ($controller.$module) {
            case 'product':
                // getProductLink($product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null, $ipa = 0, $force_routes = false, $relative_protocol = false)
                $canonical = $link->getProductLink($id, null, null, null, $id_lang, $id_shop);
                break;

            case 'category':
                // getCategoryLink($category, $alias = null, $id_lang = null, $selected_filters = null, $id_shop = null, $relative_protocol = false)
                $canonical = $link->getCategoryLink($id, null, $id_lang, Tools::getValue('selected_filters', null), $id_shop);
                break;

            case 'cms':
                if ($cat_id = (int)Tools::getValue('id_cms_category')) {
                    // getCMSCategoryLink($cms_category, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                    $canonical = $link->getCMSCategoryLink($cat_id, null, $id_lang, $id_shop);
                } else {
                    // getCMSLink($cms, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                    $canonical = $link->getCmsLink($id, null, null, $id_lang, $id_shop);
                }
                break;

            case 'supplier':
                // getSupplierLink    ($supplier,     $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
            case 'manufacturer':
                // getManufacturerLink($manufacturer, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                $canonical = $id
                    ? $link->{$getLinkFunc}($id, null, $id_lang, $id_shop)
                    : $link->getPageLink($controller, null, $id_lang, null, false, $id_shop);
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
                if (Validate::isModuleName($module)) {
                    $_params = $_GET;
                    unset($_params['fc']);
                    // getModuleLink($module, $controller = 'default', array $params = array(), $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
                    $canonical = $link->getModuleLink($module, $controller, $_params, null, $id_lang, $id_shop);
                } else {
                    // getPageLink($controller, $ssl = null, $id_lang = null, $request = null, $request_url_encode = false, $id_shop = null, $relative_protocol = false)
                    $canonical = $link->getPageLink($controller, null, $id_lang, null, false, $id_shop);
                }
        }

        return $canonical;
    }

    /**
     * @param int|null $id_lang
     * @param int|null $id_shop
     * @param bool $add_qs
     *
     * @return mixed
     */
    private function _getCanonical($id_lang = null, $id_shop = null, $add_qs = true)
    {
        $controller = $this->_controller;
        $params = array();

        $canonical = $this->_getCanonicalByLink($params, $id_lang, $id_shop);
        if ('index' === $controller && '/' === strtok($_SERVER['REQUEST_URI'], '?')) {
            $canonical = rtrim($canonical, '/');
        }

        // retain pagination for controllers supporting it, remove p=1
        if (($p = Tools::getValue('p')) && $p > 1
            && (in_array($controller, self::$_paginating_controllers, true) || Tools::getValue('module'))
        ) {
            $params['p'] = $p;
        }

        // remove "dirty" QS
        $canonical = strtok($canonical, '?');
        // add "canonical" QS if enabled
        if ($add_qs && !empty($params)) {
            $canonical .= '?'.http_build_query($params, '', '&');
        }

        // HACK: Link class should return the right protocol for a given shop but it doesn't
        $protocol = Configuration::get('PS_SSL_ENABLED', null, null, $id_shop) && Configuration::get('PS_SSL_ENABLED_EVERYWHERE', null, null, $id_shop) ? 'https' : 'http' ;

        return preg_replace('/^https?/i', $protocol, $canonical);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function _isCanonicalRequest($url)
    {
        $canonical = $this->_getCanonical(null, null, false /* $has_qs */);
        $request = strtok($url, '?');
        if ('index' === $this->_controller) {
            $request = rtrim($request, '/');
            $canonical = rtrim($canonical, '/');
        }

        return ($request === $canonical);
    }
}
