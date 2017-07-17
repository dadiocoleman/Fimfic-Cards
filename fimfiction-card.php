<?php
/**
 * Plugin Name: FIMFiction Cards
 * Plugin URI:  http://ffcards.dadiocoleman.com/
 * Description: Embed FIMFiction stories via a shortcode.
 * Version:     1.1.0
 * Author:      David Coleman
 * Author URI:  https://www.dadiocoleman.com
 * Text Domain: fimfiction-cards
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
  
	require_once( __DIR__ . '/inc/bbcode-html.php'); 
 
	//Get stylesheets
	function fimfic_register_styles() {
		wp_enqueue_style( 'fimfic-card-full', plugins_url( '/assets/css/fimfiction-card-full.min.css', __FILE__ ), array(), '20170717', 'all' );
		wp_enqueue_style( 'fimfic-card-mini', plugins_url( '/assets/css/fimfiction-card-mini.min.css', __FILE__ ), array(), '20170717', 'all' );
		wp_enqueue_style( 'font-awesome', plugins_url( '/assets/css/font-awesome.min.css', __FILE__ ), array(), '20170717', 'all' );
	}
	add_action('wp_enqueue_scripts', 'fimfic_register_styles');	
	//Get scripts
	function fimfic_register_scripts() {
		wp_enqueue_script( 'fimfic-expand', plugins_url( '/assets/js/fimfic-expand.min.js', __FILE__ ), array(), '20170717', 'all' );
	}
	add_action('wp_enqueue_scripts', 'fimfic_register_scripts');
	
	if ( ! function_exists( 'shortcode_exists' ) ) :
	function shortcode_exists( $shortcode = '' ) {
		global $shortcode_tags;
		if ( $shortcode && array_key_exists( $shortcode, $shortcode_tags ) )
			return true;
		return false;
	}
	endif;
	
	//Register shortcode
	function fimfic_shortcode($atts, $content = null){
		//Attributes
		$a = shortcode_atts( array(
			'source' => '',
			'target' => 'blank',
			'type'	 => 'mini'
		), $atts );
		//Attribute variables
		$source = $a['source'];
		$target = $a['target'];
		$card_type = $a['type'];
		
		//Get json and cache
		$story_data = file_get_contents('https://www.fimfiction.net/api/story.php?story='.$source.'.json');
		$bits = explode('/',$source);
		$value_id = $bits[4];
		$key = 'fimfic_card_' . $value_id;
		$transient_value = get_transient( $key );
		if ( $transient_value === false || $transient_value == '' ) {
			$transient_value = file_get_contents('https://www.fimfiction.net/api/story.php?story='.$value_id);
			set_transient($key, $transient_value, 3 * 60 * 60);
		}
		$story = json_decode($transient_value, true);
		
		//Covert data to vars
		$title = $story['story']['title'];
		$author = $story['story']['author']['name'];
		$author_id = $story['story']['author']['id'];
		$author_url = str_replace(' ','+',$author);
		$id = $story['story']['id'];
		$link = $story['story']['url'];
		$status = $story['story']['status'];
		if ( $status == 'Complete' ) {
			$status_icon = 'check3';
		} elseif ( $status == 'Icomplete' ) {
			$status_icon = 'pencil4';
		} elseif ( $status == 'On Hiatus' ) {
			$status_icon = 'pause7';
		} elseif ( $status == 'Cancelled' ) {
			$status_icon = 'cancel6';
		}
		$cover_src = str_replace(' ','',$story['story']['full_image']);
		$cover = esc_url(json_encode($cover_src,JSON_UNESCAPED_SLASHES));
		$description = $story['story']['short_description'];
		$description_full = bbcode_to_html(str_replace(array("\r\n", "\n", "\r"), '[br]', $story['story']['description']));
		$desc_readmore_id = "'fulldesc-$id'";
		$desc_readless_id = "'shortdesc-$id'";
		$desc_readmore = '<a id="fimfic-expand-btn" class="fulldesc-hidden" onclick="hide('.$desc_readmore_id.');show('.$desc_readless_id.');toggle(this);toggleClass(this);"><span>More</span></a>';
		$modified = $story['story']['date_modified'];
		if ( $status == 'Complete' ) {
			$modified_text = 'Completed';
		} else {
			$modified_text = 'Updated';
		}
		$modified_date = '<span class="fimfic-modified"><strong>'.$modified_text.':</strong>&nbsp;'.date('M j, Y', $modified).'</span>';
		
		//Categories
			//2nd Person
			$cat_1 = $story['story']['categories']['2nd Person'];
			if ( $cat_1 ) {
				$cat_1_output = '<li class="fimfic-cat" title="2nd Person">2nd</li>';
			} else {
				$cat_1_output = false;
			}
			
			//Adventure
			$cat_2 = $story['story']['categories']['Adventure'];
			if ( $cat_2 ) {
				$cat_2_output = '<li class="fimfic-cat" title="Adventure">Adv</li>';
			} else {
				$cat_2_output = false;
			}
			
			//Alternate Universe
			$cat_3 = $story['story']['categories']['Alternate Universe'];
			if ( $cat_3 ) {
				$cat_3_output = '<li class="fimfic-cat" title="Alternate Universe">Alt. U</li>';
			} else {
				$cat_3_output = false;
			}
			
			//Anthro
			$cat_4 = $story['story']['categories']['Anthro'];
			if ( $cat_4 ) {
				$cat_4_output = '<li class="fimfic-cat" title="Anthro">An</li>';
			} else {
				$cat_4_output = false;
			}
			
			//Comedy
			$cat_5 = $story['story']['categories']['Comedy'];
			if ( $cat_5 ) {
				$cat_5_output = '<li class="fimfic-cat" title="Comedy">Com</li>';
			} else {
				$cat_5_output = false;
			}
			
			//Crossover
			$cat_6 = $story['story']['categories']['Crossover'];
			if ( $cat_6 ) {
				$cat_6_output = '<li class="fimfic-cat" title="Crossover">X&#8217;ver</li>';
			} else {
				$cat_6_output = false;
			}
			
			//Dark
			$cat_7 = $story['story']['categories']['Dark'];
			if ( $cat_7 ) {
				$cat_7_output = '<li class="fimfic-cat" title="Dark">Dark</li>';
			} else {
				$cat_7_output = false;
			}
			
			//Drama
			$cat_8 = $story['story']['categories']['Drama'];
			if ( $cat_8 ) {
				$cat_8_output = '<li class="fimfic-cat" title="Drama">Drama</li>';
			} else {
				$cat_8_output = false;
			}
			
			//Equestria Girls
			$cat_9 = $story['story']['categories']['Equestria Girls'];
			if ( $cat_9 ) {
				$cat_9_output = '<li class="fimfic-cat" title="Equestria Girls">EqG</li>';
			} else {
				$cat_9_output = false;
			}
			
			//Horror
			$cat_10 = $story['story']['categories']['Horror'];
			if ( $cat_10 ) {
				$cat_10_output = '<li class="fimfic-cat" title="Horror">Hor</li>';
			} else {
				$cat_10_output = false;
			}
			
			//Human
			$cat_11 = $story['story']['categories']['Human'];
			if ( $cat_11 ) {
				$cat_11_output = '<li class="fimfic-cat" title="Human">Human</li>';
			} else {
				$cat_11_output = false;
			}
			
			//Mystery
			$cat_12 = $story['story']['categories']['Mystery'];
			if ( $cat_12 ) {
				$cat_12_output = '<li class="fimfic-cat" title="Mystery">Mys</li>';
			} else {
				$cat_12_output = false;
			}
			
			//Random
			$cat_13 = $story['story']['categories']['Random'];
			if ( $cat_13 ) {
				$cat_13_output = '<li class="fimfic-cat" title="Random">Rand</li>';
			} else {
				$cat_13_output = false;
			}
			
			//Romance
			$cat_14 = $story['story']['categories']['Romance'];
			if ( $cat_14 ) {
				$cat_14_output = '<li class="fimfic-cat" title="Romance">Rom</li>';
			} else {
				$cat_14_output = false;
			}
			
			//Sad
			$cat_15 = $story['story']['categories']['Sad'];
			if ( $cat_15 ) {
				$cat_15_output = '<li class="fimfic-cat" title="Sad">Sad</li>';
			} else {
				$cat_15_output = false;
			}
			
			//Sci-Fi
			$cat_16 = $story['story']['categories']['Sci-Fi'];
			if ( $cat_16 ) {
				$cat_16_output = '<li class="fimfic-cat" title="Sci-Fi">Sci</li>';
			} else {
				$cat_16_output = false;
			}
			
			//Slice of Life
			$cat_17 = $story['story']['categories']['Slice of Life'];
			if ( $cat_17 ) {
				$cat_17_output = '<li class="fimfic-cat" title="Slice of Life">SoL</li>';
			} else {
				$cat_17_output = false;
			}
			
			//Thriller
			$cat_18 = $story['story']['categories']['Thriller'];
			if ( $cat_18 ) {
				$cat_18_output = '<li class="fimfic-cat" title="Thriller">Thr</li>';
			} else {
				$cat_18_output = false;
			}
			
			//Tragedy
			$cat_19 = $story['story']['categories']['Tragedy'];
			if ( $cat_19 ) {
				$cat_19_output = '<li class="fimfic-cat" title="Tragedy">Tra</li>';
			} else {
				$cat_19_output = false;
			}
			
		//Get story content rating			
		$rating_full = $story['story']['content_rating_text'];
		if ( $rating_full == 'Mature' ) {
			$rating = 'M';
		} else if ( $rating_full == 'Teen' ) {
			$rating = 'T';
		} else {
			$rating = 'E';
		}
		
		//List categories in html
		$cats = '<ul class="fimfic-story_tags">'.$cat_2_output.$cat_14_output.$cat_5_output.$cat_10_output.$cat_7_output.$cat_8_output.$cat_1_output.$cat_3_output.$cat_4_output.$cat_6_output.$cat_9_output.$cat_11_output.$cat_12_output.$cat_13_output.$cat_15_output.$cat_16_output.$cat_17_output.$cat_18_output.$cat_19_output.'</ul>';
		
		if (str_word_count($description_full) < 450) {
			$desc_content = '<p id="shortdesc-'.$id.'" style="display: block;">'.substr($description_full, 0, 450).'&hellip;</p><p id="fulldesc-'.$id.'" style="display: none;">'.$description_full.'</p>'.$desc_readmore;
		} else {
			$desc_content = '<p style="display: block;">'.$description_full.'</p>';
		}
		
		//Output shortcode html
		if ( $card_type == 'full' ) {
			$shortcode = '<div id="fimfic-card" class="fimfic-card-full fimfic-'.$id.'"><div class="fimfic-inner"><header class="fimfic-title"><span class="fimfic-rating-'.$rating_full.'" title="Rated '.$rating_full.'">'.$rating.'</span><div class="fimfic-title-text"><a class="fimfic-story-name" href="'.$link.'" title="'.$title.'" target="_'.$target.'">'.$title.'</a>&nbsp;<div class="fimfic-author"><span class="fimfic-author-by">by</span>&nbsp;<a href="https://www.fimfiction.net/user/'.$author_id.'/'.$author_url.'" target="_'.$target.'">'.$author.'</a></div></div></header><div class="fimfic-content">'.$cats.'<section class="fimfic-description"><div class="fimfic-cover"><a target="_'.$target.'" href="'.$cover.'"><img src="'.$cover.'" /></a></div><div class="fimfic-description-text">'.$desc_content.'</div></section></div></div></div>';
		} elseif ( $card_type == 'mini' ) {
			$shortcode = '<div id="fimfic-card" class="fimfic-card-mini fimfic-'.$id.'"><div class="fimfic-inner"><header class="fimfic-title"><span class="fimfic-rating-'.$rating_full.'" title="Rated '.$rating_full.'">'.$rating.'</span><div class="fimfic-title-text"><a class="fimfic-story-name" href="'.$link.'" title="'.$title.'" target="_'.$target.'">'.$title.'</a>&nbsp;<div class="fimfic-author"><span class="fimfic-author-by">by</span>&nbsp;<a href="https://www.fimfiction.net/user/'.$author_id.'/'.$author_url.'" target="_'.$target.'">'.$author.'</a></div></div></header><div class="fimfic-content">'.$cats.'<section class="fimfic-description"><div class="fimfic-cover"><a target="_'.$target.'" href="'.$cover.'"><img src="'.$cover.'" /></a></div><div class="fimfic-description-text"><p style="display: block;">'.$description.'</p></div></section></div></div></div>';		}
		return $shortcode;
	}

	add_shortcode('fimfic','fimfic_shortcode');
	//Register shortcode button for editor
		add_action('init', 'fimfic_shortcode_button_init');
		function fimfic_shortcode_button_init() {
			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
				return; 
			add_filter("mce_external_plugins", "fimfic_register_tinymce_plugin"); 
			add_filter('mce_buttons', 'fimfic_add_tinymce_button');
		}
		function fimfic_register_tinymce_plugin($plugin_array) {
			$plugin_array['fimfic_button'] = plugins_url( '/assets/js/shortcode-icon.min.js', __FILE__ );
			return $plugin_array;
		}
		function fimfic_add_tinymce_button($buttons) {
			$buttons[] = "fimfic_button";
			return $buttons;
		}
?>
