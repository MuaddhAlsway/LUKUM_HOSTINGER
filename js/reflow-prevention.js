/**
 * Reflow Prevention Utility
 * Prevents forced reflows by batching DOM reads/writes
 */

const ReflowPrevention = {
  /**
   * Batch DOM operations to prevent forced reflows
   * @param {Function} readFn - Function that reads layout properties
   * @param {Function} writeFn - Function that writes to DOM
   */
  batch(readFn, writeFn) {
    requestAnimationFrame(() => {
      const readData = readFn();
      requestAnimationFrame(() => {
        writeFn(readData);
      });
    });
  },

  /**
   * Safe element dimension read
   * @param {Element} element
   * @returns {Object} { width, height, top, left }
   */
  readDimensions(element) {
    return {
      width: element.offsetWidth,
      height: element.offsetHeight,
      top: element.offsetTop,
      left: element.offsetLeft,
      rect: element.getBoundingClientRect()
    };
  },

  /**
   * Safe element style write
   * @param {Element} element
   * @param {Object} styles - CSS properties to apply
   */
  writeStyles(element, styles) {
    requestAnimationFrame(() => {
      Object.assign(element.style, styles);
    });
  },

  /**
   * Batch read multiple elements
   * @param {Array<Element>} elements
   * @returns {Array<Object>} Dimensions for each element
   */
  readMultiple(elements) {
    return elements.map(el => this.readDimensions(el));
  },

  /**
   * Batch write to multiple elements
   * @param {Array<{element: Element, styles: Object}>} updates
   */
  writeMultiple(updates) {
    requestAnimationFrame(() => {
      updates.forEach(({ element, styles }) => {
        Object.assign(element.style, styles);
      });
    });
  },

  /**
   * Debounced resize handler
   * @param {Function} callback
   * @param {Number} delay - Debounce delay in ms
   */
  onResize(callback, delay = 300) {
    let timeoutId;
    const handler = () => {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(callback, delay);
    };
    window.addEventListener('resize', handler);
    return () => window.removeEventListener('resize', handler);
  },

  /**
   * Intersection Observer for lazy operations
   * @param {Element} element
   * @param {Function} callback
   * @param {Object} options
   */
  observeIntersection(element, callback, options = {}) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          callback(entry);
        }
      });
    }, {
      threshold: 0.1,
      ...options
    });
    observer.observe(element);
    return observer;
  }
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ReflowPrevention;
}
