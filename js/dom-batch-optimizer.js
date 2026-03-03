/**
 * DOM Batch Optimizer
 * Prevents layout thrashing by batching DOM reads and writes
 * 
 * Layout thrashing occurs when you read DOM properties after writing to them,
 * forcing the browser to recalculate layout multiple times.
 * 
 * This class batches all reads first, then all writes, using requestAnimationFrame
 * to ensure optimal performance.
 * 
 * Usage:
 * const batch = new DOMBatchOptimizer();
 * batch.read(() => { ... });
 * batch.write(() => { ... });
 * batch.flush();
 * 
 * Or use the convenience methods:
 * DOMBatchOptimizer.batchRead(callback);
 * DOMBatchOptimizer.batchWrite(callback);
 * DOMBatchOptimizer.flush();
 */

class DOMBatchOptimizer {
    constructor() {
        this.reads = [];
        this.writes = [];
        this.scheduled = false;
    }

    /**
     * Queue a DOM read operation
     * @param {Function} callback - Function that reads from DOM
     */
    read(callback) {
        if (typeof callback !== 'function') {
            console.error('DOMBatchOptimizer.read: callback must be a function');
            return;
        }
        this.reads.push(callback);
        this.schedule();
    }

    /**
     * Queue a DOM write operation
     * @param {Function} callback - Function that writes to DOM
     */
    write(callback) {
        if (typeof callback !== 'function') {
            console.error('DOMBatchOptimizer.write: callback must be a function');
            return;
        }
        this.writes.push(callback);
        this.schedule();
    }

    /**
     * Schedule batch execution using requestAnimationFrame
     * @private
     */
    schedule() {
        if (this.scheduled) return;
        this.scheduled = true;
        
        requestAnimationFrame(() => this.flush());
    }

    /**
     * Execute all reads, then all writes
     * This ensures layout is only recalculated once
     */
    flush() {
        // Execute all reads first (no layout recalculation)
        this.reads.forEach(callback => {
            try {
                callback();
            } catch (error) {
                console.error('Error in read callback:', error);
            }
        });
        this.reads = [];

        // Then execute all writes (single layout recalculation)
        this.writes.forEach(callback => {
            try {
                callback();
            } catch (error) {
                console.error('Error in write callback:', error);
            }
        });
        this.writes = [];

        this.scheduled = false;
    }

    /**
     * Get number of queued operations
     */
    getQueueSize() {
        return this.reads.length + this.writes.length;
    }

    /**
     * Clear all queued operations
     */
    clear() {
        this.reads = [];
        this.writes = [];
        this.scheduled = false;
    }
}

// Create global instance for convenience
window.DOMBatchOptimizer = DOMBatchOptimizer;

// Convenience static methods
DOMBatchOptimizer.instance = new DOMBatchOptimizer();

DOMBatchOptimizer.batchRead = function(callback) {
    DOMBatchOptimizer.instance.read(callback);
};

DOMBatchOptimizer.batchWrite = function(callback) {
    DOMBatchOptimizer.instance.write(callback);
};

DOMBatchOptimizer.flush = function() {
    DOMBatchOptimizer.instance.flush();
};

/**
 * EXAMPLE USAGE:
 * 
 * // ❌ BAD - Causes layout thrashing
 * function animateCards() {
 *     const cards = document.querySelectorAll('.card');
 *     cards.forEach(card => {
 *         const width = card.offsetWidth;  // REFLOW 1
 *         card.style.width = width * 1.1 + 'px';  // REFLOW 2
 *     });
 * }
 * 
 * // ✅ GOOD - Batched reads and writes
 * function animateCards() {
 *     const batch = new DOMBatchOptimizer();
 *     const cards = document.querySelectorAll('.card');
 *     const measurements = [];
 * 
 *     // Batch all reads
 *     batch.read(() => {
 *         cards.forEach(card => {
 *             measurements.push({
 *                 element: card,
 *                 width: card.offsetWidth
 *             });
 *         });
 *     });
 * 
 *     // Batch all writes
 *     batch.write(() => {
 *         measurements.forEach(({ element, width }) => {
 *             element.style.width = width * 1.1 + 'px';
 *         });
 *     });
 * 
 *     batch.flush();
 * }
 * 
 * // Or use convenience methods:
 * DOMBatchOptimizer.batchRead(() => {
 *     // Read operations
 * });
 * 
 * DOMBatchOptimizer.batchWrite(() => {
 *     // Write operations
 * });
 * 
 * DOMBatchOptimizer.flush();
 */
