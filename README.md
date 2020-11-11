
# SIMPLE - Kezdő WordPress sablon

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

## TODO
    * Piklist példakódok
        * Custom post type & custom taxonomy
        * Shortcode
        * Admin pages