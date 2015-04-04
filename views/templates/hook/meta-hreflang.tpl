{foreach $languages as $l}
	<link rel="alternate" hreflang="{$l.language_code}" href="{$link->getLanguageLink($l.id)}" />
{/foreach}
