<link rel="XXX-default_lang" value="{$default_lang}" />
<link rel="XXX-default_shop" value="{$default_shop}" />
{foreach from=$shops_data item=shop_data key=shop_id}
	<link rel="XXX-shop-id" value="{$shop_data.context->shop->id}" />
	<link rel="XXX-shop-object" value="{$shop_data.context->shop|print_r|escape:'UTF-8'}" />
	{foreach $shop_data.languages as $lang}
		{assign 'url' $link->getLanguageLink($lang.id_lang, $shop_data.context)|regex_replace:'/\?.*$/':''}
		{if $url}
			{if $default_lang == $lang.id_lang AND $default_shop == $shop_data.context->shop->id}
			<link rel="alternate" hreflang="x-default" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
			{/if}
			<link rel="alternate" hreflang="{$lang.language_code}" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
