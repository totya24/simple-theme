</main>
<footer class="uk-background-secondary">
    
    <?php 
    $themeOptions = get_option('theme_settings');
    if(!empty($themeOptions['footer_content'])){
        echo '<div class="uk-container uk-padding">';
        echo '<div class="uk-child-width-expand" uk-grid>';
        foreach($themeOptions['footer_content'] as $column){
            echo '<div>';
            echo !empty($column['title']) ? '<h3 class="uk-light">'.$column['title'].'</h3>' : '';
            echo '<div>'.$column['content'].'</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
    ?>
    
    <div class="uk-background-secondary">
        <div class="uk-container uk-text-center uk-padding-small">&copy; <?php echo date('Y'); ?> Minden jog fenntartva</div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>