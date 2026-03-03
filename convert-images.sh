#!/bin/bash
# Image Conversion Script - Generate responsive WebP + AVIF variants
# Usage: chmod +x convert-images.sh && ./convert-images.sh

set -e

SOURCE_DIR="./heroImage"
OUTPUT_DIR="./heroImage"

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🖼️  Starting image optimization...${NC}"
echo "Source: $SOURCE_DIR"
echo "Output: $OUTPUT_DIR"
echo ""

# Define sizes and quality settings
declare -a SIZES=(320 480 768 1200)
WEBP_QUALITY=65
AVIF_QUALITY=50

# Function to convert image
convert_image() {
    local input=$1
    local basename=$(basename "$input" | sed 's/\.[^.]*$//')
    
    echo -e "${BLUE}Converting $basename...${NC}"
    
    for size in "${SIZES[@]}"; do
        # Skip 1200w for mobile-only images
        if [[ "$basename" == *"mobile"* ]] && [ $size -gt 768 ]; then
            continue
        fi
        
        # Convert to WebP
        if command -v ffmpeg &> /dev/null; then
            ffmpeg -i "$input" \
                -vf "scale=$size:-1" \
                -q:v $WEBP_QUALITY \
                "$OUTPUT_DIR/${basename}-${size}w.webp" \
                -y 2>/dev/null
            
            # Convert to AVIF
            ffmpeg -i "$input" \
                -vf "scale=$size:-1" \
                -c:v libaom-av1 \
                -crf $AVIF_QUALITY \
                "$OUTPUT_DIR/${basename}-${size}w.avif" \
                -y 2>/dev/null
            
            echo -e "${GREEN}✓ Created ${basename}-${size}w (WebP + AVIF)${NC}"
        else
            echo "FFmpeg not found. Install with: brew install ffmpeg"
            exit 1
        fi
    done
}

# Process all PNG/JPG files
for file in "$SOURCE_DIR"/*.{png,jpg,jpeg}; do
    [ -e "$file" ] && convert_image "$file"
done

echo ""
echo -e "${GREEN}✓ Image conversion complete!${NC}"
echo ""
echo "Generated files:"
ls -lh "$OUTPUT_DIR"/*.{webp,avif} 2>/dev/null | awk '{print $9, "(" $5 ")"}'
echo ""
echo "Next steps:"
echo "1. Verify file sizes (should be <150KB for hero, <80KB for cards)"
echo "2. Delete original oversized images: rm $SOURCE_DIR/*.png $SOURCE_DIR/*.jpg"
echo "3. Deploy to production"
