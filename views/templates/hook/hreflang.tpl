{if $page_name == 'category'}
	{foreach $languages as $lang}       
	<link rel="alternate" hreflang="{$lang.language_code}" href="{$link->getCategoryLink($smarty.get.id_category, null, $lang.id_lang,null,null )}" />	   
	{/foreach}
{/if}
{if $page_name == 'product'} 
	{foreach $languages as $lang}       
	<link rel="alternate" hreflang="{$lang.language_code}" href="{$link->getProductLink($smarty.get.id_product, null, null, null, $lang.id_lang, null, 0, false)}" />	   
	{/foreach}
{/if}
{if $page_name == 'cms'} 
	{foreach $languages as $lang}       
	<link rel="alternate" hreflang="{$lang.language_code}" href="{$link->getCMSLink($smarty.get.id_cms, null, false, $lang.id_lang)}" />	   
	{/foreach}
{/if}
{if $page_name == 'index'}
	{foreach $languages as $lang}       
	<link rel="alternate" hreflang="{$lang.language_code}" href="{Tools::getShopDomainSsl(true, true)}" />
	{/foreach}
{/if}
