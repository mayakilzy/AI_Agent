# Keyword Analyzer WordPress Plugin

Keyword Analyzer is a WordPress plugin that automatically identifies and displays the most powerful keywords within your article or post content.

## Features

- Automatically analyzes post and page content to identify important keywords
- Configurable settings for minimum word length and maximum keywords to display
- Option to exclude common words from analysis
- Multiple display methods: automatic display after content or via shortcode
- Interactive frontend display with keyword highlighting functionality
- Easy-to-use admin interface for configuration

## Installation

1. Download the plugin zip file
2. Go to your WordPress admin area and navigate to Plugins > Add New
3. Click the "Upload Plugin" button at the top of the page
4. Choose the downloaded zip file and click "Install Now"
5. After installation, click "Activate Plugin"

## Usage

### Basic Usage

Once activated, the plugin will automatically analyze your post and page content and display the most frequently used keywords at the end of your content (if the "After Content" display method is selected).

### Settings

To configure the plugin, go to Settings > Keyword Analyzer in your WordPress admin area. The following options are available:

- **Minimum Word Length**: Words shorter than this will be ignored in the analysis (default: 4)
- **Maximum Keywords to Display**: The maximum number of keywords to show in the analysis (default: 10)
- **Words to Exclude**: Comma-separated list of common words to exclude from analysis
- **Display Method**: Choose between "After Content" (automatic) or "Shortcode Only"

### Shortcode Usage

You can use the `[keyword_analysis]` shortcode to display the keyword analysis anywhere in your content. By default, it will analyze the current post/page, but you can also specify a different post ID:

```
[keyword_analysis post_id="123"]
```

## Frontend Features

On the frontend, the keywords are displayed in a visually appealing container. When a user clicks on a keyword, the plugin will highlight all occurrences of that keyword in the content and scroll to the first occurrence.

## Advanced Usage

### Developers

The plugin provides several hooks and filters that developers can use to customize its behavior. Documentation for these hooks will be provided in a future update.

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support or feature requests, please contact the plugin author.