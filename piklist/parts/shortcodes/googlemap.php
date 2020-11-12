<?php
/*
Shortcode: googlemap
*/

$language = get_locale();
$language = substr($language, 0, 2);

?>
<div class="mapouter">
<div class="gmap_canvas">
<iframe width="100%" height="<?php echo $arguments['height']; ?>" id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo $arguments['location']; ?>&t=&z=<?php echo $arguments['zoom']; ?>&ie=UTF8&iwloc=&output=embed&hl=<?php echo $language; ?>" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</div>
<style>.mapouter{position:relative;text-align:right;height:<?php echo $arguments['height']; ?>px;width:100%;}.gmap_canvas {overflow:hidden;background:none!important;height:<?php echo $arguments['height']; ?>px;width:100%;}</style>
</div>