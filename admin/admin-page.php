<?php
/**
 * Admin settings page for Keyword Analyzer plugin
 *
 * @package Keyword_Analyzer
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get current options
$options = get_option('keyword_analyzer_options');

// Save settings if form is submitted
if (isset($_POST['keyword_analyzer_save_settings']) && check_admin_referer('keyword_analyzer_settings_nonce')) {
    $new_options = array(
        'min_word_length' => intval($_POST['min_word_length']),
        'max_keywords' => intval($_POST['max_keywords']),
        'exclude_words' => sanitize_text_field($_POST['exclude_words']),
        'display_method' => sanitize_text_field($_POST['display_method']),
    );
    
    update_option('keyword_analyzer_options', $new_options);
    $options = $new_options;
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully!', 'keyword-analyzer') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('keyword_analyzer_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="min_word_length"><?php _e('Minimum Word Length', 'keyword-analyzer'); ?></label>
                </th>
                <td>
                    <input type="number" id="min_word_length" name="min_word_length" 
                           value="<?php echo esc_attr($options['min_word_length']); ?>" min="2" max="20">
                    <p class="description">
                        <?php _e('Words shorter than this will be ignored in the analysis.', 'keyword-analyzer'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="max_keywords"><?php _e('Maximum Keywords to Display', 'keyword-analyzer'); ?></label>
                </th>
                <td>
                    <input type="number" id="max_keywords" name="max_keywords" 
                           value="<?php echo esc_attr($options['max_keywords']); ?>" min="1" max="50">
                    <p class="description">
                        <?php _e('The maximum number of keywords to show in the analysis.', 'keyword-analyzer'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="exclude_words"><?php _e('Words to Exclude', 'keyword-analyzer'); ?></label>
                </th>
                <td>
                    <textarea id="exclude_words" name="exclude_words" rows="3" cols="50" class="large-text"><?php 
                        echo esc_textarea($options['exclude_words']); 
                    ?></textarea>
                    <p class="description">
                        <?php _e('Comma-separated list of common words to exclude from analysis.', 'keyword-analyzer'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Display Method', 'keyword-analyzer'); ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span><?php _e('Display Method', 'keyword-analyzer'); ?></span>
                        </legend>
                        
                        <label>
                            <input type="radio" name="display_method" value="after_content" 
                                <?php checked('after_content', $options['display_method']); ?>>
                            <?php _e('After Content', 'keyword-analyzer'); ?>
                        </label><br>
                        
                        <label>
                            <input type="radio" name="display_method" value="shortcode" 
                                <?php checked('shortcode', $options['display_method']); ?>>
                            <?php _e('Shortcode Only', 'keyword-analyzer'); ?>
                            <code>[keyword_analysis]</code>
                        </label><br>
                        
                        <p class="description">
                            <?php _e('Choose how to display the keyword analysis.', 'keyword-analyzer'); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="keyword_analyzer_save_settings" class="button-primary" 
                   value="<?php _e('Save Settings', 'keyword-analyzer'); ?>">
        </p>
    </form>
    
    <div class="keyword-analyzer-info">
        <h2><?php _e('How to Use', 'keyword-analyzer'); ?></h2>
        <p>
            <?php _e('The Keyword Analyzer plugin automatically identifies the most powerful keywords in your content.', 'keyword-analyzer'); ?>
        </p>
        
        <h3><?php _e('Display Methods', 'keyword-analyzer'); ?></h3>
        <ul>
            <li>
                <strong><?php _e('After Content:', 'keyword-analyzer'); ?></strong> 
                <?php _e('Automatically displays the keyword analysis after your post or page content.', 'keyword-analyzer'); ?>
            </li>
            <li>
                <strong><?php _e('Shortcode:', 'keyword-analyzer'); ?></strong> 
                <?php _e('Use the shortcode', 'keyword-analyzer'); ?> <code>[keyword_analysis]</code> 
                <?php _e('to display the analysis anywhere in your content.', 'keyword-analyzer'); ?>
            </li>
        </ul>
        
        <h3><?php _e('Shortcode Parameters', 'keyword-analyzer'); ?></h3>
        <ul>
            <li>
                <code>[keyword_analysis post_id="123"]</code> - 
                <?php _e('Analyze a specific post or page by ID.', 'keyword-analyzer'); ?>
            </li>
        </ul>
    </div>
</div>