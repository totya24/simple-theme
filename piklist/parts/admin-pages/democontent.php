<?php
/*
 * Page: simple-demo-content
 */

if(wp_verify_nonce($_GET['nonce'], 'demo-content')){
    if(!empty($_GET['action']) && $_GET['action'] == 'create-contact-page'){
        
        $content = '<p>Négy különböző nemzetiség képviselője volt az asztalnál: egy amerikai gyalogos, egy francia őrvezető, egy angol géppuskás és egy orosz hússaláta. A gyalogos, az őrvezető és a géppuskás a padon foglaltak helyet, a hússaláta az asztalon, egy tálban.</p>[googlemap location="Miskolc%2C%20Hunyadi%20%C3%BAt%2054." zoom="15" height="300"]';

        $postId = wp_insert_post([
            'post_title' => 'Kapcsolat',
            'post_content' => $content,
            'post_type' => 'page',
            'post_status' => 'published',
            'page_template' => 'template-contact.php'
        ]);

        if(is_numeric($postId)){
            echo '<div class="notice notice-success is-dismissible"><p><strong>Az oldal sikeresen létrehozva</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        } 
    }
    
}

$nonce = wp_create_nonce( 'demo-content' );
?>

<div class="card">
    <h2 class="title">Kapcsolat olal</h2>
    <p>Egyedi oldalsablon, amely tartalmaz egy űrlapot és a hozzá kapcsolódó funkcionalitást. Emelett van egy egyszerű shortcode példa is, ami szemlélteti, hogy hogyan kell megcsinálni őket. Az odlal létrehozásakor létrejön egy <i>Kapcsolat</i> oldal a megfelelő beállításokkal és tartalommal.</p>
    
    <form method="GET" action="">
        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
        <input type="hidden" name="action" value="create-contact-page">
        <input type="hidden" name="nonce" value="<?php echo $nonce ?>">
        <button type="submit" class="button button-primary button-large">Kapcsolat oldal létrehozása</button>
    </form>
</div>

