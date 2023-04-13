<?php
/**
 * Plugin Name: FFG Custom Info
 * Plugin URI: https://www.firefightergarage.com
 * Description: Display custom admin + additional info in page or post
 * Version: 0.1
 * Text Domain: ffg-idfcontroller
 * Author: Corvette Hunt
 * Author URI: https://www.firefightergarage.com
 */


//--------------------------
//Admin enhancements
//--------------------------

define('APA_SWCR_META_FIELD_KEY', '_wordcount');

// Añadir columnas
add_filter( 'manage_posts_columns', 'lab_columns' );
add_filter( 'manage_pages_columns', 'lab_columns' );

function lab_columns( $columns ) {
  $columns['image'] = __( 'Feature Image' );
  $columns['contentsize'] = __( 'Words in Article' );
  $columns['lastupdate'] = __( 'Last Updated' );
  $columns['ffgupdate'] = __( 'FFG Ready' );
  return $columns;
}

// Añadir contenido a las columnas
add_action( 'manage_posts_custom_column', 'lab_posts_column', 10, 2);
add_action( 'manage_pages_custom_column', 'lab_posts_column', 10, 2);

function lab_check_position($haystack,$needle,$alert,$negate="")
{
		if (!$negate && strpos(" ".$haystack,$needle)>0) {
			echo '<span style="color:red">'.$alert.'</span><br>';
		}

		if ($negate && !strpos(" ".$haystack,$needle)>0) {
			echo '<span style="color:red">'.$alert.'</span><br>';
		}
	
}

function lab_posts_column( $column, $post_id ) {

  // Check if [ffg-chrisdisclaimer /] exists
  if ( 'ffgupdate' === $column ) {
	  $content=get_the_content($post_id);
	  $title=get_the_title($post_id);
	  
	  // Positive Checks
	  lab_check_position($content,"ir-na.amazon-adsystem.com","PixelError");
	  lab_check_position($content,"lazy-loaded","LazyLoad");
	  lab_check_position($content,"underline","Underline");
	  lab_check_position($content,"heck the price <","CTA");
	  lab_check_position($content,"data-slate","data-slate");
	  lab_check_position($content,"data-pm","data-pm");
	  lab_check_position($content,"As an Amazon Associate I earn from qualifying purchases","OldDisclaimer");
	  lab_check_position($content,"googleon","GoogleOn");
	  lab_check_position($content,"wp:tadv/classic-paragraph","OldCodeParagraphs");
	  lab_check_position($content,"<h4","CheckH4");
	  lab_check_position($content,"for our situation","LameLanguage - for our situation");
	  lab_check_position($content,"or your circumstances","LameLanguage - or your circumstances");
	  lab_check_position($content,"due diligence","LameLanguage - due diligence");
	  lab_check_position($content,"amazon-adsystem.com","OldAmazonLinks");
	  lab_check_position($content,"if you explicitly consent to our terms","RepetitiveDisclaimer");

	  // Negative Checks
	  lab_check_position($content,"[ez-toc","NoTableContents",1);
	  lab_check_position($content,"<img","NoImage",1);
	  lab_check_position($content,"[ffg-chrisdisclaimer /]","MissingDisclaimer",1);
	  
	  // Advanced Checks
	  
	  if ((strpos($content,"[amalinkspro")>0 && strpos($content,"[amalinkspro_table")==0 && strpos($content,"showcase")==0) || strpos($content,"amazon-adsystem.com")>0)
	  {
		  preg_match_all("/\/dp\/([A-Z0-9]{9,10})/",$content,$result);
		  $result[1]=array_unique($result[1]);
	  	foreach($result[1] as $data)
	  	{
		  	echo $data."<br>";
	  	}
		  
	  }
	  if (!strpos(" ".strtolower($title),"best")>0 && !strpos($content,"best-")>0)
	  {
		  echo '<span style="color:red">NoLinkToBest</span><br>';
	  }
	  
	  if (strpos($content,"2018")>0 && !strpos($content,"/2018/")>0)
	  {
		  echo '<span style="color:red">2018</span><br>';
	  }

	  if (strpos($content,"2019")>0 && !strpos($content,"/2019/")>0)
	  {
		  echo '<span style="color:red">2019</span><br>';
	  }
	  
	  if (strpos($content,"2020")>0 && !strpos($content,"/2020/")>0)
	  {
		  echo '<span style="color:red">2020</span><br>';
	  }
	  
	  if (strpos($content,"2021")>0 && !strpos($content,"/2021/")>0)
	  {
		  echo '<span style="color:red">2021</span><br>';
	  }
	  
	  if (strpos($content,"2022")>0 && !strpos($content,"/2022/")>0)
	  {
		  echo '<span style="color:red">2022</span><br>';
	  }	  
	  
	  	if (strpos($content,"[amalinkspro")>0 && strpos($content,"[amalinkspro_table")==0 && strpos($content,"showcase")==0)
		{
			echo '<span style="color:red">NoAmalinkTable</span><br>';
		}
	   
		$x=wp_get_post_categories($post_id);
		if ($x[0]==1) {
			echo '<span style="color:red">NoCategory</span><br>';
		}	  
		$desc = RankMath\Post::get_meta( 'description', $post_id );

	    if (strlen($desc)>160) {
			echo '<span style="color:red">Long Description</span><br>';
	    }

	    if (strlen($desc)<50 && strlen($desc)>0) {
			echo '<span style="color:red">Short Description</span><br>';
	    }	  
			
	  	if (strpos($content,"<img")>0 && (preg_match("/<img(?!.*alt=).*?>/",$content) || strpos($content,' alt=""')>0))
		{
			echo '<span style="color:red">Image w/No Alt</span><br>';
		}
	  	if (strpos($content,"<img")>0 && (preg_match("/<img.*?alt=.*?alt=.*?>/",str_replace(">",">\n",$content)) ))
		{
			echo '<span style="color:red">Image Double Alt</span><br>';
		}
	  
		if (substr_count(" ".$content,"also")>3) {
				echo '<span style="color:red">Adverbs</span><br>';
		}
  
		if (strpos(" ".$content,"by James")>0 && !strpos(" ".$content,"James is an actual person, but ")>0) {
				echo '<span style="color:red">JamesDisclaimer</span><br>';
		}	

	    if (!has_post_thumbnail($post_id)) {
			echo '<span style="color:red">NoThumbnail</span><br>';
		}

	  if (date("Ymd")-the_modified_date("Ymd","","",false)>600) {
		    echo '<span style="color:red">Grammar Review</span><br>';
	  }
  }
	
  // Image column
  if ( 'image' === $column ) {
	echo get_the_post_thumbnail( $post_id, array(80, 80) );
  }
	// Content Size
  if ( 'contentsize' === $column ) {
    $page = get_post_meta($post_id,APA_SWCR_META_FIELD_KEY);
	echo $page[0];
  }
	// Last Modified
	  if ( 'lastupdate' === $column ) {
		echo the_modified_date();
		echo "<br>";
		echo the_modified_time();
	  }
}

// Register the columns as sortable
add_filter( 'manage_edit-post_sortable_columns', 'lab_sortable_last_modified_column' );
add_filter( 'manage_edit-page_sortable_columns', 'lab_sortable_last_modified_column' );

// Allow that content to be sortable by modified time information
function lab_sortable_last_modified_column( $columns ) {
  $columns['lastupdate'] = 'modified';
  $columns['contentsize'] = APA_SWCR_META_FIELD_KEY;	
  return $columns;
}

// Create custom filter admin
function lab_admin_posts_filter_restrict_manage_posts(){
	if (isset($_GET['NP_FIELD_VALUE'])) {
		$current_v =$_GET['NP_FIELD_VALUE'];
	}
	print('<select name="NP_FIELD_VALUE">
            <option value="">-</option>
		    <option value="NP" '.($current_v=="NP"?"selected":"").' >Not Product Reviews</option>
			<option value="OP" '.($current_v=="OP"?"selected":"").' >Only Product Reviews</option>
			<option value="NF" '.($current_v=="NF"?"selected":"").' >No Feature Image</option>
			<option value="OF" '.($current_v=="OF"?"selected":"").' >Only with Feature Image</option>
            </select>');
    }
add_action( 'restrict_manage_posts','lab_admin_posts_filter_restrict_manage_posts');

function lab_posts_filter( $query ){
        if ( is_admin() && isset($_GET['NP_FIELD_VALUE']) && $_GET['NP_FIELD_VALUE'] == 'NP') {
            $query->set( 'category__not_in', array(27) );
        }
        if ( is_admin() && isset($_GET['NP_FIELD_VALUE']) && $_GET['NP_FIELD_VALUE'] == 'OP') {
            $query->set( 'category__in', array(27) );
        }	
        if ( is_admin() && isset($_GET['NP_FIELD_VALUE']) && $_GET['NP_FIELD_VALUE'] == 'NF') {
            $query->set( 'meta_query', array(array('key' => '_thumbnail_id', 'compare' => 'not EXISTS')) );
        }
        if ( is_admin() && isset($_GET['NP_FIELD_VALUE']) && $_GET['NP_FIELD_VALUE'] == 'NFP') {
            $query->set( 'meta_query', array(array('key' => '_thumbnail_id', 'compare' => 'not EXISTS')) );
            $query->set( 'category__not_in', array(27) );
        }
        if ( is_admin() && isset($_GET['NP_FIELD_VALUE']) && $_GET['NP_FIELD_VALUE'] == 'OF') {
            $query->set( 'meta_query', array(array('key' => '_thumbnail_id', 'compare' => 'EXISTS')) );
        }	
    }
add_filter( 'parse_query', 'lab_posts_filter' );


// Create custom field with WordCount
add_action('save_post', function($post_id, $post, $update) {
    $word_count = str_word_count( strip_tags( strip_shortcodes($post->post_content) ) );
    update_post_meta($post_id, APA_SWCR_META_FIELD_KEY, $word_count);
}, 10, 3);

add_action( 'pre_get_posts', 'lab_orderby' );
add_action( 'pre_get_pages', 'lab_orderby' );

function lab_orderby( $query ) {
    $orderby = $query->get( 'orderby');
    if( APA_SWCR_META_FIELD_KEY == $orderby ) {
        $query->set('meta_key',APA_SWCR_META_FIELD_KEY);
        $query->set('orderby','meta_value_num');
    }
}


remove_action('wp_head', 'rsd_link'); //removes EditURI/RSD (Really Simple Discovery) link.
remove_action('wp_head', 'wlwmanifest_link'); //removes wlwmanifest (Windows Live Writer) link.
remove_action('wp_head', 'wp_generator'); //removes meta name generator.
remove_action('wp_head', 'wp_shortlink_wp_head'); //removes shortlink.
remove_action('wp_head', 'feed_links_extra', 3 );  //removes comments feed. 


/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Amazon Disclaimer
 */

function ffg_chris_disclaimer()
{
	$content=('<div class="ffgframe" data-nosnippet>Disclaimer here</div>');
	
	return ($content);
}

add_shortcode('ffg-chrisdisclaimer', 'ffg_chris_disclaimer');

// Disable XML  (security)
add_filter('xmlrpc_enabled', '__return_false');
?>