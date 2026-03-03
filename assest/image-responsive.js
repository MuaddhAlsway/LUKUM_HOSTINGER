/* Responsive Image System */
(function() {
  const imageMap = {
    'hero': {
      mobile: { src: 'heroImage/img-4-600.webp', avif: 'heroImage/img-4-600.avif', width: 600, height: 350 },
      tablet: { src: 'heroImage/img-4-1000.webp', avif: 'heroImage/img-4-1000.avif', width: 1000, height: 583 },
      desktop: { src: 'heroImage/img-4-1400.webp', avif: 'heroImage/img-4-1400.avif', width: 1400, height: 816 }
    },
    'card': {
      mobile: { src: 'heroImage/card-400.webp', avif: 'heroImage/card-400.avif', width: 400, height: 300 },
      desktop: { src: 'heroImage/card-600.webp', avif: 'heroImage/card-600.avif', width: 600, height: 450 }
    }
  };

  window.getResponsiveImage = function(type) {
    const breakpoint = window.innerWidth <= 768 ? 'mobile' : window.innerWidth <= 1024 ? 'tablet' : 'desktop';
    return imageMap[type]?.[breakpoint] || imageMap[type]?.desktop;
  };

  window.createPictureElement = function(type, alt, loading = 'lazy') {
    const img = getResponsiveImage(type);
    if (!img) return null;

    const picture = document.createElement('picture');
    
    const avifSource = document.createElement('source');
    avifSource.type = 'image/avif';
    avifSource.srcset = img.avif;
    picture.appendChild(avifSource);

    const webpSource = document.createElement('source');
    webpSource.type = 'image/webp';
    webpSource.srcset = img.src;
    picture.appendChild(webpSource);

    const imgEl = document.createElement('img');
    imgEl.src = img.src;
    imgEl.alt = alt;
    imgEl.loading = loading;
    imgEl.decoding = 'async';
    imgEl.width = img.width;
    imgEl.height = img.height;
    picture.appendChild(imgEl);

    return picture;
  };
})();
