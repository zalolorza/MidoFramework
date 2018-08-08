<?php
namespace Mido;
/**
 * Mido Admin page class
 *
 * @package Mido
 */

class FilterPagesByTemplate {

	public function __construct()
	{
		add_action( 'restrict_manage_posts', array( $this, 'filter_dropdown' ) );
		add_filter( 'request', array( $this, 'filter_post_list' ) );

		add_filter('manage_pages_columns', array( $this, 'post_list_columns_head'));
		add_action('manage_pages_custom_column', array( $this, 'post_list_columns_content' ), 10, 2);

	}

	public function filter_dropdown()
	{
		if ( $GLOBALS['pagenow'] === 'upload.php' ) {
			return;
		}

		$template = isset( $_GET['page_template_filter'] ) ? $_GET['page_template_filter'] : "all";
		$default_title = apply_filters( 'default_page_template_title',  __( 'Default Template' ), 'meta-box' );
		?>
		<select name="page_template_filter" id="page_template_filter">
			<option value="all">All Page Templates</option>
			<option value="default" <?php echo ( $template == 'default' )? ' selected="selected" ' : "";?>><?php echo esc_html( $default_title ); ?></option>
			<?php page_template_dropdown($template); ?>
		</select>
		<?php
	}//end func


	public function filter_post_list( $vars ){

		if ( ! isset( $_GET['page_template_filter'] ) ) return $vars;


		$template = $_GET['page_template_filter'];
		if ( $template == "" || $template == 'all' ) return $vars;

		if(!is_array($template)){
			$template = array($template);
		}

		$meta_query = array('relation' => 'OR');

		foreach($template as $temp){
				$temp = trim($temp);
				$meta_query[] = array(
					array(
						'key'     => '_wp_page_template',
						'value'   => $temp,
						'compare' => '=',
					),
				);
		}

		$vars = array_merge(
			$vars,
			array(
				'meta_query' => $meta_query
			)
		);
		return $vars;

	}//end func

	# Add new column to post list
	public function post_list_columns_head( $columns )
	{
	    $columns['template'] = 'Template';
	    return $columns;
	}

	#post list column content
	public function post_list_columns_content( $column_name, $post_id )
	{
	    $post_type = 'page';

	    if($column_name == 'template')
	    {
	        $template = get_post_meta($post_id, "_wp_page_template" , true );
	        if($template)
	        {
	        	if($template == 'default')
	        	{
	        		$template_name = apply_filters( 'default_page_template_title',  __( 'Default Template' ), 'meta-box' );
	        		echo '<span title="Template file : page.php">'.$template_name.'</span>';
	        	}
	        	else
	        	{
	        		$templates = wp_get_theme()->get_page_templates( null, $post_type );

	        		if( isset( $templates[ $template ] ) )
	        		{
	        			echo '<span title="Template file : '.$template.'">'.$templates[ $template ].'</span>';
	        		}
	        		else
	        		{

	        			echo '<span style="color:red" title="This template file does not exist">'.$template.'</span>';
	        		}
	        	}

	        }
	    }
	}


}//end class
