[PrestaShop module "Faktiva SEO ToolKit"](https://github.com/faktiva/prestashop-seo-tk)
===

[![GitHub release](https://img.shields.io/github/release/faktiva/prestashop-seo-tk.svg?style=flat&label=latest)](https://github.com/faktiva/prestashop-seo-tk/releases/latest)
[![Project Status](http://opensource.box.com/badges/active.svg?style=flat)](http://opensource.box.com/badges)
[![Percentage of issues still open](http://isitmaintained.com/badge/open/faktiva/prestashop-seo-tk.svg?style=flat)](http://isitmaintained.com/project/faktiva/prestashop-seo-tk "Percentage of issues still open")
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/faktiva/prestashop-seo-tk.svg?style=flat)](http://isitmaintained.com/project/faktiva/prestashop-seo-tk "Average time to resolve an issue")
[![composer.lock](https://poser.pugx.org/faktiva/prestashop-seo-tk/composerlock?style=flat)](https://packagist.org/packages/faktiva/prestashop-seo-tk)
[![Dependencies Status](https://img.shields.io/librariesio/github/faktiva/prestashop-seo-tk.svg?maxAge=3600&style=flat)](https://libraries.io/github/faktiva/prestashop-seo-tk)
[![License](https://img.shields.io/packagist/l/faktiva/prestashop-seo-tk.svg?style=flat)](https://tldrlegal.com/license/mit-license)

[![Join the chat at https://gitter.im/faktiva/prestashop-seo-tk](https://img.shields.io/badge/Gitter-CHAT%20NOW-brightgreen.svg?style=flat)](https://gitter.im/faktiva/prestashop-seo-tk)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/faktiva/prestashop-seo-tk.svg?style=social)](https://twitter.com/intent/tweet?text=Fantastic+@PrestaShop+module+"%23Faktiva+SEO+ToolKit"&url=https://github.com/faktiva/prestashop-clean-urls)
[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YF3R37RLY85CU&lc=IT&item_name=faktiva&item_number=GitHub%2dprestashop%2dseo%2dtk&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted)

____


> **Warning**: This SW is unmaintained



Handles a few basic SEO related improvements such as:
* "hreflang"
* "canonical"
* "noindex"

>
>    For production use the **latest stable [release](https://github.com/faktiva/prestashop-seo-tk/releases/latest)**
>
>    It has been reported to work on **PS >= 1.6.0.9** but it should work on PS 1.6.x.x too and *could work* on PS >= 1.5.0.1 too.
>    **If you succesfully use this module on some older version please report**
>

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

