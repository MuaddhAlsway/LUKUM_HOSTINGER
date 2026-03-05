<?php
/**
 * Script to replace headers in index.php, blog.php, and blogPageDetails.php
 * with the header from contact.php
 */

// Read the contact.php header
$contactContent = file_get_contents('contact.php');

// Extract header from contact.php (from start to just before <!-- Hero Section -->)
$headerEndMarker = '<!-- Hero Section -->';
$headerEndPos = strpos($contactContent, $headerEndMarker);
if ($headerEndPos === false) {
    die("Could not find header end marker in contact.php\n");
}

$contactHeader = substr($contactContent, 0, $headerEndPos);

// Files to update with their specific titles
$files = [
    'index.php' => [
        'title' => "<?php echo t('page_title', 'LAKUM Artspace - Cultural Hub in Riyadh | Art Exhibitions & Events'); ?>",
        'heroMarker' => '<!-- Hero Section -->'
    ],
    'blog.php' => [
        'title' => "<?php echo t('blog_page_title', 'Blog - LAKUM Artspace'); ?>",
        'heroMarker' => '<!-- Hero Section -->'
    ],
    'blogPageDetails.php' => [
        'title' => 'id="page-title">Blog - LAKUM Artspace</title>',
        'heroMarker' => '<!-- Hero Section -->'
    ]
];

foreach ($files as $filename => $config) {
    echo "Processing $filename...\n";
    
    $fileContent = file_get_contents($filename);
    
    // Find where the hero section starts
    $heroPos = strpos($fileContent, $config['heroMarker']);
    if ($heroPos === false) {
        echo "  ERROR: Could not find hero marker in $filename\n";
        continue;
    }
    
    // Get the content after the header (from hero section onwards)
    $pageContent = substr($fileContent, $heroPos);
    
    // Build new file content with contact header + page-specific title + page content
    $newContent = $contactHeader;
    
    // Replace the generic title in the header with the page-specific title
    $newContent = str_replace(
        '<title>Contact Us - LAKUM Artspace</title>',
        '<title>' . $config['title'] . '</title>',
        $newContent
    );
    
    // Add the page content
    $newContent .= $pageContent;
    
    // Write the updated file
    if (file_put_contents($filename, $newContent)) {
        echo "  ✓ Successfully updated $filename\n";
    } else {
        echo "  ERROR: Could not write to $filename\n";
    }
}

echo "\nHeader replacement complete!\n";
?>
