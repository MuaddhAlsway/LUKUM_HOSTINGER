# Deploy new responsive header to all PHP files
# This script replaces the old lakum-header with the new app-header

$files = @('about.php', 'blog.php', 'blogPageDetails.php', 'calendar.php', 'contact.php', 'event.php', 'exhibitions.php', 'index.php', 'press.php', 'privacy.php', 'shop.php', 'spaces.php', 'terms.php')

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "Processing: $file"
        $content = Get-Content $file -Raw
        
        # Find and replace old header - use simpler pattern
        if ($content -match '<header class="lakum-header"') {
            # Replace the entire old header block
            $content = $content -replace '(?s)<header class="lakum-header".*?</header>', ''
            
            # Add new header after opening body tag or at the beginning of content
            $newHeader = Get-Content 'new-header.html' -Raw
            
            # Insert after <body> tag
            if ($content -match '<body[^>]*>') {
                $content = $content -replace '(<body[^>]*>)', "`$1`n$newHeader"
            }
            
            Set-Content $file $content
            Write-Host "✓ Updated: $file"
        } else {
            Write-Host "⚠ No old header found in: $file"
        }
    } else {
        Write-Host "✗ Not found: $file"
    }
}

Write-Host "`n✓ Header deployment complete!"
