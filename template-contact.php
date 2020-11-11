<?php /* Template Name: Kapcsolat */

get_header();

the_post();
?>
<div class="uk-container">
    <div class="uk-grid-divider uk-child-width-1-2@m" uk-grid>
        <div>
            <?php the_content(); ?>
        </div>
        <div>
        <form class="ajax-form uk-form-stacked" name="contactForm">
            <input type="hidden" name="action" value="sendContactForm">

            <div class="uk-margin">
                <label class="uk-form-label" for="contact_name">Név</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="contact_name" type="text" name="name" placeholder="Pl.: Kiss Eduárd">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="contact_email">Email cím</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="contact_email" type="email" name="email" placeholder="Pl.: kiss.eduard@mail.com">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="contact_message">Üzenet</label>
                <div class="uk-form-controls">
                    <textarea id="contact_message" class="uk-textarea" rows="5" name="message" placeholder="Írja le pár szóban, mit szeretne nekünk üzenni"></textarea>
                </div>
            </div>

            <div class="uk-margin">
                <div class="uk-form-controls">
                    <input class="uk-checkbox" id="contact_privacy" type="checkbox" name="privacy" value="1"> <label for="contact_privacy">Elolvastam és elfogadom <a href="javascript:void(0)">az adatkezelési tájékoztatóban</a> leírtakat</label>
                </div>
            </div>

            <button type="submit" class="uk-button uk-button-primary">
                <span>Elküld</span>
                <div class="loader" style="display: none;" uk-spinner="ratio:0.8"></div>
            </button>

        </form>
        </div>
    </div>
</div>
<?php
get_footer();