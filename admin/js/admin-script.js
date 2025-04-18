/**
 * Admin JavaScript for Keyword Analyzer plugin
 */

(function($) {
    'use strict';
    
    /**
     * Initialize the admin functionality
     */
    function initAdminPage() {
        // Initialize the exclude words input with tags-like functionality
        initExcludeWordsInput();
        
        // Preview functionality
        $('#preview-analysis').on('click', function(e) {
            e.preventDefault();
            previewKeywordAnalysis();
        });
    }
    
    /**
     * Initialize the exclude words input with tags-like functionality
     */
    function initExcludeWordsInput() {
        const $input = $('#exclude_words');
        if (!$input.length) return;
        
        // Create a visual representation of the comma-separated list
        const $container = $('<div class="keyword-tags-container"></div>');
        $input.after($container);
        
        // Update the visual representation when the input changes
        function updateTagsDisplay() {
            const words = $input.val().split(',').filter(word => word.trim() !== '');
            $container.empty();
            
            words.forEach(function(word) {
                const $tag = $(
                    '<span class="keyword-tag">' + 
                    word.trim() + 
                    '<span class="remove-tag">×</span>' + 
                    '</span>'
                );
                $container.append($tag);
            });
            
            // Add a new tag input
            const $newTagInput = $('<input type="text" class="new-tag-input" placeholder="Add word...">');
            $container.append($newTagInput);
        }
        
        // Initial update
        updateTagsDisplay();
        
        // Handle removing tags
        $container.on('click', '.remove-tag', function() {
            const tagText = $(this).parent().text().replace('×', '').trim();
            let words = $input.val().split(',').map(word => word.trim());
            words = words.filter(word => word !== tagText && word !== '');
            $input.val(words.join(','));
            updateTagsDisplay();
        });
        
        // Handle adding new tags
        $container.on('keydown', '.new-tag-input', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const newTag = $(this).val().trim();
                if (newTag) {
                    let currentVal = $input.val();
                    if (currentVal && !currentVal.endsWith(',')) {
                        currentVal += ',';
                    }
                    currentVal += newTag;
                    $input.val(currentVal);
                    updateTagsDisplay();
                }
            }
        });
    }
    
    /**
     * Preview the keyword analysis with current settings
     */
    function previewKeywordAnalysis() {
        // This would typically make an AJAX call to the server
        // For now, we'll just show a sample preview
        const $previewArea = $('.keyword-analysis-preview');
        if (!$previewArea.length) {
            $('.keyword-analyzer-info').before('<div class="keyword-analysis-preview"></div>');
            $previewArea = $('.keyword-analysis-preview');
        }
        
        $previewArea.html(
            '<div class="notice notice-info">' +
            '<p>Preview functionality would analyze a sample post with current settings.</p>' +
            '<div class="keyword-analyzer-container">' +
            '<h3>Sample Keywords Preview</h3>' +
            '<div class="keyword-analyzer-keywords">' +
            '<span class="keyword-analyzer-keyword" title="Appears 12 times">wordpress</span>' +
            '<span class="keyword-analyzer-keyword" title="Appears 8 times">plugin</span>' +
            '<span class="keyword-analyzer-keyword" title="Appears 7 times">content</span>' +
            '<span class="keyword-analyzer-keyword" title="Appears 5 times">analysis</span>' +
            '<span class="keyword-analyzer-keyword" title="Appears 4 times">keywords</span>' +
            '</div></div></div>'
        );
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        initAdminPage();
    });
    
})(jQuery);

// Add CSS for the tags input
jQuery(document).ready(function($) {
    $('head').append(
        '<style>' +
        '.keyword-tags-container { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 5px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; background: #fff; }' +
        '.keyword-tag { display: inline-flex; align-items: center; background: #e9e9e9; border-radius: 3px; padding: 2px 8px; margin-right: 5px; margin-bottom: 5px; }' +
        '.remove-tag { margin-left: 5px; cursor: pointer; font-weight: bold; }' +
        '.remove-tag:hover { color: #d54e21; }' +
        '.new-tag-input { border: none; outline: none; flex-grow: 1; min-width: 100px; }' +
        '.keyword-analysis-preview { margin: 20px 0; }' +
        '</style>'
    );
});