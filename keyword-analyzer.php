<?php
/**
 * Plugin Name: Keyword Analyzer
 * Plugin URI: https://example.com/keyword-analyzer
 * Description: Identifies the most powerful keywords within an article or post content.
 * Version: 1.0.0
 * Author: WordPress Developer
 * Author URI: https://example.com
 * Text Domain: keyword-analyzer
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('KEYWORD_ANALYZER_VERSION', '1.0.0');
define('KEYWORD_ANALYZER_PATH', plugin_dir_path(__FILE__));
define('KEYWORD_ANALYZER_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class
 */
class Keyword_Analyzer {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        // Hook into WordPress
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_filter('the_content', array($this, 'analyze_content'));
        add_shortcode('keyword_analysis', array($this, 'keyword_analysis_shortcode'));
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
            'min_word_length' => 4,
            'max_keywords' => 10,
            'exclude_words' => 'the,and,that,for,this,with,from,your,have,are,not,will,more,what,about',
            'display_method' => 'after_content',
        );
        
        add_option('keyword_analyzer_options', $default_options);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup if needed
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_options_page(
            __('Keyword Analyzer Settings', 'keyword-analyzer'),
            __('Keyword Analyzer', 'keyword-analyzer'),
            'manage_options',
            'keyword-analyzer',
            array($this, 'display_admin_page')
        );
    }
    
    /**
     * Display the admin settings page
     */
    public function display_admin_page() {
        require_once KEYWORD_ANALYZER_PATH . 'admin/admin-page.php';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_keyword-analyzer' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'keyword-analyzer-admin',
            KEYWORD_ANALYZER_URL . 'admin/css/admin-style.css',
            array(),
            KEYWORD_ANALYZER_VERSION
        );
        
        wp_enqueue_script(
            'keyword-analyzer-admin',
            KEYWORD_ANALYZER_URL . 'admin/js/admin-script.js',
            array('jquery'),
            KEYWORD_ANALYZER_VERSION,
            true
        );
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        if (is_single() || is_page()) {
            wp_enqueue_style(
                'keyword-analyzer-frontend',
                KEYWORD_ANALYZER_URL . 'public/css/frontend-style.css',
                array(),
                KEYWORD_ANALYZER_VERSION
            );
            
            wp_enqueue_script(
                'keyword-analyzer-frontend',
                KEYWORD_ANALYZER_URL . 'public/js/frontend-script.js',
                array('jquery'),
                KEYWORD_ANALYZER_VERSION,
                true
            );
        }
    }
    
    /**
     * Analyze content and identify powerful keywords
     */
    public function analyze_content($content) {
        // Only process on single posts or pages
        if (!is_single() && !is_page()) {
            return $content;
        }
        
        // Get plugin options
        $options = get_option('keyword_analyzer_options');
        
        // If display method is not 'after_content', return content unchanged
        if ($options['display_method'] !== 'after_content') {
            return $content;
        }
        
        // Get keywords from content
        $keywords = $this->extract_keywords($content);
        
        // Generate the keywords display
        $keywords_html = $this->generate_keywords_html($keywords);
        
        // Append keywords to content
        return $content . $keywords_html;
    }
    
    /**
     * Extract keywords from content
     */
    public function extract_keywords($content) {
        // Get plugin options
        $options = get_option('keyword_analyzer_options');
        $min_length = isset($options['min_word_length']) ? intval($options['min_word_length']) : 4;
        $max_keywords = isset($options['max_keywords']) ? intval($options['max_keywords']) : 10;
        $exclude_words = isset($options['exclude_words']) ? explode(',', strtolower($options['exclude_words'])) : array();
        
        // Strip HTML tags and convert to lowercase
        $text = strtolower(strip_tags($content));
        
        // Remove punctuation and special characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Count word frequencies
        $word_counts = array();
        foreach ($words as $word) {
            // Skip short words and excluded words
            if (mb_strlen($word) < $min_length || in_array($word, $exclude_words)) {
                continue;
            }
            
            if (isset($word_counts[$word])) {
                $word_counts[$word]++;
            } else {
                $word_counts[$word] = 1;
            }
        }
        
        // Sort by frequency (descending)
        arsort($word_counts);
        
        // Return top keywords
        return array_slice($word_counts, 0, $max_keywords, true);
    }
    
    /**
     * Generate HTML for displaying keywords
     */
    public function generate_keywords_html($keywords) {
        if (empty($keywords)) {
            return '';
        }
        
        $html = '<div class="keyword-analyzer-container">';
        $html .= '<h3>' . __('Top Keywords', 'keyword-analyzer') . '</h3>';
        $html .= '<div class="keyword-analyzer-keywords">';
        
        foreach ($keywords as $word => $count) {
            $html .= '<span class="keyword-analyzer-keyword" title="' . sprintf(__('Appears %d times', 'keyword-analyzer'), $count) . '">';
            $html .= esc_html($word);
            $html .= '</span>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Shortcode for displaying keyword analysis
     */
    public function keyword_analysis_shortcode($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
        ), $atts, 'keyword_analysis');
        
        $post = get_post($atts['post_id']);
        if (!$post) {
            return '';
        }
        
        $content = $post->post_content;
        $keywords = $this->extract_keywords($content);
        
        return $this->generate_keywords_html($keywords);
    }
}

// Initialize the plugin
$keyword_analyzer = new Keyword_Analyzer();