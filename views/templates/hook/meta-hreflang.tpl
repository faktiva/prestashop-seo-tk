{foreach from=$shops_data item=shop_data key=shop_id}
	{foreach $shop_data.languages as $lang}
		{* FIXME *}
		{assign 'url' $link->getLanguageLink($lang.id_lang, $shop_data.context)|regex_replace:'/\?.*$/':''|replace:'f:1':''}
		{assign 'url' $url|replace:$current_domain:$shop_data.context->shop->domain|escape:'html':'UTF-8'}
		{if $url}
			{if $page_name == 'index'}{assign 'url' $url|rtrim:'/'}{/if}
			{if $default_lang == $lang.id_lang AND $default_shop == $shop_data.context->shop->id}
			<link rel="alternate" hreflang="x-default" href="{$url|escape:'html':'UTF-8'}{$qs|escape:'html':'UTF-8'}" />
			{/if}
			<link rel="alternate" hreflang="{$lang.language_code|strtolower|replace:'_':'-'|escape:'html':'UTF-8'}" href="{$url|escape:'html':'UTF-8'}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
