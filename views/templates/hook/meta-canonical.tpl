{if isset($smarty.get.p) && !empty($smarty.get.p)}
	{assign var="pagenumber" value={l s=' - Page  %1$d' sprintf=$smarty.get.p} }
	{assign var="pagination" value="?p={$smarty.get.p}" }
{else}
	{assign var="pagenumber" value="" }
	{assign var="pagination" value="" }
{/if}

{assign 'pages_array' ['best-sales', 'new-products', 'adresses', '404', 'address', 'authentication', 'my-account', 'contact-form', 'discount', 'guest-tracking', 'index', 'history', 'manufacturer', 'order-opc', 'order-follow' ,'order-carrier' ,'order-payment', 'order-detail', 'order-slip', 'order-return', 'order-confirmation', 'order-address', 'password', 'search', 'prices-drop', 'sitemap', 'store-infos', 'stores', 'supplier-list', 'suppliers']}

{assign 'noindex_pages_array' ['404', 'address', 'my-account', 'guest-tracking', 'order-opc', 'order-follow' ,'order-carrier' ,'order-payment', 'order-detail', 'order-slip', 'order-confirmation', 'order-address', 'cart', 'search', 'supplier-list', 'supplier', 'suppliers']}

{if isset($smarty.server.REQUEST_URI)}
	{assign 'noindex_vars' ['noredirect','orderway','orderby','content_only']}

	{foreach from=$noindex_vars item=v}
		{if stristr($smarty.server.REQUEST_URI,$v) }
			{assign var=nobots value=true}
			{break}
		{/if}
{/if}

{if $page_name == 'product' && isset($product->id)}
	<link rel="canonical" href="{$link->getProductLink($product->id)}" />
{elseif $page_name == 'manufacturer' && isset($manufacturer->id)}
	<link rel="canonical" href="{$link->getManufacturerLink($manufacturer->id)}{$pagination}" />
{elseif $page_name == 'supplier' && isset($supplier->id)}
	<link rel="canonical" href="{$link->getSupplierLink($supplier->id)}{$pagination}" />
{elseif $page_name == 'category' && isset($category->id)}
	<link rel="canonical" href="{$link->getCategoryLink($category->id)}{$pagination}" />
{elseif in_array($page_name,$pages_array)}
	<link rel="canonical" href="{$link->getPageLink($page_name)}" />
{elseif $page_name == 'cms' && isset($cms->id)}
	<link rel="canonical" href="{$link->getCmsLink($cms->id)}" />
{else}
	{assign var=amn value=explode("-",$page_name)}
	{if strpos($page_name,"module-") !== false && count($amn) == 3}
		<link rel="canonical" href="{$link->getModuleLink($amn.1,$amn.2)}{$pagination}" />
	{else}
		<link rel="canonical" href="{$base_dir|replace:'.fr/':'.fr'|replace:'http':'https'}{$request_uri|regex_replace:'/\?(.*)/':''|replace:'index.php':''}{$pagination}" />
		{assign var=nobots value=true}
	{/if}
{/if}

{if in_array($page_name,$noindex_pages_array)}
	{assign var=nobots value=true}
{/if}

<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
