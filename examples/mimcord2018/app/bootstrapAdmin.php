<?php


class BootstrapAdmin extends MidoBootstrap {


		function _init(){
			
					ImagesManager::add_admin_actions();
					Mido::filterPagesByTemplate();
					new JPB_User_Caps();
					
		}

		function action_init(){
		
			if (is_user_logged_in() && ! current_user_can( 'edit_posts' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}

	

		function action_admin_body_class($classes){

			$template = get_page_template_slug();

			if ($template) $classes .= ' ' . sanitize_title($template);

			return $classes;
		}

		function action_admin_init(){

			self::disable_new_pages();
			self::disable_editor_on_pages();

		}
		function action_admin_head_99(){
				global $post;
				if($post){
						remove_meta_box('icl_div_config',$post->posttype,'normal');
				}
		}

		function action_admin_menu(){



		}


		/*
		*
		* Disable WPML metabox
		*
		*/

		function action_admin_head(){

			global $post;
			if(isset($post->posttype)){

				remove_meta_box('icl_div_config', $post->posttype, 'normal');

			}

		}


		/*
		*
		* Disable New Pages Button
		*
		*/

		public function disable_new_pages($toRoles = array()) {

			        //if(current_user_can('administrator')) return false;

			        $user = wp_get_current_user();

			        /*if ( !in_array( $user->roles, (array) $toRoles ) ) {
			            return false;
			        }*/

			        //disable new pages
			        add_action('admin_menu', function(){
			                global $submenu;
			                unset($submenu['edit.php?post_type=page'][10]);
			                if (isset($_GET['post_type']) && $_GET['post_type'] == 'page') {
			                    echo '<style type="text/css">
			                .page-title-action:not(.page-title-action-avoidremove) { display:none; }
			                </style>';
			                }
			        });

			        //hide page buttons
			        add_action('admin_head', function(){
			                global $current_screen;
			                if (!isset($current_screen)) return;
			                if (!isset($current_screen->id)) return;
			                if ($current_screen->id == 'page') {
			                    echo '<style>.page-title-action:not(.page-title-action-avoidremove){display: none;}</style>';
			                }
			        });

			}
			
			public function disable_editor_on_pages() {
				// Get the Post ID.
				$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
				if( !isset( $post_id ) ) return;
				
				// Hide the editor on a page with a specific page template
				// Get the name of the Page Template file.
				$template_file = get_post_meta($post_id, '_wp_page_template', true);
				if($template_file == 'home.twig'){ // the filename of the page template
					remove_post_type_support('page', 'editor');
				}
			}



};
