/**
 * LAKUM Spaces - Dynamic Pricing Loader with Bilingual Support
 * Loads pricing from database and displays in current language
 */

document.addEventListener('DOMContentLoaded', async function() {
    const pricingGrid = document.getElementById('pricingGrid');
    if (!pricingGrid) {
        console.warn('pricingGrid element not found');
        return;
    }

    // Get current language
    const urlParams = new URLSearchParams(window.location.search);
    const lang = urlParams.get('lang') || localStorage.getItem('lakum_language') || 'en';
    
    console.log('Loading pricing with language:', lang);
    
    try {
        // Fetch pricing from API - use absolute path
        const apiUrl = window.location.pathname.includes('/admin/') 
            ? '../api/get_pricing.php' 
            : '/LUKUM(main)/api/get_pricing.php';
        
        console.log('Fetching from:', apiUrl);
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('API Response:', data);
        
        if (!data.success || !data.data || data.data.length === 0) {
            console.warn('No pricing data available');
            return;
        }
        
        // Clear existing pricing cards
        pricingGrid.innerHTML = '';
        
        // Render pricing cards
        data.data.forEach(pricing => {
            const card = createPricingCard(pricing, lang);
            pricingGrid.appendChild(card);
        });
        
        console.log('Pricing loaded successfully with', data.data.length, 'items');
        
    } catch (error) {
        console.error('Error loading pricing:', error);
    }
});

function createPricingCard(pricing, lang) {
    const wrapper = document.createElement('div');
    wrapper.className = 'pricing-card-wrapper';
    wrapper.setAttribute('data-pricing-id', pricing.id);
    
    // Get name and description based on language
    const name = lang === 'ar' ? (pricing.name_ar || pricing.title) : (pricing.name_en || pricing.title);
    const description = lang === 'ar' ? pricing.description_ar : pricing.description_en;
    const duration = lang === 'ar' ? pricing.duration_ar : pricing.duration_en;
    
    // Format price display
    let priceDisplay = '';
    if (pricing.price_sec) {
        // Multiple prices (like Hourly Rate)
        priceDisplay = `
            <div class="pricing-accordion__price pricing-accordion__price--multi">
                <div>${pricing.price_sec}</div>
            </div>
        `;
    } else if (pricing.price) {
        // Single price
        priceDisplay = `
            <div class="pricing-accordion__price">
                <span class="pricing-accordion__amount">${formatPrice(pricing.price)}</span>
                <span class="pricing-accordion__currency">${pricing.price_unit || 'SAR'}</span>
            </div>
        `;
    }
    
    // Build content HTML
    let contentHTML = description || '';
    
    // If no description, use default content
    if (!contentHTML) {
        contentHTML = getDefaultContent(pricing.id, lang);
    }
    
    // Add RTL direction for Arabic content
    const contentDir = lang === 'ar' ? 'dir="rtl"' : '';
    
    wrapper.innerHTML = `
        <details class="pricing-accordion">
            <summary class="pricing-accordion__header">
                <div class="pricing-accordion__info">
                    <h3 class="pricing-accordion__name" ${contentDir}>${escapeHtml(name)}</h3>
                    ${priceDisplay}
                    <span class="pricing-accordion__vat">${pricing.vat_note || '*(excluding VAT)'}</span>
                </div>
                <span class="pricing-accordion__icon"></span>
            </summary>
            <div class="pricing-accordion__content" ${contentDir}>
                ${contentHTML}
            </div>
        </details>
        <div class="pricing-button-fixed">
            <a href="#form" class="lakum-btn lakum-btn--primary">Book Now</a>
        </div>
    `;
    
    return wrapper;
}

function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getDefaultContent(pricingId, lang) {
    const defaults = {
        1: {
            en: `<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, and reliable electrical supply.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event's specific requirements.</p></div></div>`,
            ar: `<div class="pricing-accordion__service"><span></span><div><strong>خدمات الدعم</strong><p>توفير دعم لوجستي وتقني شامل، بما في ذلك إدارة تدفق الدخول وموظفي المساعدة في الموقع وخدمات التنظيف الاحترافية.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>الخدمات التشغيلية</strong><p>إدارة العمليات التقنية الأساسية، بما يغطي الإضاءة وأنظمة الصوت وتكييف الهواء والإمداد الكهربائي الموثوق.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>إعداد الأحداث المخصصة</strong><p>توفير أثاث وعناصر عرض إضافية، متاحة حسب الطلب ومصممة خصيصاً لتناسب رؤية حدثك الفريدة.</p></div></div>`
        },
        2: {
            en: `<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, reliable electrical supply, and the provision of a projector and screen.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event's specific requirements.</p></div></div>`,
            ar: `<div class="pricing-accordion__service"><span></span><div><strong>خدمات الدعم</strong><p>توفير دعم لوجستي وتقني شامل، بما في ذلك إدارة تدفق الدخول وموظفي المساعدة في الموقع وخدمات التنظيف الاحترافية.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>الخدمات التشغيلية</strong><p>إدارة العمليات التقنية الأساسية، بما يغطي الإضاءة وأنظمة الصوت وتكييف الهواء والإمداد الكهربائي الموثوق، وتوفير جهاز عرض وشاشة.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>إعداد الأحداث المخصصة</strong><p>توفير أثاث وعناصر عرض إضافية، متاحة حسب الطلب ومصممة خصيصاً لتناسب رؤية حدثك الفريدة.</p></div></div>`
        }
    };
    
    return defaults[pricingId]?.[lang] || '';
}
