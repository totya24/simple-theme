
# Egyszerű WordPress sablon




  

## TWIG sablonkezelő rendszer

A sablon kiegészült egy beépített [TWIG](https://twig.symfony.com/) sablonkezelő támogatással. Ez a következő változásokat vonja maga után:
* Eltűnt a header.php és footer.php. Helyette a **templates/components/header.twig** és **templates/components/footer.twig** került használatba.
* A globálisan elérhető változók a **includes/twig.globals.php** fájlban lettek definiálva. A fontosabbak: 
  * **site.title** : oldal címe (wp_title alapján)
  * **site.lang** : aktuális nyelv (get_locale alapján)
  * **site.baseUrl** : alapértelmezett url
  * **site.themeUrl** : az aktuális sablon elérési útvonala
  * **site.privacyPolicyUrl** : Ammennyiben van adatkezelési tájékoztató oldal, annak az url-je.
* A *page.php, single.php* és hasonló sablonfájlok gyakorlatilag *controllerként* funkcionálnak, azaz megjelenéssel kapcsolatos kódot nem tartalmaznak, csupán összegyűjtjük bennük a megjeleníteni kívánt adatokat.

### TWIG nélkül
**page.php**
```php
<?php 
get_header()
the_post();
?>
<main>
<div class="ez-a-tartalom">
<h1><?phh the_title(); ?></h1>
<div><?php the_content(); ?></div>
</main>
<?php
get_footer();
?>
```

### TWIG használatával
**page.php**
```php
<?php 
the_post();
$data = array(
	'title' => get_the_title(),
	'content' => apply_filters('the_content', get_the_content())
); 
twig_render('pages/page.twig', $data);
```
**templates/pages/page.twig**
```twig
{% extends  "master.twig" %}
{% block  content %}
<h1>{{ title }}</h1>
<div>{{ content }}</div>
{% endblock %}
```

## Fájlrendszer

* **.vscode** : VS Code részére létrehozott task, ami kezeli az uikit scss fordítását
*  **assets** : Minden kapcsolódó assetet tartalmazó könyvtár
   * ** css ** : tartalmazza a lefodított style.min.css-t és admin.css-t
   * **img** : könyvtár a sablon képeinek
   * **js** : javascript fájlok
   * **scss** : uikit forrás, kiegészítve a testreszabáshoz szükséges dolgokkal (kiinduló fájl a theme.scss)
   * **svg** : külön könyvtár az svg fájlok részére (TWIG aliast is kapott: @svg/[ikon.svg])
* **core** : a sablon működésének magja, csak indokolt esetben kell piszkálni, kifejtése később
* **includes** : minden a működést meghatározó fájl ide kerül, php kiterjesztés esetén automatikusan be lesznek töltve. A prefixelés opcionális, de segít az átláthatóságban
* **templates** : TWIG sablonok
* **tweaks** : apró módosítók, amik bármikor bővíthetők, törölhetők
    * **clear-upload-filenames.php** : a fájlokat ékezetmentesíti a feltöltés során
    * **disable-author-search.php** : letiltja, hogy csak úgy lekérdezhető legyen az összes felhasználó
    * **disable-comments.php** : kommentek letiltása globálisan
    * **disable-emojis.php** : alapértelmezett emoji támogatás letiltása
    * **disable-feeds.php** : RSS feed letiltása
    * **disable-restapi.php** : RESTAPI letiltása (bizonyos esetekben megakaszthatja a weboldal működését)
    * **disable-xmlrpc.php** : XMLRPC letiltása (bizonyos esetekben megakaszthatja a weboldal működését)
    * **hide-post.disabled** : bejegyzések eltűntetése adminról (kikapcsolva)
    * **override-emails.php** : kimenő emailek felülírása (Admin->Beállítások->Általános->Kimenő levelek)
    * **remove-dashboard-widgets.php** : Haszontalan vezérlőpulti widgetek eltávolítása
    * **slow-hearthbeat.php** : Adminon az ajax (hearthbeat) hívás lelassítása

## UIKIT
A sablon alapból tartalmazza a [UIKUT](https://getuikit.com/) legfrissebb fejlesztői változatát, amit testre is lehet szabni az scss forráson keresztül. Bővebben erről itt: [https://getuikit.com/docs/sass](https://getuikit.com/docs/sass). 

#### Fordítás konzolnból
```bash
npm install -g node-sass

node-sass --output-style compressed assets/scss/theme.scss > assets/css/style.min.css
```

#### VS Code esetén: 
A node-sass package telepítése után: Ctrl+Shift+B -> SCSS Complie task futtatása