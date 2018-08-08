<?php

class LoginManager extends MidoManager  {




    public static function render_private(&$context){

        add_filter( 'rocket_override_donotcachepage', '__return_true', PHP_INT_MAX );
       
        

        if(!is_user_logged_in()){
			
            self::hydrate_context($context);

            $context->render('login.twig');
            
		} else {

            $context->logout = array(
                'link' => wp_logout_url( get_permalink() ),
                'text' => __('Logout','mimcord')
            );

            $context->headerRight = '<a href="'.$context->logout['link'].'">'.$context->logout['text'].'</a>';

            $context->render();
            
		}

    }





    function action_wp_login_failed( $username ) {
        $referrer = $_SERVER['HTTP_REFERER'];  
        if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
            wp_redirect(strtok($_SERVER["HTTP_REFERER"],'?') . '/?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
            exit;
        }
    }



    function action_lostpassword_post_99(){
        $referrer = $_SERVER['HTTP_REFERER'];

        if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
            if(isset($_POST['user_login']) && !empty($_POST['user_login'])){
                $email_address = $_POST['user_login'];
                if(filter_var( $email_address, FILTER_VALIDATE_EMAIL )){
                    if(!email_exists( $email_address )){
                        wp_redirect( strtok($_SERVER["HTTP_REFERER"],'?').'/?lostpassword=true&userexist=false' );
                        exit;
                    }
                }else{
                        $username = $_POST['user_login'];
                        if ( !username_exists( $username ) ){
                            wp_redirect( strtok($_SERVER["HTTP_REFERER"],'?').'/?lostpassword=true&userexist=false' );
                            exit;
                        }
                    } 

            }else{

                wp_redirect( strtok($_SERVER["HTTP_REFERER"],'?').'/?lostpassword=true&lostempty=true' );
                exit;   
            }
        }
    }
	
    
    function hydrate_context(&$context){

        WebManager::set_header(array(
            'display' => false,
            'background' => 'blue'
        ));

        WebManager::set_footer(false);
        
        $context->redirect = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"],'?');
		
        $context->GET = $_GET;
			
		if($_GET['lostpassword']){
               
				$context -> action = site_url('wp-login.php?action=lostpassword', 'login_post');
			} else {
				$context -> action = get_bloginfo('url').'/wp-login.php';
        }

        $context->texts = self::get_texts();


        return $context;
    }

	function get_texts(){
        return array(
            'usernameoremail' =>  __('Usuari o correu'),
            'userlogin' => esc_attr(stripslashes($user_login)),
            'password' => __('Password'),
            'login'=> __('Login'),
            'resetpassword' => __('Restablir contrasenya'),
            'username'=> __('Usuari o correu electrònic'),
            'forgotpassword'=> __("No recordo la contrasenya",'mimcord'),
            'back'=>__("Enrere",'mimcord'),
            'enviatmail' => __("T'hem enviat un correu, comprova la teva safata d'entrada",'mimcord'),
            'introlost' => __("Introdueix el teu nom d'usuari o el teu correu",'mimcord'),
            'loginfailed' => __("Usuari i/o contrasenya erronis",'mimcord'),
            'userdontexist' => __("L'usuari o correu introduït no existeix. Comprova-ho si us plau",'mimcord'),
            'lostempty' => __("El nom d'usuari no pot estar buid",'mimcord'),
        );
    }

}