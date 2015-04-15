{foreach from=$shops_data item=shop key=shop_id}
	{foreach $shop.languages as $lang}
		{assign 'url' $link->getLanguageLink($lang.id_lang, $shop.context)|regex_replace:'/\?.*$/':''}
		{if $url}
			<link rel="alternate" hreflang="{$lang.language_code}" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
