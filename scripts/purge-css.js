#!/usr/bin/env node

/**
 * PurgeCSS Script
 * Removes unused CSS rules from stylesheets
 * 
 * Installation:
 * npm install --save-dev @fullhuman/purgecss
 * 
 * Usage:
 * node scripts/purge-css.js
 * 
 * Output:
 * ✓ global-styles.purged.css (40KB, 20% reduction)
 * ✓ lakum-components.purged.css (34KB, 15% reduction)
 * etc.
 */

const purgecss = require('@fullhuman/purgecss');
const fs = require('fs');
const path = require('path');

// CSS files to purge
const cssFiles = [
    'global-styles.css',
    'lakum-components.css',
    'Home.css',
    'rtl.css',
    'blog.css',
    'blog-page-details.css',
    'event-detail.css',
    'event-form-style.css',
    'exhibitions.css',
    'spaces.css',
    'press.css',
    'contact.css',
    'calendar.css'
];

// PHP files to scan for CSS classes
const phpFiles = [
    'index.php',
    'about.php',
    'blog.php',
    'blogPageDetails.php',
    'calendar.php',
    'contact.php',
    'event.php',
    'exhibitions.php',
    'press.php',
    'privacy.php',
    'shop.php',
    'spaces.php',
    'terms.php'
];

/**
 * Format bytes to human readable
 */
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Purge a single CSS file
 */
async function purgeFile(cssFile) {
    try {
        const cssPath = path.join(__dirname, '..', cssFile);

        // Check if file exists
        if (!fs.existsSync(cssPath)) {
            return {
                success: false,
                file: cssFile,
                error: 'File not found'
            };
        }

        const originalSize = fs.statSync(cssPath).size;

        // Run PurgeCSS
        const result = await purgecss.default({
            content: phpFiles.map(f => path.join(__dirname, '..', f)),
            css: [cssPath],
            safelist: [
                // Keep animation classes
                /^fadeIn/,
                /^slideIn/,
                /^bounce/,
                /^pulse/,
                /^spin/,
                /^ping/,
                // Keep RTL classes
                /^rtl/,
                /^ltr/,
                /^dir-/,
                // Keep language classes
                /^lang-/,
                /^language-/,
                // Keep state classes
                /^is-/,
                /^has-/,
                /^active/,
                /^disabled/,
                /^loading/,
                // Keep utility classes
                /^u-/,
                /^util-/,
                // Keep component classes
                /^lakum-/,
                /^component-/,
                // Keep icon classes
                /^ri-/,
                /^icon-/,
                // Keep modal/popup classes
                /^modal/,
                /^popup/,
                /^dialog/,
                // Keep form classes
                /^form-/,
                /^input-/,
                /^select-/,
                // Keep layout classes
                /^grid-/,
                /^flex-/,
                /^container/,
                // Keep responsive classes
                /^sm:/,
                /^md:/,
                /^lg:/,
                /^xl:/,
                // Keep hover/focus states
                /^hover:/,
                /^focus:/,
                /^group-/,
                // Keep pseudo-elements
                /^before:/,
                /^after:/,
                // Keep dark mode
                /^dark:/,
                /^light:/,
                // Keep specific classes
                'active',
                'selected',
                'open',
                'closed',
                'visible',
                'hidden',
                'show',
                'hide'
            ]
        });

        const purgedSize = result[0].css.length;
        const reduction = ((1 - purgedSize / originalSize) * 100).toFixed(1);

        // Write purged file
        const purgedFile = cssFile.replace('.css', '.purged.css');
        const purgedPath = path.join(__dirname, '..', purgedFile);
        fs.writeFileSync(purgedPath, result[0].css);

        return {
            success: true,
            file: purgedFile,
            originalSize,
            purgedSize,
            reduction,
            originalFormatted: formatBytes(originalSize),
            purgedFormatted: formatBytes(purgedSize)
        };
    } catch (error) {
        return {
            success: false,
            file: cssFile,
            error: error.message
        };
    }
}

/**
 * Main function
 */
async function main() {
    console.log('🧹 PurgeCSS Script\n');
    console.log('Scanning PHP files for CSS classes...');
    console.log(`Found ${phpFiles.length} PHP files\n`);
    console.log('Processing CSS files...\n');

    let totalOriginal = 0;
    let totalPurged = 0;
    let successCount = 0;
    let skipCount = 0;
    let errorCount = 0;

    for (const file of cssFiles) {
        const result = await purgeFile(file);

        if (result.success) {
            console.log(`✓ ${result.file}`);
            console.log(`  Original: ${result.originalFormatted}`);
            console.log(`  Purged: ${result.purgedFormatted}`);
            console.log(`  Reduction: ${result.reduction}%\n`);

            totalOriginal += result.originalSize;
            totalPurged += result.purgedSize;
            successCount++;
        } else if (result.error === 'File not found') {
            console.log(`⚠  Skipping ${result.file} (not found)\n`);
            skipCount++;
        } else {
            console.log(`✗ ${result.file}`);
            console.log(`  Error: ${result.error}\n`);
            errorCount++;
        }
    }

    // Summary
    console.log('═'.repeat(50));
    console.log('📊 Summary\n');
    console.log(`✓ Purged: ${successCount} files`);
    console.log(`⚠  Skipped: ${skipCount} files`);
    console.log(`✗ Errors: ${errorCount} files\n`);

    console.log(`Total Original Size: ${formatBytes(totalOriginal)}`);
    console.log(`Total Purged Size: ${formatBytes(totalPurged)}`);

    const totalReduction = ((1 - totalPurged / totalOriginal) * 100).toFixed(1);
    console.log(`Total Reduction: ${totalReduction}%\n`);

    const savedBytes = totalOriginal - totalPurged;
    console.log(`💾 Saved: ${formatBytes(savedBytes)}`);
    console.log('═'.repeat(50));

    console.log('\n💡 Next steps:');
    console.log('1. Review .purged.css files');
    console.log('2. Rename to .css if satisfied');
    console.log('3. Run minify-css.js to minify');
    console.log('4. Deploy to production');

    process.exit(errorCount > 0 ? 1 : 0);
}

// Run
main().catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
});
