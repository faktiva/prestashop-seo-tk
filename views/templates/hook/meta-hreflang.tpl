{foreach from=$shops item=shop_languages}
	{foreach $shop_languages as $l}
		<link rel="alternate" hreflang="{$l.language_code}" href="{$link->getLanguageLink($l.id)|regex_replace:'/\?.*$/':''|escape:html}{$qs|escape:html:'UTF-8'}" />
	{/foreach}
{/foreach}
