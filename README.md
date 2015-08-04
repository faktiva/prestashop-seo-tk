# PrestaShop module "ZiZuu SEO ToolKit"

[ABOUT](https://github.com/ZiZuu-store/PrestaShop_module-zzSEOtk)

Handles a few basic SEO related improvements such as "hereflang" and "canonical".

For production use the **latest stable [release](https://github.com/ZiZuu-store/PrestaShop_module-zzSEOtk/releases/)**

For developing or Pull Request please use only the "**develop**" branch


## Canonical URLs

Insert the `<meta rel="canonical">` html tag to avoid content duplication

Query string is removed only when needed, pagination is retained and handled (an existing "prev/next" mechanism is needed, newer PS already does it)

## HrefLang

Handle multilingual sites.
Insert the `<meta rel="hreflang">` html tag, a default lang is handled too.

hreflang meta is only added on canonical pages, as explained in the following image and explained by [Eoghan Henn](http://www.rebelytics.com/hreflang-canonical/)

<img src="./hreflang-canonical-image.jpg">

