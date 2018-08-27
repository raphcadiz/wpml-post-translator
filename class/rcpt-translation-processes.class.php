<?php if ( ! defined( 'ABSPATH' ) ) exit;

use Google\Cloud\Translate\TranslateClient;

class RCPT_Translation_Processes {

    public function __construct() {
        add_action('admin_init', array($this, 'process_translation' ));
        add_action('rcpt_translation_schedule', array( $this, 'run_rcpt_translation_schedule'));
    }

    public function process_translation() {
        global $pagenow;
        if (( $pagenow == 'post.php' ) || (get_post_type() == 'post')) {
            $post = get_post($_REQUEST['post']);
            if ($post) {
                $this->do_translation_to_post($post->ID);
            }
        }

    }

    public function get_translated_value($string, $target) {
        $rcpt_settings = get_option('rcpt_settings');
        $google_transalator_key = isset($rcpt_settings['google_transalator_key']) ? $rcpt_settings['google_transalator_key'] : '';

        if (!$google_transalator_key)
            return;

        try {
          /** Google clould translation has differnt
            * lang codes from WPML.
            * Covert it to be compatible for Google Translations
            */
            if ($target == 'zh-hans') {
                $parsed_target = 'zh';
            } else if ($target == 'zh-hant') {
                $parsed_target = 'zh-TW';
            } else if ($target == 'pt-br' || $target == 'pt-pt') {
                $parsed_target = 'pt';
            } else {
                $parsed_target = $target;
            }

            $translate = new TranslateClient([
                'key' => $google_transalator_key
            ]);
    
            $parsed_string = $translate->translate($string, [
                'target' => $parsed_target
            ]);
            $parsed_string = ($parsed_string['text']) ? $parsed_string['text'] : $string;
    
            return $parsed_string;

        } catch(Exception $e) {
            error_log($e);
        }
        
    }

    public function do_translation_to_post($post_id) {
        $duplicated_posts = apply_filters( 'wpml_post_duplicates', $post_id );
        $post_format = get_post_format($post_id);

        foreach ($duplicated_posts as $lang_code => $duplicated_post_id):

            $post = get_post($duplicated_post_id);
			if ($post->ID == $post_id) {
				continue;
            }
            
            $translated_version = get_post_meta($post->ID, 'translated_version', true);
            if ($translated_version) {
                continue;
            }
		
            if (!is_wp_error($post)) {
            
                if ($post_format) {
                    set_post_format( $post->ID, $post_format );
                }

                $categories = wp_get_post_categories( $post->ID );
                if (!empty($categories)) {
                    foreach ($categories as $category_id) {
                        $category = get_category( $category_id );
                        $cat_args = array(
                            'name' => $this->get_translated_value($category->name, $lang_code),
                            'slug' => $this->get_translated_value($category->slug, $lang_code)
                        );

                        wp_update_term($category->term_id, 'category', $cat_args);
                    }
                }

                $tags = get_the_tags( $post->ID );
                if ($tags) {
                    foreach ($tags as $tag) {
                        $tag_args = array(
                            'name' => $this->get_translated_value($tag->name, $lang_code),
                            'slug' => $this->get_translated_value($tag->slug, $lang_code)
                        );

                        wp_update_term($tag->term_id, 'post_tag', $tag_args);
                    }
                }

                $updated_post = array(
                    'ID'           => $post->ID,
                    'post_title'   => $this->get_translated_value($post->post_title, $lang_code),
                    'post_content' => $this->get_translated_value($post->post_content, $lang_code),
                );
                wp_update_post( $updated_post );
                update_post_meta( $post->ID, 'translated_version', 1 );
                
            }

        endforeach;
    }

    public function run_rcpt_translation_schedule() {
        $rcpt_settings = get_option('rcpt_settings');
        $number_posts_translate = isset($rcpt_settings['number_posts_translate']) ? $rcpt_settings['number_posts_translate'] : 10;

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $number_posts_translate,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_icl_lang_duplicate_of',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'done_translation',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $posts = get_posts($args);

        $translated_posts = array();
        if (!empty($posts)):

            foreach($posts as $post) {
                do_action( 'wpml_admin_make_post_duplicates', $post->ID );
                $translated_posts[] = $post->ID;
                update_post_meta( $post->ID, 'done_translation', 1);
            }
            
        endif;

        if (!empty($translated_posts)) {
            foreach($translated_posts as $translated_post_id) {
                $this->do_translation_to_post($translated_post_id);
            }
        }
    }
}

new RCPT_Translation_Processes;