<?php
/**
 * Advanced keyword analysis functionality
 *
 * @package Keyword_Analyzer
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Class for advanced keyword analysis
 */
class Keyword_Analysis {
    
    /**
     * Analyze content using TF-IDF like approach
     *
     * @param string $content The content to analyze
     * @param array  $options Analysis options
     * @return array Analyzed keywords with scores
     */
    public static function analyze($content, $options = array()) {
        // Get basic word frequencies first
        $word_counts = self::get_word_frequencies($content, $options);
        
        // If we don't have enough words, return the basic analysis
        if (count($word_counts) < 5) {
            return $word_counts;
        }
        
        // Calculate the total word count
        $total_words = array_sum($word_counts);
        
        // Calculate term frequency (TF)
        $term_frequencies = array();
        foreach ($word_counts as $word => $count) {
            $term_frequencies[$word] = $count / $total_words;
        }
        
        // Get word importance scores
        $word_scores = self::calculate_word_importance($term_frequencies, $content);
        
        // Sort by score (descending)
        arsort($word_scores);
        
        // Return top keywords based on max_keywords setting
        $max_keywords = isset($options['max_keywords']) ? intval($options['max_keywords']) : 10;
        return array_slice($word_scores, 0, $max_keywords, true);
    }
    
    /**
     * Get word frequencies from content
     *
     * @param string $content The content to analyze
     * @param array  $options Analysis options
     * @return array Word frequencies
     */
    public static function get_word_frequencies($content, $options = array()) {
        // Get options with defaults
        $min_length = isset($options['min_word_length']) ? intval($options['min_word_length']) : 4;
        $exclude_words = isset($options['exclude_words']) ? explode(',', strtolower($options['exclude_words'])) : array();
        
        // Add common English stop words if not already in exclude list
        $common_stop_words = array(
            'the', 'and', 'that', 'have', 'for', 'not', 'with', 'you', 'this', 'but', 'his', 'from',
            'they', 'will', 'would', 'there', 'their', 'what', 'about', 'which', 'when', 'make', 'like',
            'time', 'just', 'him', 'know', 'take', 'people', 'into', 'year', 'your', 'good', 'some', 'could',
            'them', 'see', 'other', 'than', 'then', 'now', 'look', 'only', 'come', 'its', 'over', 'think',
            'also', 'back', 'after', 'use', 'two', 'how', 'our', 'work', 'first', 'well', 'way', 'even',
            'new', 'want', 'because', 'any', 'these', 'give', 'day', 'most', 'cant', 'cant'
        );
        
        $exclude_words = array_merge($exclude_words, $common_stop_words);
        $exclude_words = array_unique($exclude_words);
        
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
        
        return $word_counts;
    }
    
    /**
     * Calculate word importance using various factors
     *
     * @param array  $term_frequencies Term frequencies
     * @param string $content The original content
     * @return array Word importance scores
     */
    public static function calculate_word_importance($term_frequencies, $content) {
        $word_scores = array();
        $content_length = str_word_count(strip_tags($content));
        
        // Get headings from content
        preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $headings);
        $heading_text = implode(' ', $headings[1]);
        $heading_text = strtolower(strip_tags($heading_text));
        
        // Get first paragraph (often contains important keywords)
        preg_match('/<p[^>]*>(.*?)<\/p>/i', $content, $first_para);
        $first_para_text = '';
        if (!empty($first_para[1])) {
            $first_para_text = strtolower(strip_tags($first_para[1]));
        }
        
        foreach ($term_frequencies as $word => $tf) {
            $score = $tf; // Base score is the term frequency
            
            // Bonus for words in headings (3x weight)
            if (strpos($heading_text, $word) !== false) {
                $score *= 3;
            }
            
            // Bonus for words in first paragraph (2x weight)
            if (strpos($first_para_text, $word) !== false) {
                $score *= 2;
            }
            
            // Bonus for longer words (they tend to be more meaningful)
            $length_factor = min(mb_strlen($word) / 10, 1.5); // Cap at 1.5x bonus
            $score *= $length_factor;
            
            // Penalize words that appear too frequently (likely less meaningful)
            if ($tf > 0.1) { // If word makes up more than 10% of content
                $score *= 0.8; // 20% penalty
            }
            
            $word_scores[$word] = $score;
        }
        
        return $word_scores;
    }
    
    /**
     * Get related keywords based on co-occurrence
     *
     * @param string $content The content to analyze
     * @param string $keyword The main keyword to find related terms for
     * @param int    $count Number of related keywords to return
     * @return array Related keywords
     */
    public static function get_related_keywords($content, $keyword, $count = 5) {
        // Strip HTML tags and convert to lowercase
        $text = strtolower(strip_tags($content));
        
        // Split content into sentences
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        
        // Find sentences containing the keyword
        $relevant_sentences = array();
        foreach ($sentences as $sentence) {
            if (strpos($sentence, $keyword) !== false) {
                $relevant_sentences[] = $sentence;
            }
        }
        
        // If no relevant sentences found, return empty array
        if (empty($relevant_sentences)) {
            return array();
        }
        
        // Combine relevant sentences and analyze word frequencies
        $related_text = implode(' ', $relevant_sentences);
        $options = array(
            'min_word_length' => 4,
            'exclude_words' => $keyword, // Exclude the main keyword
        );
        
        $related_words = self::get_word_frequencies($related_text, $options);
        
        // Sort by frequency (descending)
        arsort($related_words);
        
        // Return top related keywords
        return array_slice($related_words, 0, $count, true);
    }
}