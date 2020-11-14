
<<<<<<< HEAD
# SIMPLE - Kezdő WordPress sablon
=======
# Egyszerű WordPress sablon

## Core funkciók

A core könyvtár tartalmazza azokat az extrákat, amiket változtatni alapvetően enm akarunk, viszont soksok plugin funkcionalitását adja a sablon működéséhez. (A core könyvtárban lévő mappák az egyes funkcionalitások kiegészítői, részletezésük nem szükséges)
* **acrhivelinks.php** : A menüszerkesztőben megjelennek az egyes custom post típusokhoz tartozó archívum linkek
* **customlistcolumns.php** : könnyedén hozzá lehet adni az admin post típus listákhoz egyedi mezőket (extra benne, hogy a dátum elé teszi midnig őket)
* **duplicatepost.php** : a quickmenüben (admin, post típus listák) megjelenik egy másolás gomb, amivel könnyedén lehet duplikálni bármilyen bejegyzést, metaadatokkal együtt
* **greedo.php** : helper osztály, amiben jelenleg csak egy fancy var dump van (az util.php -ból nyúlva!)
* **optimize.php** : alapvető extrál beállítása (head generator eltűntetése, theme support beállítás, admin bar eltűntetés)
* **postorder.php** : sorbarendezhetővé teszi az egyes post típusokat
* **singleton.php** : egyke pattern, amit minden használ. azért kell, mhogy az egyes constructorban megadott hook-ok szigorúan csak egyszer fussanak le
* **wptwig.php** : TWIG támogatást megoldó kiegészítő

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
>>>>>>> 132ff6b086dbb73ee8288f017d616e51556d941f

###

## Fájlrendszer

* **.vscode** : VS Code részére létrehozott task, ami kezeli az uikit scss fordítását
*  **assets** : Minden kapcsolódó assetet tartalmazó könyvtár
   * ** css ** : tartalmazza a lefodított style.min.css-t és admin.css-t
   * **img** : könyvtár a sablon képeinek
   * **js** : javascript fájlok
   * **scss** : uikit forrás, kiegészítve a testreszabáshoz szükséges dolgokkal (kiinduló fájl a theme.scss)
* **core** : a sablon működésének magja, csak indokolt esetben kell piszkálni, kifejtése később
* **includes** : minden a működést meghatározó fájl ide kerül, php kiterjesztés esetén automatikusan be lesznek töltve. A prefixelés opcionális, de segít az átláthatóságban
* **tweaks** : apró módosítók, amik bármikor bővíthetők, törölhetők
    * **clear-upload-filenames.php** : a fájlokat ékezetmentesíti a feltöltés során
    * **disable-author-search.php** : letiltja, hogy csak úgy lekérdezhető legyen az összes felhasználó
    * **disable-comments.php** : kommentek letiltása globálisan
    * **disable-emojis.php** : alapértelmezett emoji támogatás letiltása
    * **disable-feeds.php** : RSS feed letiltása
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

## Beépített funkciók alkalmazása

#### Post típusok sorbarendezhetősége
A ** filter meghívásával lehet megadni, ha egy post típust sorbarendezhetővé szeretnénk tenni.
```php
add_filter( 'post_order_types', function($types){ $types[] = '[POST_TYPE]'; return $types; } );
```

**Lekérés módosítása**
```php
add_action( 'pre_get_posts', '_modifyQuery' );
function _modifyQuery( $query ) {
	if ( is_post_type_archive( '[POST_TYPE]' ) ) {
    		$query->set( 'posts_per_page', '-1' );
    		$query->set( 'orderby', 'menu_order' );
    		$query->set( 'order', 'ASC' );
	}
	return $query;
}
```

#### Custom List Columns
Új mezőt ad hozzá az admin post listához. Használata:
```php
$columns = new CustomAdminListColumn([POST_TYPE], array(
	'Bélyegkép' => function($postId){
		echo get_the_post_thumbnail($postId, array('75','75'));
	}
));
```

#### Debug mód
A **/?debug=1** GET paraméter alkalmazása esetén megjelennek a NOTICE és WARNING típusú hibaüzenetek is, továbbá az admin bar.

#### Fancy var_dump
Szebb és értelmezhetőbb var_dump-ot kapunk a következő módon:
```php
Greedo::var_dump($barmi);
```
