#!/bin/bash

# CRITICAL FIXES DEPLOYMENT SCRIPT
# Deploys all 10 critical fixes to production

set -e

echo "=========================================="
echo "CRITICAL FIXES DEPLOYMENT"
echo "=========================================="
echo ""

# Step 1: Generate optimized images
echo "Step 1: Generating optimized images..."
php api/batch-image-optimizer-production.php
echo "✓ Images optimized"
echo ""

# Step 2: Verify images exist
echo "Step 2: Verifying optimized images..."
if [ -d "assest/optimized" ]; then
    IMAGE_COUNT=$(find assest/optimized -name "*.webp" | wc -l)
    echo "✓ Found $IMAGE_COUNT optimized images"
else
    echo "✗ ERROR: assest/optimized directory not found"
    exit 1
fi
echo ""

# Step 3: Check file sizes
echo "Step 3: Checking image sizes..."
TOTAL_SIZE=$(du -sh assest/optimized | cut -f1)
echo "✓ Total optimized images size: $TOTAL_SIZE"
echo ""

# Step 4: Verify critical files exist
echo "Step 4: Verifying critical files..."
FILES=(
    "index.php"
    "Home.css"
    "accessibility-fixes.css"
    "js/dom-batch-optimizer.js"
    "api/responsive-image-helper-production.php"
    "api/batch-image-optimizer-production.php"
    "api/image-compression-strategy.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file"
    else
        echo "✗ ERROR: $file not found"
        exit 1
    fi
done
echo ""

# Step 5: Git commit
echo "Step 5: Committing changes..."
git add -A
git commit -m "Fix: All 10 critical issues - responsive images, accessibility, performance

- Issue 1: Responsive images with srcset (320w, 480w, 768w, 1024w, 1600w)
- Issue 2: Hero image LCP optimization (preload, eager loading, fetchpriority)
- Issue 3: Image compression (≤50KB-250KB per breakpoint)
- Issue 4: Forced reflow prevention (DOM batch optimizer)
- Issue 5: Color contrast fixes (4.5:1 minimum)
- Issue 6: Link accessibility (aria-labels)
- Issue 7: Touch targets (48px minimum)
- Issue 8: Heading structure (proper hierarchy)
- Issue 9: Logo image optimization (200px WebP)
- Issue 10: Page size reduction (<1.5MB)

Performance targets:
- LCP < 2.5s
- Page size < 1.5MB
- Lighthouse Mobile ≥ 90
- Contrast ratio ≥ 4.5:1
- Touch targets ≥ 48px"

echo "✓ Changes committed"
echo ""

# Step 6: Push to GitHub
echo "Step 6: Pushing to GitHub..."
git push origin main
echo "✓ Pushed to GitHub"
echo ""

# Step 7: Summary
echo "=========================================="
echo "DEPLOYMENT COMPLETE ✓"
echo "=========================================="
echo ""
echo "Summary:"
echo "- Optimized images: $IMAGE_COUNT files"
echo "- Total size: $TOTAL_SIZE"
echo "- All critical files verified"
echo "- Changes committed and pushed"
echo ""
echo "Next steps:"
echo "1. Monitor GitHub Actions deployment"
echo "2. Run Lighthouse audit on production"
echo "3. Test on mobile and desktop"
echo "4. Verify performance metrics"
echo ""
echo "Documentation:"
echo "- CRITICAL_FIXES_COMPLETE_SUMMARY.md"
echo "- CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md"
echo "- RESPONSIVE_IMAGES_QUICK_REFERENCE.md"
echo "- IMAGE_OPTIMIZATION_PRODUCTION_CHECKLIST.md"
echo ""
