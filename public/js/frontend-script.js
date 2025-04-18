/**
 * Frontend JavaScript for Keyword Analyzer plugin
 */

(function($) {
    'use strict';
    
    /**
     * Initialize the frontend functionality
     */
    function initKeywordAnalyzer() {
        // Add tooltips to keywords
        $('.keyword-analyzer-keyword').each(function() {
            $(this).attr('title', $(this).attr('title') || 'Keyword: ' + $(this).text());
        });
        
        // Add click event to keywords to highlight them in content
        $('.keyword-analyzer-keyword').on('click', function() {
            const keyword = $(this).text().toLowerCase();
            highlightKeywordInContent(keyword);
        });
    }
    
    /**
     * Highlight the selected keyword in the content
     */
    function highlightKeywordInContent(keyword) {
        // Remove any existing highlights
        $('.entry-content, .post-content').find('.keyword-highlight').each(function() {
            const text = $(this).text();
            $(this).replaceWith(text);
        });
        
        // Find the content container
        const $content = $('.entry-content, .post-content');
        if (!$content.length) return;
        
        // Create a regex to find the keyword (word boundaries to match whole words only)
        const regex = new RegExp('\\b(' + keyword + ')\\b', 'gi');
        
        // Find text nodes in the content
        $content.find('*').contents().each(function() {
            if (this.nodeType === 3) { // Text node
                const text = this.nodeValue;
                if (regex.test(text)) {
                    const highlighted = text.replace(regex, '<span class="keyword-highlight">$1</span>');
                    $(this).replaceWith(highlighted);
                }
            }
        });
        
        // Scroll to the first highlighted keyword
        const $firstHighlight = $('.keyword-highlight').first();
        if ($firstHighlight.length) {
            $('html, body').animate({
                scrollTop: $firstHighlight.offset().top - 100
            }, 500);
        }
        
        // Add CSS for highlighted keywords
        if ($('.keyword-highlight').length && !$('#keyword-highlight-style').length) {
            $('head').append(
                '<style id="keyword-highlight-style">' +
                '.keyword-highlight { background-color: #ffff99; color: #000; padding: 0 2px; }' +
                '</style>'
            );
        }
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        initKeywordAnalyzer();
    });
    
})(jQuery);