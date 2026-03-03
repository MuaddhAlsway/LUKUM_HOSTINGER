#!/bin/bash

# LAKUM Batch Image Optimizer
# Converts all images to responsive WebP sizes
# Usage: ./optimize-images-batch.sh

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
HERO_QUALITY=75
IMAGE_QUALITY=70
THUMB_QUALITY=65

# Sizes: width x height
declare -A SIZES=(
  ["320"]="320x240"
  ["480"]="480x360"
  ["768"]="768x576"
  ["1024"]="1024x768"
  ["1600"]="1600x1200"
)

# Hero sizes (16:9)
declare -A HERO_SIZES=(
  ["480"]="480x270"
  ["768"]="768x432"
  ["1024"]="1024x576"
  ["1600"]="1600x900"
)

echo -e "${YELLOW}LAKUM Image Optimizer${NC}"
echo "========================"

# Function to optimize image
optimize_image() {
  local input=$1
  local output_prefix=$2
  local quality=$3
  local is_hero=$4
  
  if [ ! -f "$input" ]; then
    echo -e "${RED}✗ File not found: $input${NC}"
    return 1
  fi
  
  echo -e "${YELLOW}Processing: $input${NC}"
  
  if [ "$is_hero" = true ]; then
    # Hero images (16:9)
    for size in "${!HERO_SIZES[@]}"; do
      dimensions=${HERO_SIZES[$size]}
      output="${output_prefix}-${size}.webp"
      
      if command -v magick &> /dev/null; then
        magick convert "$input" -resize "$dimensions" -quality "$quality" -strip "$output"
      elif command -v ffmpeg &> /dev/null; then
        ffmpeg -i "$input" -vf "scale=$dimensions" -q:v 5 -y "$output" 2>/dev/null
      else
        echo -e "${RED}✗ ImageMagick or FFmpeg not found${NC}"
        return 1
      fi
      
      size_kb=$(du -k "$output" | cut -f1)
      echo -e "${GREEN}✓ Created: $output (${size_kb}KB)${NC}"
    done
  else
    # Regular images (4:3)
    for size in "${!SIZES[@]}"; do
      dimensions=${SIZES[$size]}
      output="${output_prefix}-${size}.webp"
      
      if command -v magick &> /dev/null; then
        magick convert "$input" -resize "$dimensions" -quality "$quality" -strip "$output"
      elif command -v ffmpeg &> /dev/null; then
        ffmpeg -i "$input" -vf "scale=$dimensions" -q:v 5 -y "$output" 2>/dev/null
      else
        echo -e "${RED}✗ ImageMagick or FFmpeg not found${NC}"
        return 1
      fi
      
      size_kb=$(du -k "$output" | cut -f1)
      echo -e "${GREEN}✓ Created: $output (${size_kb}KB)${NC}"
    done
  fi
}

# Process hero images
echo -e "\n${YELLOW}Processing Hero Images...${NC}"
if [ -d "heroImage" ]; then
  for img in heroImage/*.{jpg,jpeg,png}; do
    if [ -f "$img" ]; then
      base=$(basename "$img" | sed 's/\.[^.]*$//')
      optimize_image "$img" "heroImage/$base" "$HERO_QUALITY" true
    fi
  done
fi

# Process regular images
echo -e "\n${YELLOW}Processing Regular Images...${NC}"
if [ -d "assest" ]; then
  for img in assest/*.{jpg,jpeg,png}; do
    if [ -f "$img" ]; then
      base=$(basename "$img" | sed 's/\.[^.]*$//')
      optimize_image "$img" "assest/$base" "$IMAGE_QUALITY" false
    fi
  done
fi

# Process gallery images
echo -e "\n${YELLOW}Processing Gallery Images...${NC}"
if [ -d "gallery" ]; then
  for img in gallery/*.{jpg,jpeg,png}; do
    if [ -f "$img" ]; then
      base=$(basename "$img" | sed 's/\.[^.]*$//')
      optimize_image "$img" "gallery/$base" "$IMAGE_QUALITY" false
    fi
  done
fi

echo -e "\n${GREEN}✓ Image optimization complete!${NC}"
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Verify image quality in browser"
echo "2. Update HTML with responsive image tags"
echo "3. Deploy to production"
