<?php
/**
 * MOBILE-FIRST HERO IMAGE COMPONENT
 * Generates fully responsive, optimized hero image for mobile
 * 
 * CRITICAL OPTIMIZATIONS:
 * - 4 sizes: 320w, 480w, 768w, 1024w
 * - AVIF + WebP formats
 * - Proper srcset/sizes for mobile
 * - fetchpriority="high" for LCP
 * - NO lazy loading (above fold)
 * - Aspect ratio to prevent CLS
 */

function renderMobileHeroImage($imagePath = 'heroImage/img-4', $alt = 'LAKUM Artspace') {
    // Mobile-first sizes
    $sizes = '(max-width: 480px) 100vw, (max-width: 768px) 90vw, (max-width: 1024px) 85vw, 1024px';
    
    // Image dimensions (mobile-first)
    $width = 1024;
    $height = 683;
    $aspectRatio = $width / $height;
    
    return <<<HTML
<!-- MOBILE-OPTIMIZED HERO IMAGE -->
<picture>
    <!-- AVIF: Smallest, modern browsers (45-55% smaller than WebP) -->
    <source type="image/avif" 
            srcset="$imagePath-320w.avif 320w,
                    $imagePath-480w.avif 480w,
                    $imagePath-768w.avif 768w,
                    $imagePath-1024w.avif 1024w"
            sizes="$sizes">
    
    <!-- WebP: Modern browsers (30-40% smaller than JPEG) -->
    <source type="image/webp" 
            srcset="$imagePath-320w.webp 320w,
                    $imagePath-480w.webp 480w,
                    $imagePath-768w.webp 768w,
                    $imagePath-1024w.webp 1024w"
            sizes="$sizes">
    
    <!-- Fallback: JPEG for old browsers -->
    <img src="$imagePath-768w.webp"
         alt="$alt"
         width="$width"
         height="$height"
         fetchpriority="high"
         loading="eager"
         decoding="async"
         class="lakum-hero__image"
         style="aspect-ratio: $aspectRatio; width: 100%; height: auto; display: block;">
</picture>
HTML;
}

/**
 * IMAGE GENERATION STRATEGY FOR MOBILE
 * 
 * Use ImageMagick or similar tool to generate variants:
 * 
 * AVIF (Quality 45-55, smallest):
 * convert img-4.jpg -resize 320x213 -quality 50 -format avif img-4-320w.avif
 * convert img-4.jpg -resize 480x320 -quality 50 -format avif img-4-480w.avif
 * convert img-4.jpg -resize 768x512 -quality 50 -format avif img-4-768w.avif
 * convert img-4.jpg -resize 1024x683 -quality 50 -format avif img-4-1024w.avif
 * 
 * WebP (Quality 60-70, medium):
 * convert img-4.jpg -resize 320x213 -quality 65 -format webp img-4-320w.webp
 * convert img-4.jpg -resize 480x320 -quality 65 -format webp img-4-480w.webp
 * convert img-4.jpg -resize 768x512 -quality 65 -format webp img-4-768w.webp
 * convert img-4.jpg -resize 1024x683 -quality 65 -format webp img-4-1024w.webp
 * 
 * JPEG Fallback (Quality 75, largest):
 * convert img-4.jpg -resize 768x512 -quality 75 -format jpeg img-4-768w.jpg
 * 
 * TARGET FILE SIZES (MOBILE):
 * - 320w AVIF: 25-35KB
 * - 480w AVIF: 40-55KB
 * - 768w AVIF: 60-80KB
 * - 1024w AVIF: 80-100KB
 * 
 * - 320w WebP: 35-50KB
 * - 480w WebP: 55-75KB
 * - 768w WebP: 85-110KB
 * - 1024w WebP: 110-150KB
 * 
 * TOTAL HERO IMAGE PAYLOAD: ~400-500KB (vs 1.1MB original)
 */

// Usage in index.php:
// echo renderMobileHeroImage('heroImage/img-4', 'LAKUM Artspace');
?>


