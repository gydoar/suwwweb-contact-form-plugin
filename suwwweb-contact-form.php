<?php

/*
  Plugin Name: Formulario de contacto
  Plugin URI: http://suWWWeb.com
  Description: Formulario de contacto para las páginas de suWWWeb
  Version: 1.0
  Author: Andrés Vega
  Author URI: http://suwwweb.com
 */

class suwwweb_contact_form {

    private $form_errors = array();

    function __construct() {
        // Registrar el shortcode: [sw_contact_form]
        add_shortcode('sw_contact_form', array($this, 'shortcode'));
    }

    static public function form() {
        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
        echo '<p>';
        echo 'Nombre * <br/>';
        echo '<input type="text" name="your-name" value="' . $_POST["your-name"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Email * <br/>';
        echo '<input type="text" name="your-email" value="' . $_POST["your-email"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Teléfono * <br/>';
        echo '<input type="text" name="your-telefono" value="' . $_POST["your-telefono"] . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Mensaje <br/>';
        echo '<textarea rows="10" cols="35" name="your-message">' . $_POST["your-message"] . '</textarea>';
        echo '</p>';
        echo '<p><input type="submit" name="form-submitted" value="Enviar"></p>';
		echo '</form>';
    }

    public function validate_form( $name, $email, $telefono) {
    	
        // If any field is left empty, add the error message to the error array
        if ( empty($name) || empty($email) || empty($telefono) ) {
            array_push( $this->form_errors, 'Existen campos requeridos sin llenar' );
        }
		
        // if the name field isn't alphabetic, add the error message
        if ( strlen($name) < 4 ) {
            array_push( $this->form_errors, 'El nombre debe incluir por lo menos 4 caracteres' );
        }

        // Check if the email is valid
        if ( !is_email($email) ) {
            array_push( $this->form_errors, 'El Email no es valido' );
        }
    }

    public function send_email($name, $email, $telefono, $message) {
        	
        // Ensure the error array ($form_errors) contain no error
        if ( count($this->form_errors) < 1 ) {

            // sanitize form values
            $name = sanitize_text_field($name);
            $email = sanitize_email($email);
            $telefono = sanitize_text_field($telefono);
            $message = esc_textarea($message);
            
			// get the blog administrator's email address
            $to = get_option('admin_email');
			
            $headers = "From: $name <$email>" . "\r\n";

            // If email has been process for sending, display a success message
            if ( wp_mail($to, $telefono, $message, $headers) )
                echo '<div style="background: #55a753; color:#fff; padding:2px;margin:2px">';
                echo 'Gracias por contactarnos, pronto nos pondremos en contacto';
                echo '</div>';
        }
    }

    public function process_functions() {
        if ( isset($_POST['form-submitted']) ) {
			
			// call validate_form() to validate the form values
            $this->validate_form($_POST['your-name'], $_POST['your-email'], $_POST['your-telefono'], $_POST['your-message']);
            
            // display form error if it exist
            if (is_array($this->form_errors)) {
                foreach ($this->form_errors as $error) {
                    echo '<div style="color:#DD514C;">';
                    echo '<strong>ERROR</strong>: ';
                    echo $error . '<br/>';
                    echo '</div>';
                }
            }
        }

        $this->send_email( $_POST['your-name'], $_POST['your-email'], $_POST['your-telefono'], $_POST['your-message'] );

        self::form();
    }

    public function shortcode() {
        ob_start();
        $this->process_functions();
        return ob_get_clean();
    }

}

new suwwweb_contact_form;
