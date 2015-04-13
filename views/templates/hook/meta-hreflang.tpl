{foreach $languages as $l}
	<link rel="alternate" hreflang="{$l.language_code}" href="{$link->getLanguageLink($l.id)|regex_replace:'/\?.*$/':''|escape:html}{$qs|escape:html:'UTF-8'}" />
{/foreach}
