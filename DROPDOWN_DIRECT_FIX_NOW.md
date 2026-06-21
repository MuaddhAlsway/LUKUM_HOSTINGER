# ⚡ DROPDOWN NOT SHOWING? DIRECT FIX

## THE PROBLEM EXPLAINED

Your CSS shows:
```css
.lakum-nav__dropdown {
    opacity: 0 !important;        /* Hidden by default */
    visibility: hidden !important; /* Not visible */
}

.lakum-nav__item--dropdown.active .lakum-nav__dropdown {
    opacity: 1 !important;        /* Should become visible */
    visibility: visible !important;
}
```

**This means:**
- Dropdown starts HIDDEN (opacity 0, visibility hidden)
- When you click arrow, it adds `.active` class to parent
- CSS rule kicks in and makes it VISIBLE (opacity 1, visibility visible)

**If it's NOT showing:** The `.active` class is NOT being added!

---

## VERIFICATION CHECKLIST

### Step 1: Open Browser DevTools
```
Press F12
Go to Console tab
```

### Step 2: Run This Command
Paste into console:
```javascript
document.querySelectorAll('.lakum-nav__dropdown-toggle').length
```

**Expected:** Should show `9` (number of nav items)  
**If shows 0:** HTML structure is wrong

### Step 3: Check If Event Listener Exists
```javascript
const toggle = document.querySelector('.lakum-nav__dropdown-toggle');
toggle.onclick
```

**Expected:** Should show the function reference  
**If shows null:** Event listener not attached

### Step 4: Manually Test Click
```javascript
const item = document.querySelector('.lakum-nav__item--dropdown');
console.log('Before click:', item.classList.contains('active')); // Should be false
item.classList.add('active');
console.log('After manual add:', item.classList.contains('active')); // Should be true
```

Then VISUALLY check: Does the dropdown appear now?

**If YES:** CSS is working, JavaScript is the problem  
**If NO:** CSS is the problem

---

## QUICK FIX: Force Dropdown Visible

If nothing is working, try this **temporary fix** to prove the CSS works:

### Edit: `lakum-header-dropdowns.css`

Find this line:
```css
.lakum-nav__dropdown {
    position: absolute !important;
    opacity: 0 !important;
```

Change to:
```css
.lakum-nav__dropdown {
    position: absolute !important;
    opacity: 1 !important;  /* FORCE VISIBLE TEMPORARILY */
```

And change:
```css
    visibility: hidden !important;
```

To:
```css
    visibility: visible !important; /* FORCE VISIBLE TEMPORARILY */
```

**After saving:**
- Hard refresh: Ctrl+Shift+R
- All dropdowns should be VISIBLE all the time
- If they appear now: CSS is OK, JavaScript needs fixing
- If they DON'T appear: CSS or HTML structure is broken

---

## IF FORCE-SHOWING WORKS

**This proves:**
- ✅ CSS is correct
- ✅ HTML structure is correct
- ❌ JavaScript is NOT adding `.active` class

**Then check JavaScript:**

### In Browser Console, Run:
```javascript
// Check if clicks are being detected
document.addEventListener('click', function(e) {
    if (e.target.closest('.lakum-nav__dropdown-toggle')) {
        console.log('🔴 CLICK DETECTED ON DROPDOWN TOGGLE');
        console.log('Target:', e.target);
        console.log('Parent item:', e.target.closest('.lakum-nav__item--dropdown'));
    }
});
```

Then click a dropdown arrow.  
You should see the log message.

---

## IF FORCE-SHOWING DOESN'T WORK

**This means:**
- ❌ CSS is NOT being applied OR
- ❌ HTML structure is wrong OR
- ❌ CSS file not loading

**Then check:**

### 1. CSS File Loading
```javascript
// Check in DevTools Network tab (F12 → Network)
// Refresh page
// Look for: lakum-header-dropdowns.css
// Should have status 200
// Should have ?v=2.4.0 in URL
```

### 2. CSS Specificity Issue
```javascript
// Check if CSS is being overridden
const dropdown = document.querySelector('.lakum-nav__dropdown');
const style = window.getComputedStyle(dropdown);
console.log('opacity:', style.opacity);     // Should be 0 (or 1 if you forced it)
console.log('visibility:', style.visibility); // Should be hidden (or visible if forced)
```

### 3. HTML Structure
```javascript
// Check if elements exist
console.log('Dropdown items:', document.querySelectorAll('.lakum-nav__item--dropdown').length);
console.log('Toggles:', document.querySelectorAll('.lakum-nav__dropdown-toggle').length);
console.log('Dropdowns:', document.querySelectorAll('.lakum-nav__dropdown').length);
```

All should be 9.

---

## STEP-BY-STEP DEBUG

### Scenario A: "Dropdown appears when I force opacity to 1"

**Problem:** JavaScript not adding `.active` class

**Solution:**
1. Check if `handleToggleClick()` is being called
2. Check if `closeAllDropdowns()` is removing active class incorrectly
3. Check if event.stopPropagation() is causing issues
4. Try removing `event.stopPropagation()`

### Scenario B: "Dropdown never appears, even when I force opacity to 1"

**Problem:** CSS file not loading OR HTML structure wrong

**Solution:**
1. Check Network tab for CSS file (must be 200)
2. Verify `?v=2.4.0` version in URL
3. Do hard refresh: Ctrl+Shift+R
4. Check if styles are being overridden by other CSS files
5. Verify HTML has `.lakum-nav__dropdown` elements

### Scenario C: "Some dropdowns work, some don't"

**Problem:** Partial JavaScript execution

**Solution:**
1. Check browser console for JavaScript errors
2. Check if all toggle buttons have event listeners
3. Try: `document.querySelectorAll('.lakum-nav__dropdown-toggle')[0].click()`
4. Check if different dropdowns have different classes

---

## NUCLEAR OPTION: Override Everything

Add this to `<head>` temporarily (last resort):

```html
<style>
    .lakum-nav__dropdown {
        opacity: 1 !important !important !important;
        visibility: visible !important !important !important;
        pointer-events: auto !important !important !important;
    }
</style>
```

If dropdown appears with this: CSS conflicts are the issue.

---

## ACTUAL FIX: Update JavaScript

If the problem is JavaScript not adding `.active`, do this:

In `js/lakum-header-dropdowns.js`, find:
```javascript
function handleToggleClick(event) {
    event.preventDefault();
    event.stopPropagation();
```

Change to:
```javascript
function handleToggleClick(event) {
    event.preventDefault();
    // event.stopPropagation(); // COMMENT OUT TEMPORARILY TO DEBUG
```

Then test if click is detected.

If that fixes it, the issue was `stopPropagation()` preventing event bubbling.

---

## COMPLETE DEBUG SCRIPT

Copy-paste this entire thing into browser console:

```javascript
console.clear();
console.log('=== DROPDOWN DEBUG START ===\n');

// 1. Check elements exist
console.log('1. ELEMENT CHECK:');
const toggles = document.querySelectorAll('.lakum-nav__dropdown-toggle');
const items = document.querySelectorAll('.lakum-nav__item--dropdown');
const dropdowns = document.querySelectorAll('.lakum-nav__dropdown');
console.log('   Toggles found:', toggles.length, toggles.length > 0 ? '✅' : '❌');
console.log('   Items found:', items.length, items.length > 0 ? '✅' : '❌');
console.log('   Dropdowns found:', dropdowns.length, dropdowns.length > 0 ? '✅' : '❌');

// 2. Check CSS is loading
console.log('\n2. CSS CHECK:');
const dropdown = document.querySelector('.lakum-nav__dropdown');
if (dropdown) {
    const style = window.getComputedStyle(dropdown);
    console.log('   Default opacity:', style.opacity);
    console.log('   Default visibility:', style.visibility);
}

// 3. Check event listeners
console.log('\n3. EVENT LISTENER CHECK:');
const firstToggle = toggles[0];
if (firstToggle) {
    console.log('   First toggle element:', firstToggle);
    console.log('   Has onclick?', firstToggle.onclick ? '✅' : '❌');
    
    // Add temporary listener
    firstToggle.addEventListener('click', () => {
        const parentItem = firstToggle.closest('.lakum-nav__item--dropdown');
        console.log('   🖱️ CLICK DETECTED!');
        console.log('   Parent has .active?', parentItem.classList.contains('active'));
    });
}

// 4. Manual test
console.log('\n4. MANUAL TEST:');
console.log('   Try: firstToggle.click()');
console.log('   Or manually: document.querySelector(".lakum-nav__item--dropdown").classList.add("active")');

console.log('\n=== END DEBUG ===');
```

Run this and tell me the output!

---

## WHAT TO REPORT

If dropdown still doesn't work, tell me:

1. **Did you force opacity to 1?**
   - Yes, then dropdown appeared → CSS is OK
   - No, dropdown didn't appear → CSS issue

2. **Does console show element counts?**
   - 9, 9, 9 → HTML is OK
   - 0, 0, 0 → HTML is broken

3. **Do you see debug messages when clicking?**
   - Yes → JavaScript IS running
   - No → JavaScript NOT running

4. **What's the browser?**
   - Chrome, Firefox, Safari, Edge?

5. **Any red errors in console?**
   - What are they?

---

## IMPORTANT

**Do NOT edit the live site yet!**

First:
1. Test on `test-dropdown-simple.html`
2. Run debug scripts in console
3. Tell me what you find
4. We'll fix based on the actual problem

This will identify exactly where the issue is.

---

*Last Updated: June 21, 2026*
