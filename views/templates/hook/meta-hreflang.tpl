{foreach from=$shops_data item=shop_data key=shop_id}
	<link rel="XXX-debug-shop-id" value="{$shop_data.context->shop->id|escape:'UTF-8'}" />
	<link rel="XXX-debug-shop" value="{$shop_data.context->shop|print_r|escape:'UTF-8'}" />
	{foreach $shop_data.languages as $lang}
		{assign 'url' $link->getLanguageLink($lang.id_lang, $shop_data.context)|regex_replace:'/\?.*$/':''}
		{if $url}
			<link rel="alternate" hreflang="{$lang.language_code}" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
