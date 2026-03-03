#!/usr/bin/env node

/**
 * CSS Minification Script
 * Minifies all CSS files using PostCSS and cssnano
 * 
 * Installation:
 * npm install --save-dev postcss cssnano
 * 
 * Usage:
 * node scripts/minify-css.js
 * 
 * Output:
 * ✓ global-styles.min.css (40KB, 20% reduction)
 * ✓ lakum-components.min.css (32KB, 20% reduction)
 * etc.
 */

const postcss = require('postcss');
const cssnano = require('cssnano');
const fs = require('fs');
const path = require('path');

// CSS files to minify
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
    'calendar.css',
    'critical.css'
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
 * Minify a single CSS file
 */
async function minifyFile(filePath) {
    try {
        // Read file
        const css = fs.readFileSync(filePath, 'utf8');
        const originalSize = css.length;

        // Minify with cssnano
        const result = await postcss([
            cssnano({
                preset: ['default', {
                    discardComments: {
                        removeAll: true,
                    },
                    normalizeUnicode: false,
                    normalizeUrl: false,
                    reduceIdents: false,
                    zindex: false,
                }]
            })
        ]).process(css, { from: filePath });

        const minifiedSize = result.css.length;
        const reduction = ((1 - minifiedSize / originalSize) * 100).toFixed(1);

        // Write minified file
        const minFile = filePath.replace('.css', '.min.css');
        fs.writeFileSync(minFile, result.css);

        return {
            success: true,
            file: path.basename(minFile),
            originalSize,
            minifiedSize,
            reduction,
            originalFormatted: formatBytes(originalSize),
            minifiedFormatted: formatBytes(minifiedSize)
        };
    } catch (error) {
        return {
            success: false,
            file: path.basename(filePath),
            error: error.message
        };
    }
}

/**
 * Main function
 */
async function main() {
    console.log('🔧 CSS Minification Script\n');
    console.log('Processing CSS files...\n');

    let totalOriginal = 0;
    let totalMinified = 0;
    let successCount = 0;
    let skipCount = 0;
    let errorCount = 0;

    for (const file of cssFiles) {
        const filePath = path.join(__dirname, '..', file);

        // Check if file exists
        if (!fs.existsSync(filePath)) {
            console.log(`⚠  Skipping ${file} (not found)`);
            skipCount++;
            continue;
        }

        // Minify file
        const result = await minifyFile(filePath);

        if (result.success) {
            console.log(`✓ ${result.file}`);
            console.log(`  Original: ${result.originalFormatted}`);
            console.log(`  Minified: ${result.minifiedFormatted}`);
            console.log(`  Reduction: ${result.reduction}%\n`);

            totalOriginal += result.originalSize;
            totalMinified += result.minifiedSize;
            successCount++;
        } else {
            console.log(`✗ ${result.file}`);
            console.log(`  Error: ${result.error}\n`);
            errorCount++;
        }
    }

    // Summary
    console.log('═'.repeat(50));
    console.log('📊 Summary\n');
    console.log(`✓ Minified: ${successCount} files`);
    console.log(`⚠  Skipped: ${skipCount} files`);
    console.log(`✗ Errors: ${errorCount} files\n`);

    console.log(`Total Original Size: ${formatBytes(totalOriginal)}`);
    console.log(`Total Minified Size: ${formatBytes(totalMinified)}`);

    const totalReduction = ((1 - totalMinified / totalOriginal) * 100).toFixed(1);
    console.log(`Total Reduction: ${totalReduction}%\n`);

    const savedBytes = totalOriginal - totalMinified;
    console.log(`💾 Saved: ${formatBytes(savedBytes)}`);
    console.log('═'.repeat(50));

    process.exit(errorCount > 0 ? 1 : 0);
}

// Run
main().catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
});
