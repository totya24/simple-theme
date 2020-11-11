<?php

Class ContactForm extends Singleton
{
    public function __construct()
    {
        add_action( 'wp_ajax_sendContactForm', [$this, 'sendContactForm'] );
        add_action( 'wp_ajax_nopriv_sendContactForm', [$this, 'sendContactForm'] );
    }

    public function sendContactForm()
    {
        $errors = [];

        if(empty($_POST['name'])){
            $errors['name'] = 'Kérem, adja meg a nevét';
        }

        if(empty($_POST['email'])){
            $errors['email'] = 'Kérem, adja meg az email címét';
        } elseif (!is_email($_POST['email'])){
            $errors['email'] = 'Kérem, valós email címet adjon meg';
        }

        if(empty($_POST['privacy'])){
            $errors['privacy'] = 'Kérem, fogadja ez az adatkezelési tájékoztatóban foglaltakat';
        }

        if(empty($_POST['message'])){
            $errors['message'] = 'Kérem, adja meg az üzenete szövegét';
        }
        
        if(!empty($errors)){
            wp_send_json_error( array('errors' => $errors) );
        }

        $mailContent = '<h2>Szép napot!</h2><br><p>Megkeresés érkezett a weboldal kapcsolati űrlapját használva.</p><br>'
        . '<b>Név: </b>'.$_POST['name'].'<br>'
        . '<b>Email cím: </b>'.$_POST['email'].'<br>'
        . '<b>Üzenet: </b><br>'.nl2br($_POST['message']);

        $to = ''; //mivel ez nincs kitöltve, mindig hibát fog dobni
        $subject = 'Megkeresés a weboldalról';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        $result = wp_mail( $to, $subject, $mailContent, $headers );

        if($result){
            $modalContent = '<button class="uk-modal-close-default" type="button" uk-close></button>'
            . '<div class="uk-modal-header"><h2 class="uk-modal-title">Köszönjük megkeresését!</h2></div>'
            . '<div class="uk-modal-body"><p>Amennyiben szükséges, munkatársunk hamarosan felveszi önnen a lapcsolatot.</p></div>'
            . '<div class="uk-modal-footer uk-text-center"><button class="uk-button uk-button-default uk-modal-close" type="button">Bezár</button></div>';
    
            wp_send_json_success( ['script' => 'contactForm.reset()', 'modal' => $modalContent, 'options' => json_encode(['bgClose' => false])] );
        }

        wp_send_json_success( ['notification' => 'Hoppá. Valami hiba történt az email küldése során, kérjük próbálja később újra, vagy próbálkozzon az elérhetőségek valamelyikével direkt módon.'] );
    }
}

ContactForm::getInstance();