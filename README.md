[PrestaShop "SEO ToolKit"](https://github.com/faktiva/prestashop-seo-tk)
===

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d830cdd5-11a2-4ceb-8e07-96ce099305ee/big.png)](https://insight.sensiolabs.com/projects/d830cdd5-11a2-4ceb-8e07-96ce099305ee)
[![GitHub issues](https://img.shields.io/github/issues/faktiva/prestashop-seo-tk.svg?style=plastic)](https://github.com/faktiva/prestashop-seo-tk/issues)

[![Packagist](https://img.shields.io/packagist/l/faktiva/prestashop-seo-tk.svg?style=plastic)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![GitHub release](https://img.shields.io/github/release/faktiva/prestashop-seo-tk.svg?style=plastic&label=latest)](https://github.com/faktiva/prestashop-seo-tk/releases/latest)

[![Packagist](https://img.shields.io/packagist/dt/faktiva/prestashop-seo-tk.svg?style=plastic)](https://packagist.org/packages/faktiva/prestashop-seo-tk)
[![GitHub stars](https://img.shields.io/github/stars/faktiva/prestashop-seo-tk.svg?style=social)](https://github.com/faktiva/prestashop-seo-tk/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/faktiva/prestashop-seo-tk.svg?style=social&label=Forks)](https://github.com/faktiva/prestashop-seo-tk/network)

[![Join the chat at https://gitter.im/faktiva/prestashop-seo-tk](https://img.shields.io/badge/Gitter-CHAT%20NOW-brightgreen.svg?style=plastic)](https://gitter.im/faktiva/prestashop-seo-tk)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/faktiva/prestashop-seo-tk.svg?style=social)](https://twitter.com/intent/tweet?text=Fantastic @PrestaShop module: "PrestaShop SEO ToolKit"&url=https://github.com/faktiva/prestashop-seo-tk)

___

Handles a few basic SEO related improvements such as:
* "hreflang"
* "canonical"
* "noindex"

Being reported to work on **PS >= 1.6.0.9** .. but it should work on PS 1.6.x.x too and *could work* on PS >= 1.5.0.1

For production use the **latest stable [release](https://github.com/faktiva/prestashop-seo-tk/releases/)**

For developing or Pull Request please use only the **[dev](https://github.com/faktiva/prestashop-seo-tk/tree/dev)** branch


## Canonical URLs

Insert the `<meta rel="canonical">` html tag to avoid content duplication

Query string is removed only when needed, pagination is retained and handled (an existing "prev/next" mechanism is needed, newer PS already does it)

## HrefLang

Handle multilingual sites.
Insert the `<meta rel="hreflang">` html tag, a default lang is handled too.

**hreflang meta is only added on canonical pages**, as explained in the following image and explained by [Eoghan Henn](http://www.rebelytics.com/hreflang-canonical/)

<img src="./hreflang-canonical-image.jpg">

## NoIndex

Automatically assure a `<meta robots="noindex">` tag is added to every PS controller that should not been indexed by search engines.
This allows for big "robots.txt" cleanup .. and a better SEO 

The phylosophy is "Don't use robots.txt to tell robots to do not index a page, it should be used to block them .. it's different"

# License

![Creative Commons BY-NC-SA License](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)


**[PrestaShop SEO ToolKit](https://github.com/faktiva/prestashop-seo-tk)** by [Faktiva](https://github.com/faktiva) is licensed under a **Creative Commons [Attribution-NonCommercial-ShareAlike](http://creativecommons.org/licenses/by-nc-sa/4.0/) 4.0 International License**.

Permissions beyond the scope of this license may be available contacting us at info@faktiva.com.
