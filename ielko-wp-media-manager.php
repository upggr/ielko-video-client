<?php
/*
Plugin Name: Ielko WP Media Manager
Plugin URI: http://ielko.com/support/ielko-wp-media-manager/
Description: Media manager for roku and tvOS clients
Version: 1.4
Author: Ioannis Kokkinis
Author URI: http://ielko.com
License: Commercial
*/

/* You can steal this, but note that the client generators are in a different server and have lot more proprietary code than here :p  */

function ielko_wp_media_manager() {

	$labels = array(
		'name'                  => _x( 'Media Items', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Media Item', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Ielko Media', 'text_domain' ),
		'name_admin_bar'        => __( 'Ielko Media', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Media Item', 'text_domain' ),
		'description'           => __( 'Media (video or audio)', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'custom-fields','thumbnail' ),
		'taxonomies'            => array( 'category'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'media_item', $args );

}

function replace_featured_image_box()
{
    remove_meta_box( 'postimagediv', 'media_item', 'side' );
    add_meta_box('postimagediv', __('Media Image'), 'post_thumbnail_meta_box', 'media_item', 'normal', 'high');
}

function custom_admin_post_thumbnail_html( $content ) {
    return $content = str_replace( __( 'Set featured image' ), __( 'Set the media image thumbnail' ), $content);
}
add_filter( 'admin_post_thumbnail_html', 'custom_admin_post_thumbnail_html' );


function wpse_98269_script() {
    if (isset($GLOBALS['post'])) {
        $post_type = get_post_type($GLOBALS['post']);
        if (post_type_supports($post_type, 'custom-fields')) {
            ?>
                <script>
                    var $metakeyinput = jQuery('#metakeyinput'),
                        $metakeyselect = jQuery('#metakeyselect');
                    if ($metakeyinput.length && ( ! $metakeyinput.hasClass('hide-if-js'))) {
                        $metakeyinput.addClass('hide-if-js'); // Using WP admin class.
                        $metakeyselect = jQuery('<select id="metakeyselect" name="metakeyselect">').appendTo('#newmetaleft');
                        $metakeyselect.append('<option value="#NONE#">— Select —</option>');
                    }
                    if (jQuery("[value='media_url']").length < 1) {
                        $metakeyselect.append("<option value='media_url'>Media URL</option>");
                    }
                    if (jQuery("[value='media_thumb']").length < 1) {
                        $metakeyselect.append("<option value='media_thumb'>Media Thumbnail</option>");
                    }
                </script>

            <?php

        }

    }

}



//meta box
function media_meta_box( $post ){
    $id = 'media_info';
    $title = 'Media Information';
    $callback = create_work_meta;
    $screen = 'media_item';
    $context = 'normal';
    $priority = 'low';
    add_meta_box( $id, $title, $callback, $screen, $context, $priority );
}

function checkactive($thevar,$thecheck) {
  if ($thevar == $thecheck) {
    return 'checked';
  }
  else if ($thevar =! $thecheck) {
    return '';
  }
}


function create_work_meta( $post ){
    wp_nonce_field( basename( __FILE__ ), 'media_meta_box_nonce' );
    $media_url = get_post_meta( $post->ID, 'media_url', true );
    $media_description = get_post_meta( $post->ID, 'media_description', true );
    $media_active = get_post_meta( $post->ID, 'media_active', true );
    $media_type = get_post_meta( $post->ID, 'media_type', true );
    $media_qty = get_post_meta( $post->ID, 'media_qty', true );
    echo '<div>
        <p>
            <label for=\'media_url\'>Media URL (could be the url of a  video you uploaded in the media library, a youtube link, an m3u8 link etc..):</label>
            <br />
            <input type=\'url\' name=\'media_url\' value=\'' . $media_url .'\' required style=\'width:97%\' />
        </p>

        <p>
            <label for=\'media_description\'>Media Description:</label>
            <br />
            <input type=\'text\' name=\'media_description\' value=\''. $media_description . '\' style=\'width:97%\' />
        </p>

        <p>
            <label for=\'media_active\'>Is it Active? :</label>
            <br />
            <input type=\'radio\' name=\'media_active\' value=\'1\' '.checkactive($media_active,1).'> Yes<br>
            <input type=\'radio\' name=\'media_active\' value=\'0\' '.checkactive($media_active,0).'> No<br>
        </p>

        <p>
            <label for=\'media_type\'>Media Type :</label>
            <br />
            <input type=\'radio\' name=\'media_type\' value=\'1\' '.checkactive($media_type,1).'> VIDEO<br>
            <input type=\'radio\' name=\'media_type\' value=\'0\' '.checkactive($media_type,0).'> AUDIO<br>
        </p>
        <p>
            <label for=\'media_type\'>Media Quality (for video) :</label>
            <br />
            <input type=\'radio\' name=\'media_qty\' value=\'1\' '.checkactive($media_qty,1).'> SD <br>
            <input type=\'radio\' name=\'media_qty\' value=\'0\' '.checkactive($media_qty,0).'> HD <br>
        </p>

    </div>';
}




function save_media_meta( $post_id ){
    if( !isset( $_POST['media_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['media_meta_box_nonce'], basename( __FILE__ ) ) ){
            return;
    }
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }
    if( !current_user_can( 'edit_post', $post_id ) ){
        return;
    } elseif ( !current_user_can ( 'edit_page', $post_id) ){
        return;
    }
    if( isset( $_REQUEST['media_url'] ) ){
        update_post_meta( $post_id, 'media_url', sanitize_text_field( $_POST['media_url'] ) );
    }

    if( isset( $_REQUEST['media_description'] ) ){
        update_post_meta( $post_id, 'media_description', sanitize_text_field( $_POST['media_description'] ) );
    }
    if( isset( $_REQUEST['media_active'] ) ){
        update_post_meta( $post_id, 'media_active', sanitize_text_field( $_POST['media_active'] ) );
    }
    if( isset( $_REQUEST['media_type'] ) ){
        update_post_meta( $post_id, 'media_type', sanitize_text_field( $_POST['media_type'] ) );
    }
    if( isset( $_REQUEST['media_qty'] ) ){
        update_post_meta( $post_id, 'media_qty', sanitize_text_field( $_POST['media_qty'] ) );
    }
}


// meta box

function rokuXML(){
        add_feed('roku', 'rokuXMLFunc');
}

function rokuXMLFunc(){
$postCount = 1000;
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
echo '<categories>';

$cats = get_categories();
foreach ($cats as $cat) {
          $thecatid = $cat->term_id;
          $thecategory = $cat->name;
          $thecategorydesc = $cat->description;
          $thecategoryimg = z_taxonomy_image_url($cat->term_id);
if ($thecategory != 'Uncategorized') {

					query_posts("cat=$thecatid&posts_per_page=100&post_type='media_item");

          echo '<category title="'.$thecategory.'" description="'.$thecategorydesc.'" sd_img="'.$thecategoryimg.'" hd_img="'.$thecategoryimg.'">';
        echo '<feed title="'.$thecategory.'" description="'.$thecategorydesc.'" sd_img="'.$thecategoryimg.'" hd_img="'.$thecategoryimg.'">';
					if (have_posts()) : while (have_posts()) : the_post();
          $thetitle = get_the_title();
          $theurl = get_post_meta(get_the_ID(), 'media_url', true);
          $thedescription = get_post_meta(get_the_ID(), 'media_description', true);
          $theimg =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'single-post-thumbnail' );
					$theimg =  $theimg[0];
          $thefrmt = 'hls';
          $thestrg = 'full-adaptation';
          $thequality = get_post_meta(get_the_ID(), 'media_qty', true);
          if ($thequality == 1) {
            $thequality_ = 'SD';
          }
          else if ($thequality == 0) {
            $thequality_ = 'HD';
          }
          $thebitrate = '0';

          if (strpos($theurl, 'm3u8') !== false) {
          echo '<item sdImg="'.$theimg.'" hdImg="'.$theimg.'">
          <title>'.$thetitle.'</title>
          <description>'.$thedescription.'</description>
          <streamFormat>'.$thefrmt.'</streamFormat>
          <switchingStrategy>'.$thestrg.'</switchingStrategy>
          <media>
          <streamFormat>'.$thefrmt.'</streamFormat>
          <streamQuality>'.$thequality_.'</streamQuality>
          <streamBitrate>'.$thebitrate.'</streamBitrate>
          <streamUrl>'.$theurl.'</streamUrl>
          </media>
          </item>';
          }
          endwhile;
          endif;
          echo '</feed>';
          echo '</category>';
        }
         }
echo '</categories>';

}
function rokuXMLcats(){
        add_feed('roku_cats', 'rokuXMLcats_f');
}

function rokuXMLcats_f(){
$postCount = 1000;
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
echo '<categories>';
  echo '<category title="LIVE TV" description="LIVE TV STATIONS" sd_img="" hd_img="">';
$cats = get_categories();
foreach ($cats as $cat) {
          $thecatid = $cat->term_id;
          $thecategory = $cat->name;
          $thecategorydesc = $cat->description;
          $thecategoryimg = z_taxonomy_image_url($cat->term_id);
if ($thecategory != 'Uncategorized') {

					query_posts("cat=$thecatid&posts_per_page=100&post_type='media_item");


					echo '<categoryLeaf title="'.$thecategory.'" description="'.$thecategorydesc.'" feed="'.get_site_url().'/?feed=roku_by_cat&amp;cat='.$thecategory.'"/>';

        }
         }
				 echo '</category>';
echo '</categories>';

}




function rokuXMLbycat(){
        add_feed('roku_by_cat', 'rokuXMLbycat_f');
}

function rokuXMLbycat_f(){
$postCount = 1000;
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
echo '<feed>';
$cats = get_categories();
foreach ($cats as $cat) {
          $thecatid = $cat->term_id;
          $thecategory = $cat->name;
          $thecategorydesc = $cat->description;
          $thecategoryimg = z_taxonomy_image_url($cat->term_id);
if ($thecategory == $_GET['cat']) {

					query_posts("cat=$thecatid&posts_per_page=100&post_type=media_item");

					if (have_posts()) :
						$thePosts = query_posts("cat=$thecatid&posts_per_page=100&post_type=media_item");
						global $wp_query;
						$noposts= $wp_query->found_posts;
						echo '<resultLength>'.$noposts.'</resultLength>';
						echo '<endIndex>'.$noposts.'</endIndex>';
					while (have_posts()) : the_post();
          $thetitle = get_the_title();
          $theurl = get_post_meta(get_the_ID(), 'media_url', true);
          $thedescription = get_post_meta(get_the_ID(), 'media_description', true);
          $theimg =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'single-post-thumbnail' );
					$theimg =  $theimg[0];
          $thefrmt = 'hls';
          $thestrg = 'full-adaptation';
          $thequality = get_post_meta(get_the_ID(), 'media_qty', true);
          if ($thequality == 1) {
            $thequality_ = 'SD';
          }
          else if ($thequality == 0) {
            $thequality_ = 'HD';
          }
          $thebitrate = '0';

          if (strpos($theurl, 'm3u8') !== false) {
          echo '<item sdImg="'.$theimg.'" hdImg="'.$theimg.'">
          <title>'.$thetitle.'</title>
					<contentId>'.$theimg.'</contentId>
					<contentType>Live TV</contentType>
					<contentQuality>'.$thequality_.'</contentQuality>
          <description>'.$thedescription.'</description>
          <streamFormat>'.$thefrmt.'</streamFormat>
          <media>
          <streamFormat>'.$thefrmt.'</streamFormat>
          <streamQuality>'.$thequality_.'</streamQuality>
          <streamBitrate>'.$thebitrate.'</streamBitrate>
          <streamUrl>'.$theurl.'</streamUrl>
          </media>
					<synopsis>'.$thedescription.'</synopsis>
					<genres>Live TV</genres>
					<runtime>Live</runtime>
          </item>';
          }
          endwhile;
          endif;

        }
         }
echo '</feed>';

}




function tvosXML(){
        add_feed('tvos', 'tvosXMLFunc');
}

function tvosXMLFunc(){
$postCount = 1000;
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: text');
echo 'var Template = function() { return `<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
echo '<document><catalogTemplate><banner><title>Greek TV by UPG.GR</title></banner><list>';

$cats = get_categories();
foreach ($cats as $cat) {
          $thecatid = $cat->term_id;
          $thecategory = $cat->name;
          $thecategorydesc = $cat->description;
if ($thecategory != 'Uncategorized') {
query_posts("cat=$thecatid&posts_per_page=100&post_type='media_item");

echo '<section><listItemLockup><title>'.$thecategory.'</title><decorationLabel>'.$thecategorydesc.'</decorationLabel><relatedContent><grid><section>';
					if (have_posts()) : while (have_posts()) : the_post();
          $theurl = get_post_meta(get_the_ID(), 'media_url', true);
					$theimg =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'single-post-thumbnail' );
					$theimg =  $theimg[0];
  if (strpos($theurl, 'm3u8') !== false) {
          echo '<lockup videoURL="'.$theurl.'"><img src="'.$theimg.'" width="250" height="150" /></lockup>';
        }
          endwhile;
          endif;
          echo '</section></grid></relatedContent></listItemLockup></section>';
        }
         }
echo '</list></catalogTemplate></document>`}';

}



function android1XML(){
        add_feed('android1', 'android1XMLFunc');
}

function android1XMLFunc(){
$postCount = 1000;
$posts = query_posts('showposts=' . $postCount);
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
echo '<channels updated="10-07-2017">';

$cats = get_categories();
foreach ($cats as $cat) {

          $thecatid = $cat->term_id;
          $thecategory = $cat->name;
          $thecategorydesc = $cat->description;
if ($thecategory != 'Uncategorized') {
query_posts("cat=$thecatid&posts_per_page=100&post_type='media_item");
					if (have_posts()) : while (have_posts()) : the_post();
					$thetitle = get_the_title();
					$thedescription = get_post_meta(get_the_ID(), 'media_description', true);
          $theurl = get_post_meta(get_the_ID(), 'media_url', true);
					$theimg =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'single-post-thumbnail' );
					$theimg =  $theimg[0];
          echo '<channel enable=""><name>'.$thetitle.'</name><group>'.$thecategory.'</group><logo>'.$theimg.'</logo><url>'.$theurl.'</url><info>'.$thedescription.'</info></channel>';
          endwhile;
          endif;
        }
         }
echo '</channels>';

}






function inject_res($filename,$injector1,$injector2) {
	$extension_pos = strrpos($filename, '.');
	$thenew = substr($filename, 0, $extension_pos) . '-'.$injector1 .'x'.$injector2 . substr($filename, $extension_pos);
	return $thenew;
}



if ( ! class_exists( 'WC_CPInstallCheck' ) ) {
  class WC_CPInstallCheck {
		static function install() {
			if ( !in_array( 'categories-images/categories-images.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
				deactivate_plugins(__FILE__);
				$error_message = __('This plugin requires <a target="_blank" href="https://wordpress.org/plugins/categories-images/">Categories Images</a> &amp; plugin to be active!', 'categories-images');
				die($error_message);

			}
		}
	}
}
register_activation_hook( __FILE__, array('WC_CPInstallCheck', 'install') );




function ivc_add_admin_menu(  ) {
add_submenu_page('edit.php?post_type=media_item', __('Settings & Output','menu-test'), __('Settings & Output','menu-test'), 'manage_options', 'ielko_wp_media_manager', 'ivc_options_page' );
//add_menu_page( 'Ielko Settings', 'Ielko Settings', 'manage_options', 'ielko_wp_media_manager', 'ivc_options_page' );

}


function ivc_settings_init(  ) {

	register_setting( 'pluginPage', 'ivc_settings' );

	add_settings_section(
		'ivc_pluginPage_section',
		__( 'Instructions and feeds', 'wordpress' ),
		'ivc_settings_section_intro',
		'pluginPage'
	);

  add_settings_field(
    'ivc_text_field_1',
    __( 'Name your Application (ROKU & TVOS)', 'wordpress' ),
    'ivc_text_field_1_render',
    'pluginPage',
    'ivc_pluginPage_section'
  );

	add_settings_field(
    'ivc_text_field_2',
    __( 'Subtitle/slogan (ROKU)', 'wordpress' ),
    'ivc_text_field_2_render',
    'pluginPage',
    'ivc_pluginPage_section'
  );

	add_settings_field(
		'ivc_image_field_0',
		__( 'ROKU Icon (336 x 210)', 'wordpress' ),
		'ivc_image_field_0_render',
		'pluginPage',
		'ivc_pluginPage_section'
	);
add_settings_field(
	'ivc_image_field_1',
	__( 'ROKU Side (108 x 69)', 'wordpress' ),
	'ivc_image_field_1_render',
	'pluginPage',
	'ivc_pluginPage_section'
);
add_settings_field(
'ivc_image_field_2',
__( 'ROKU Overhang (234 x 104)', 'wordpress' ),
'ivc_image_field_2_render',
'pluginPage',
'ivc_pluginPage_section'
);

	add_settings_field(
		'ivc_image_field_3',
		__( 'ROKU Splash (1280 x 720)', 'wordpress' ),
		'ivc_image_field_3_render',
		'pluginPage',
		'ivc_pluginPage_section'
	);

	add_settings_field(
		'ivc_image_field_4',
		__( 'ROKU store (540 x 405)', 'wordpress' ),
		'ivc_image_field_4_render',
		'pluginPage',
		'ivc_pluginPage_section'
	);

	add_settings_field(
		'ivc_checkbox_field_0',
		__( 'Enable media checker (BETA) - Will check every 10 minutes for dead media and mark them as inactive', 'wordpress' ),
		'ivc_checkbox_field_0_render',
		'pluginPage',
		'ivc_pluginPage_section'
	);

  add_settings_field(
		'ivc_checkbox_field_1',
		__( 'Enable mediarazzi Ad network on your videos (BETA)', 'wordpress' ),
		'ivc_checkbox_field_1_render',
		'pluginPage',
		'ivc_pluginPage_section'
	);

}


function ivc_checkbox_field_0_render(  ) {
$options = get_option( 'ivc_settings' );
if (isset($options['ivc_checkbox_field_0'])) { ?>
<input type='checkbox' name='ivc_settings[ivc_checkbox_field_0]' <?php checked( $options['ivc_checkbox_field_0'], 1 ); ?> value='1'>
<?php } else { ?>
<input type='checkbox' name='ivc_settings[ivc_checkbox_field_0]' value='1'>
<?php } ?>
<?php
}


function ivc_checkbox_field_1_render(  ) {
	$options = get_option( 'ivc_settings' );
	if (isset($options['ivc_checkbox_field_1'])) { ?>
	<input type='checkbox' name='ivc_settings[ivc_checkbox_field_1]' <?php checked( $options['ivc_checkbox_field_1'], 1 ); ?> value='1'>
	<?php } else { ?>
	<input type='checkbox' name='ivc_settings[ivc_checkbox_field_1]' value='1'>
	<?php } ?>
	<?php
}

function ivc_text_field_1_render(  ) {

	$options = get_option( 'ivc_settings' );
	if (isset($options['ivc_text_field_1'])) { ?>
	<input type='text' name='ivc_settings[ivc_text_field_1]' value='<?php echo $options['ivc_text_field_1']; ?>' style='width:50%'>
	<?php } else { ?>
	<input type='text' name='ivc_settings[ivc_text_field_1]' value='' style='width:50%'>
	<?php } ?>
	<?php
}

function ivc_text_field_2_render(  ) {

	$options = get_option( 'ivc_settings' );
	if (isset($options['ivc_text_field_2'])) { ?>
	<input type='text' name='ivc_settings[ivc_text_field_2]' value='<?php echo $options['ivc_text_field_2']; ?>' style='width:50%'>
	<?php } else { ?>
	<input type='text' name='ivc_settings[ivc_text_field_2]' value='' style='width:50%'>
	<?php } ?>
	<?php
}
function ivc_image_field_0_render(  ) {

	$options = get_option( 'ivc_settings' );
	?>
	 <input type="text" name="ivc_settings[ivc_image_field_0]" id="image_url" class="regular-text" value="<?php echo $options['ivc_image_field_0']; ?>">
	 <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
	<?php

}

function ivc_image_field_1_render(  ) {

	$options = get_option( 'ivc_settings' );
	?>
	 <input type="text" name="ivc_settings[ivc_image_field_1]" id="image_url1" class="regular-text" value="<?php echo $options['ivc_image_field_1']; ?>">
	 <input type="button" name="upload-btn1" id="upload-btn1" class="button-secondary" value="Upload Image">
	<?php

}

function ivc_image_field_2_render(  ) {

	$options = get_option( 'ivc_settings' );
	?>
	 <input type="text" name="ivc_settings[ivc_image_field_2]" id="image_url2" class="regular-text" value="<?php echo $options['ivc_image_field_2']; ?>">
	 <input type="button" name="upload-btn2" id="upload-btn2" class="button-secondary" value="Upload Image">
	<?php

}

function ivc_image_field_3_render(  ) {

	$options = get_option( 'ivc_settings' );
	?>
	 <input type="text" name="ivc_settings[ivc_image_field_3]" id="image_url3" class="regular-text" value="<?php echo $options['ivc_image_field_3']; ?>">
	 <input type="button" name="upload-btn3" id="upload-btn3" class="button-secondary" value="Upload Image">
	<?php

}

function ivc_image_field_4_render(  ) {

	$options = get_option( 'ivc_settings' );
	?>
	 <input type="text" name="ivc_settings[ivc_image_field_4]" id="image_url4" class="regular-text" value="<?php echo $options['ivc_image_field_4']; ?>">
	 <input type="button" name="upload-btn4" id="upload-btn4" class="button-secondary" value="Upload Image">
	<?php

}



function ivc_settings_section_intro(  ) {
$options = get_option( 'ivc_settings' );
	echo __( 'Thank you for installing the IELKO plugin.
  <br />
	  <br />
		<p>
		Note that this is still in beta mode. The roku app should work ok provided you complete the fields bellow. the tvos app, will compile fine on your mac and will run fine on the simulator or your tvos device as soon as you press play, but I have not created the logic to incorporate the media assets yet, so you will just see some images (splash screens, icons) from an existing app I am using.

		</p>
		<br />
		  <br />
  <b>STEP 1</b> : Add media sources using the Ielko Media menu in the menu, as if you were creating a new wordpress post. Complete the title of the media (for example "my awesome video"), and at least the source (for example http://mydomain.com/my_awesome_video.mp4). You will need to define a category for each item you create, and you can upload an image for the categories using the interface.
    <br />
    <br />
		<b>STEP 2</b> : Complete the fields bellow on this page.
			<br />
			<br />
			<b>STEP 3</b> : 	<span id="roku_app" fname="'.hash('ripemd160', get_site_url()).'" url="'.get_site_url().'" title="'.$options['ivc_text_field_1'].'" subtitle="'.$options['ivc_text_field_2'].'" version="1" mm_icon_focus_hd="'.$options['ivc_image_field_0'].'"
				mm_icon_focus_sd="'.$options['ivc_image_field_0'].'"
				mm_icon_side_hd="'.$options['ivc_image_field_1'].'"
				mm_icon_side_sd="'.$options['ivc_image_field_1'].'"
				overhang_hd="'.$options['ivc_image_field_2'].'"
				overhang_sd="'.$options['ivc_image_field_2'].'"
				splash_screen_sd="'.$options['ivc_image_field_3'].'"
				splash_screen_hd="'.$options['ivc_image_field_3'].'"
				splash_screen_fhd="'.$options['ivc_image_field_3'].'"
				store_fhd="'.$options['ivc_image_field_4'].'"
				store_hd="'.$options['ivc_image_field_4'].'"
				store_sd="'.$options['ivc_image_field_4'].'"
				>Download your ROKU app by clicking here  (Watch out for your popup blocker)</span>.
				<br />
				<br />
				<b>STEP 4</b> : 	<span id="tvos_app" fname="'.hash('ripemd160', get_site_url()).'" url="'.get_site_url().'" title="'.$options['ivc_text_field_1'].'" subtitle="'.$options['ivc_text_field_2'].'" version="1" mm_icon_focus_hd="'.$options['ivc_image_field_0'].'"
					mm_icon_focus_sd="'.$options['ivc_image_field_0'].'"
					mm_icon_side_hd="'.$options['ivc_image_field_1'].'"
					mm_icon_side_sd="'.$options['ivc_image_field_1'].'"
					overhang_hd="'.$options['ivc_image_field_2'].'"
					overhang_sd="'.$options['ivc_image_field_2'].'"
					splash_screen_sd="'.$options['ivc_image_field_3'].'"
					splash_screen_hd="'.$options['ivc_image_field_3'].'"
					splash_screen_fhd="'.$options['ivc_image_field_3'].'"
					store_fhd="'.$options['ivc_image_field_4'].'"
					store_hd="'.$options['ivc_image_field_4'].'"
					store_sd="'.$options['ivc_image_field_4'].'"
					>Download your TVOS app by clicking here  (Watch out for your popup blocker)</span>.
					<br />
					<br />
  Your ROKU feed is accessible from <a href="'.get_site_url().'/?feed=roku">'.get_site_url().'/?feed=roku</a><br />
  Your TVOS feed is accessible from <a href="'.get_site_url().'/?feed=tvos">'.get_site_url().'/?feed=tvos</a><br />
	Your Android (Variant 1) feed is accessible from <a href="'.get_site_url().'/?feed=android1">'.get_site_url().'/?feed=android1</a><br />
	<br />
    ', 'wordpress' );
}


//<form action="http://factory.upg.gr/index.php" method="post">
//<input type="hidden" value="test" name="test" />
//<button type="submit" >Download Roku app</button>
//</form>
function ivc_options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2>Ielko Instructions and basic settings</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}


function file_replace() {
	$upload_dir = wp_upload_dir();
	$js_dirname = $upload_dir['basedir'] . '/' . 'js';
	if(!file_exists($js_dirname)) wp_mkdir_p($js_dirname);

    $plugin_dir = plugin_dir_path( __FILE__ ) . 'js/application.js';
		$uploads_dir = km_get_wordpress_uploads_directory_path() . '/js/application.js';
    if (!copy($plugin_dir, $uploads_dir)) {
        echo "failed to copy $plugin_dir to $uploads_dir...\n";
    }

		$plugin_dir = plugin_dir_path( __FILE__ ) . 'js/Presenter.js';
		$uploads_dir = km_get_wordpress_uploads_directory_path() . '/js/Presenter.js';
    if (!copy($plugin_dir, $uploads_dir)) {
        echo "failed to copy $plugin_dir to $uploads_dir...\n";
    }

		$plugin_dir = plugin_dir_path( __FILE__ ) . 'js/ResourceLoader.js';
		$uploads_dir = km_get_wordpress_uploads_directory_path() . '/js/ResourceLoader.js';
    if (!copy($plugin_dir, $uploads_dir)) {
        echo "failed to copy $plugin_dir to $uploads_dir...\n";
    }

		$path_to_file = km_get_wordpress_uploads_directory_path() . '/js/application.js';
		$file_contents = file_get_contents($path_to_file);
		$file_contents = str_replace("-TVOSFEED-",get_site_url().'/?feed=tvos',$file_contents);
		file_put_contents($path_to_file,$file_contents);

}

function km_get_wordpress_uploads_directory_path() {
	$upload_dir = wp_upload_dir();
	return trailingslashit( $upload_dir['basedir'] );
}



function load_wp_media_files() {
	wp_enqueue_script( 'ielko',plugin_dir_url( __FILE__ ) . '/js/ielko.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_media();
}
add_action( 'save_post_media_item', 'save_media_meta', 10, 2 );
add_action( 'init', 'ielko_wp_media_manager', 0 );
add_action( 'init', 'file_replace' );
add_action('do_meta_boxes', 'replace_featured_image_box');
add_action( 'add_meta_boxes_media_item', 'media_meta_box' );
add_action('init', 'rokuXML');
add_action('init', 'rokuXMLcats');
add_action('init', 'rokuXMLbycat');
add_action('init', 'tvosXML');
add_action('init', 'android1XML');
add_action( 'admin_menu', 'ivc_add_admin_menu' );
add_action( 'admin_init', 'ivc_settings_init' );
add_theme_support( 'post-thumbnails' );
add_image_size( 'ielko_focus_hd', 336, 210, false );
add_image_size( 'ielko_focus_sd', 248, 140, false );
add_image_size( 'ielko_side_hd', 108, 69, false );
add_image_size( 'ielko_side_sd', 80, 46, false );
add_image_size( 'ielko_overhang_hd', 234, 104, false );
add_image_size( 'ielko_overhang_sd', 131, 58, false );
add_image_size( 'ielko_splash_hd', 1280, 720, false );
add_image_size( 'ielko_splash_sd', 740, 480, false );
add_image_size( 'ielko_store_sd', 290, 218, false );
add_image_size( 'ielko_store_sd', 214, 144, false );

add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );



?>