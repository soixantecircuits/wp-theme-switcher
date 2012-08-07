<?php
/*
Plugin Name: wp-theme-switcher
Plugin URI: http://asso-ela.com/
Description: A simple plugin which what you can choose a theme for your page
Version: 1.0
Author: Soixante Circuits
Author URI: http://soixantecircuits.fr/
License: GPL
*/

//metabox creation
	$prefix = 'custom_style_field_'; //metabox prefix for the get_meta_box() datas
	$meta_box = array(
	    'id' => 'custom_style'
	    ,'title' => 'Apparence'
	    ,'page' => 'page'
	    ,'context' => 'side'
	    ,'priority' => 'high'
	    ,'fields' => array(
		      array(
	            'name' => 'Thème'
	            ,'id' => $prefix . 'select'
	            ,'type' => 'select'
	            ,'options' => array() //if you want to manualy add options, you can do it here like array("blue.css","specials/christmas.css")
	         )
	    )
	);

//Get all the css files ! o/
	$dir = "../wp-content/plugins/custom-style/css_themes";
	// Open a known directory, and proceed to read its contents
	if (is_dir($dir)) {
	    if ($dh = opendir($dir)) {
	        while (($file = readdir($dh)) !== false) {
	        	if (strlen($file)>3)
	        	{
	        		$extension = substr($file, -3, 3);
					if($extension=="css"){
		        		$name=str_replace(".css", "", $file);
		            	array_push($meta_box['fields'][0]["options"], $name);
	            	}	
	            }
	        }
	        closedir($dh);
	    }
	}

add_action('admin_menu', 'mytheme_add_box');
 
// Add meta box
function mytheme_add_box() 
{
    global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'mytheme_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}


// Callback function to show fields in meta box
function mytheme_show_box() 
{
    global $meta_box, $post;
 
    // Use nonce for verification
?>
	<input type="hidden" name="mytheme_meta_box_nonce" value="<?php wp_create_nonce(basename(__FILE__)) ?>" />
	<table class="form-table">
<?php   // get current post meta data
        $field=$meta_box['fields'][0];
        $meta = get_post_meta($post->ID, $field["id"] , true);

?>
        <tr>
			<th style="width:20%">
				<label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>
			</th>
            <td>
                <select name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>">
                	<option value="false">default</option>
                <?php foreach ($field['options'] as $option) : ?>
                	<?php if ($meta==$option) : ?>
                		<option selected="selected" value="<?php echo $option ; ?>"> <?php echo $option ; ?> </option>
                	<?php else : ?>
                		<option value="<?php echo $option ; ?>"> <?php echo $option ; ?> </option>
                	<?php endif; ?>
                <?php endforeach; ?>
               </select>
        	<td>
       	</tr>
    </table>
<?php
}

add_action('save_post', 'save_my_metadata');

function save_my_metadata()
{
    global $post,$meta_box;
    $field=$meta_box['fields'][0];
    if (!empty($field)) {
    	update_post_meta($post->ID, $field["id"], $_POST[$field["id"]]);
	}
}
?>
