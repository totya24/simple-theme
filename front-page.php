<?php

/**
 * Ez az oldal több esetben is bejöhet.
 * Ha nincsen beállítva, hogy külön főoldal legyen, akkro a legutóbbi posztok is itt listázódnak
 * Bővebben: https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 */

get_header();

the_post();

?>
<div class="uk-container">
    <div class="uk-padding uk-padding-remove-horizontal">
        <?php the_content(); ?>
    </div>
</div>
<?php

get_footer();