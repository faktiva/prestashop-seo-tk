{foreach from=$shop_languages item=shop_langs key=shop_id}
	{foreach $shop_langs.languages as $l}
		{assign 'url' $link->getLanguageLink($l.id_lang, $shop_langs.context)|regex_replace:'/\?.*$/':''}
		{if $url}
			<link rel="alternate" hreflang="{$l.language_code}" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
