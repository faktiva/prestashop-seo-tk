{foreach from=$shops_data item=shop_data key=shop_id}
    {foreach $shop_data item=data}
        {if $default_lang_id == $data.language.id AND $default_shop_id == $shop_id}
        <link rel="alternate" hreflang="x-default" href="{$data.url|escape:'html':'UTF-8'}" />
        {/if}
        <link rel="alternate" hreflang="{$data.language.code|strtolower|replace:'_':'-'|escape:'html':'UTF-8'}" href="{$data.url|escape:'html':'UTF-8'}" />
    {/foreach}
{/foreach}
