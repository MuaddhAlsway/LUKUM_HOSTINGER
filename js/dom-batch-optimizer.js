/**
 * DOM Batch Optimizer - Prevents forced reflows
 * Batches DOM reads and writes to avoid layout thrashing
 */

class DOMBatchOptimizer {
  constructor() {
    this.readQueue = [];
    this.writeQueue = [];
    this.isScheduled = false;
  }

  read(callback) {
    this.readQueue.push(callback);
    this.schedule();
  }

  write(callback) {
    this.writeQueue.push(callback);
    this.schedule();
  }

  schedule() {
    if (this.isScheduled) return;
    this.isScheduled = true;
    requestAnimationFrame(() => this.flush());
  }

  flush() {
    // Execute all reads first
    this.readQueue.forEach(cb => cb());
    this.readQueue = [];

    // Then execute all writes
    this.writeQueue.forEach(cb => cb());
    this.writeQueue = [];

    this.isScheduled = false;
  }
}

window.DOMBatch = new DOMBatchOptimizer();

// Example usage:
// DOMBatch.read(() => {
//   const width = element.offsetWidth;
// });
// DOMBatch.write(() => {
//   element.style.width = width + 'px';
// });
