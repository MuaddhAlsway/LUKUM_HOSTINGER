#!/bin/bash
# Image Conversion Script for LAKUM Artspace
# Converts images to responsive WebP + AVIF variants
# Usage: bash IMAGE_CONVERSION_COMMANDS.sh

set -e

echo "🖼️  LAKUM Artspace - Image Optimization"
echo "========================================"

# Check if FFmpeg is installed
if ! command -v ffmpeg &> /dev/null; then
    echo "❌ FFmpeg not found. Install it:"
    echo "   macOS: brew install ffmpeg"
    echo "   Ubuntu: sudo apt-get install ffmpeg"
    echo "   Windows: choco install ffmpeg"
    exit 1
fi

echo "✓ FFmpeg found"

# Create output directory
mkdir -p heroImage

# Define image sizes and quality
SIZES=(320 480 768 1200)
WEBP_QUALITY=65
AVIF_QUALITY=50

# Function to convert image
convert_image() {
    local input=$1
    local basename=$(basename "$input" | sed 's/\.[^.]*$//')
    
    if [ ! -f "$input" ]; then
        echo "⚠️  File not found: $input"
        return
    fi
    
    echo ""
    echo "Converting: $basename"
    echo "─────────────────────────────────────"
    
    for size in "${SIZES[@]}"; do
        echo "  → ${size}w..."
        
        # WebP conversion
        ffmpeg -i "$input" \
            -vf "scale=$size:-1" \
            -q:v $WEBP_QUALITY \
            "heroImage/${basename}-${size}w.webp" \
            -y 2>/dev/null
        
        # AVIF conversion
        ffmpeg -i "$input" \
            -vf "scale=$size:-1" \
            -c:v libaom-av1 \
            -crf $AVIF_QUALITY \
            "heroImage/${basename}-${size}w.avif" \
            -y 2>/dev/null
        
        # Get file sizes
        webp_size=$(du -h "heroImage/${basename}-${size}w.webp" | cut -f1)
        avif_size=$(du -h "heroImage/${basename}-${size}w.avif" | cut -f1)
        
        echo "     ✓ WebP: $webp_size | AVIF: $avif_size"
    done
}

# Convert hero images
echo ""
echo "📸 HERO IMAGES"
echo "=============="

# Check for original images
if [ -f "heroImage/img-4.png" ]; then
    convert_image "heroImage/img-4.png"
elif [ -f "heroImage/img-4.jpg" ]; then
    convert_image "heroImage/img-4.jpg"
else
    echo "⚠️  img-4 not found (PNG or JPG)"
fi

if [ -f "heroImage/img-3.png" ]; then
    convert_image "heroImage/img-3.png"
elif [ -f "heroImage/img-3.jpg" ]; then
    convert_image "heroImage/img-3.jpg"
else
    echo "⚠️  img-3 not found (PNG or JPG)"
fi

# Summary
echo ""
echo "✅ CONVERSION COMPLETE"
echo "======================"
echo ""
echo "Generated files:"
ls -lh heroImage/*-{320,480,768,1200}w.{webp,avif} 2>/dev/null | awk '{print "  " $9 " (" $5 ")"}'

echo ""
echo "📊 TOTAL SIZE:"
du -sh heroImage/ | awk '{print "  " $1}'

echo ""
echo "🗑️  CLEANUP (optional):"
echo "  rm heroImage/img-4.png heroImage/img-3.png"
echo "  (Keep only WebP/AVIF variants)"

echo ""
echo "✓ Done!"
