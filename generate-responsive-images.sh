#!/bin/bash

# Responsive Image Generator
# Generates multiple sizes of images for responsive delivery
# Requires: ImageMagick (convert command)

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
QUALITY_HERO=70
QUALITY_GALLERY=70
QUALITY_LOGO=85

# Hero image sizes (16:9)
HERO_SIZES=(480 768 1024 1600)
HERO_HEIGHTS=(270 432 576 900)

# Gallery image sizes (3:2)
GALLERY_SIZES=(320 480 768 1024)
GALLERY_HEIGHTS=(213 320 512 683)

# Logo sizes (1:1)
LOGO_SIZES=(60 120)

echo -e "${YELLOW}=== Responsive Image Generator ===${NC}\n"

# Function to generate hero images
generate_hero() {
  local input=$1
  local output_base=$2
  
  echo -e "${YELLOW}Generating hero images from: $input${NC}"
  
  for i in "${!HERO_SIZES[@]}"; do
    local size=${HERO_SIZES[$i]}
    local height=${HERO_HEIGHTS[$i]}
    local output="${output_base}-${size}.webp"
    
    if [ -f "$output" ]; then
      echo -e "${GREEN}✓${NC} $output (already exists)"
    else
      echo -n "  Generating ${size}w... "
      convert "$input" \
        -quality $QUALITY_HERO \
        -resize "${size}x${height}!" \
        -strip \
        "$output"
      
      local size_kb=$(du -k "$output" | cut -f1)
      echo -e "${GREEN}✓${NC} ($size_kb KB)"
    fi
  done
}

# Function to generate gallery images
generate_gallery() {
  local input=$1
  local output_base=$2
  
  echo -e "${YELLOW}Generating gallery images from: $input${NC}"
  
  for i in "${!GALLERY_SIZES[@]}"; do
    local size=${GALLERY_SIZES[$i]}
    local height=${GALLERY_HEIGHTS[$i]}
    local output="${output_base}-${size}.webp"
    
    if [ -f "$output" ]; then
      echo -e "${GREEN}✓${NC} $output (already exists)"
    else
      echo -n "  Generating ${size}w... "
      convert "$input" \
        -quality $QUALITY_GALLERY \
        -resize "${size}x${height}!" \
        -strip \
        "$output"
      
      local size_kb=$(du -k "$output" | cut -f1)
      echo -e "${GREEN}✓${NC} ($size_kb KB)"
    fi
  done
}

# Function to generate logo images
generate_logo() {
  local input=$1
  local output_base=$2
  
  echo -e "${YELLOW}Generating logo images from: $input${NC}"
  
  for size in "${LOGO_SIZES[@]}"; do
    local output="${output_base}-${size}.webp"
    
    if [ -f "$output" ]; then
      echo -e "${GREEN}✓${NC} $output (already exists)"
    else
      echo -n "  Generating ${size}w... "
      convert "$input" \
        -quality $QUALITY_LOGO \
        -resize "${size}x${size}!" \
        -strip \
        "$output"
      
      local size_kb=$(du -k "$output" | cut -f1)
      echo -e "${GREEN}✓${NC} ($size_kb KB)"
    fi
  done
}

# Check if convert command exists
if ! command -v convert &> /dev/null; then
  echo -e "${RED}Error: ImageMagick 'convert' command not found${NC}"
  echo "Install with: brew install imagemagick (macOS) or apt-get install imagemagick (Linux)"
  exit 1
fi

# Generate hero images
if [ -f "heroImage/img-4.webp" ]; then
  generate_hero "heroImage/img-4.webp" "heroImage/img-4"
else
  echo -e "${RED}Warning: heroImage/img-4.webp not found${NC}"
fi

# Generate gallery images
if [ -f "about/1.jpg" ]; then
  generate_gallery "about/1.jpg" "about/1"
elif [ -f "about/1.png" ]; then
  generate_gallery "about/1.png" "about/1"
else
  echo -e "${RED}Warning: about/1.jpg or about/1.png not found${NC}"
fi

if [ -f "about/2.jpg" ]; then
  generate_gallery "about/2.jpg" "about/2"
elif [ -f "about/2.png" ]; then
  generate_gallery "about/2.png" "about/2"
else
  echo -e "${RED}Warning: about/2.jpg or about/2.png not found${NC}"
fi

# Generate logo images
if [ -f "assest/logo/right_section.png" ]; then
  generate_logo "assest/logo/right_section.png" "assest/logo/right_section"
else
  echo -e "${RED}Warning: assest/logo/right_section.png not found${NC}"
fi

if [ -f "assest/logo/left_section.png" ]; then
  generate_logo "assest/logo/left_section.png" "assest/logo/left_section"
else
  echo -e "${RED}Warning: assest/logo/left_section.png not found${NC}"
fi

echo -e "\n${GREEN}=== Image generation complete ===${NC}"
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Verify generated images in heroImage/, about/, and assest/logo/ directories"
echo "2. Check file sizes match targets (hero: 50-150KB, gallery: 25-80KB, logo: 5-15KB)"
echo "3. Deploy images to production server"
echo "4. Run Lighthouse audit to verify performance improvements"
