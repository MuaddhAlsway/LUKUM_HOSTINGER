# Direct Database Test

## Test 1: Check if exhibitions table has data

Visit: `http://localhost/api/test_slug_match.php`

This will show:
1. All exhibitions in database
2. Which ones match "ampa" using PHP strpos
3. Which ones match "ampa" using SQL LOWER() LIKE

If database_like_matches is EMPTY but PHP matches find something, that means your MySQL version doesn't support LOWER() function or there's a configuration issue.

---

## Expected Output

If AMPM exists:
```json
{
  "all_exhibitions": [
    { "id": 5, "title_en": "AMPM" }
  ],
  "database_like_matches": [
    { "id": 5, "title_en": "AMPM" }
  ]
}
```

---

## Fallback Solution (if LOWER() doesn't work)

If the database test shows that LOWER() matching isn't working, we'll need to use a different approach:

**Option 1:** Normalize titles in PHP (convert to lowercase before comparison)
**Option 2:** Store normalized versions of titles
**Option 3:** Use COLLATE utf8_general_ci instead of LOWER()

The fix will adjust based on your database configuration.

---

## Immediate Action

1. Open: `http://localhost/api/test_slug_match.php`
2. Check the results
3. Share the response with diagnosis of what's working/not working
4. If database_like_matches is empty but php_strpos_matches has results, that's the issue
5. If both are empty, exhibitions table is empty (need to add data)

