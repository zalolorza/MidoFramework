<?php


class BootstrapAdmin extends MidoBootstrap {


		function _init(){

				
					include_once(__DIR__.'/vendor/class.taxonomy-single-term.php');
					$custom_tax_mb = new Taxonomy_Single_Term( 'neighborhood', array( 'story'), 'select' );
					$custom_tax_mb->set( 'metabox_title', __( 'Belongs to', 'mido' ) );
					//$custom_tax_mb->set( 'context', 'normal' );
					//$custom_tax_mb->set( 'priority', 'low' );

				
		}

		function action_init(){



		}

		function action_admin_body_class($classes){

			$template = get_page_template_slug();

			if ($template) $classes .= ' ' . sanitize_title($template);

			return $classes;
		}

		function action_admin_init(){

			self::disable_new_pages();

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


};
