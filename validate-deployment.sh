#!/bin/bash

# LAKUM Artspace - Deployment Validation Script
# Checks for common deployment issues before pushing to production

echo "🔍 LAKUM Deployment Validation"
echo "=============================="
echo ""

ERRORS=0
WARNINGS=0

# Check 1: .env file not in Git
echo "✓ Checking if .env is properly ignored..."
if git ls-files | grep -q "\.env"; then
    echo "  ❌ ERROR: .env file is tracked in Git!"
    echo "     Run: git rm --cached .env"
    ((ERRORS++))
else
    echo "  ✅ .env is properly ignored"
fi

# Check 2: Credentials in files
echo ""
echo "✓ Checking for hardcoded credentials..."
if grep -r "?PuzuDXOo" . --exclude-dir=.git --exclude-dir=node_modules 2>/dev/null | grep -v ".md"; then
    echo "  ⚠️  WARNING: Found exposed database password in files!"
    echo "     This should be in .env, not in code"
    ((WARNINGS++))
else
    echo "  ✅ No obvious credentials found"
fi

# Check 3: .gitignore exists
echo ""
echo "✓ Checking .gitignore..."
if [ -f ".gitignore" ]; then
    echo "  ✅ .gitignore exists"
else
    echo "  ❌ ERROR: .gitignore not found"
    ((ERRORS++))
fi

# Check 4: GitHub Actions workflow exists
echo ""
echo "✓ Checking GitHub Actions workflow..."
if [ -f ".github/workflows/deploy-hostinger.yml" ]; then
    echo "  ✅ Deployment workflow exists"
else
    echo "  ⚠️  WARNING: Deployment workflow not found"
    ((WARNINGS++))
fi

# Check 5: deployment-hostinger branch exists
echo ""
echo "✓ Checking deployment-hostinger branch..."
if git rev-parse --verify deployment-hostinger > /dev/null 2>&1; then
    echo "  ✅ deployment-hostinger branch exists"
else
    echo "  ⚠️  WARNING: deployment-hostinger branch not found"
    echo "     Create it with: git checkout -b deployment-hostinger"
    ((WARNINGS++))
fi

# Check 6: index.php exists
echo ""
echo "✓ Checking index.php..."
if [ -f "index.php" ]; then
    echo "  ✅ index.php found in root"
else
    echo "  ❌ ERROR: index.php not found in root"
    ((ERRORS++))
fi

# Check 7: config.php exists
echo ""
echo "✓ Checking config.php..."
if [ -f "config.php" ] || [ -f "api/config.php" ]; then
    echo "  ✅ config.php found"
else
    echo "  ❌ ERROR: config.php not found"
    ((ERRORS++))
fi

# Check 8: .env.example exists
echo ""
echo "✓ Checking .env.example..."
if [ -f ".env.example" ]; then
    echo "  ✅ .env.example exists (reference for credentials)"
else
    echo "  ⚠️  WARNING: .env.example not found"
    ((WARNINGS++))
fi

# Summary
echo ""
echo "=============================="
echo "Validation Summary"
echo "=============================="
echo "Errors:   $ERRORS"
echo "Warnings: $WARNINGS"
echo ""

if [ $ERRORS -eq 0 ]; then
    echo "✅ Ready for deployment!"
    exit 0
else
    echo "❌ Fix errors before deploying"
    exit 1
fi
