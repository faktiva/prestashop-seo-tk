{foreach from=$shop_languages item=shop_langs}
	{foreach $shop_langs as $l}
		{assign 'url' $link->getLanguageLink($l.id_lang)|regex_replace:'/\?.*$/':''}
		{if $url}
			<link rel="alternate" hreflang="{$l.language_code}" href="{$url|escape:html}{$qs|escape:html:'UTF-8'}" />
		{/if}
	{/foreach}
{/foreach}
