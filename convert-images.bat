@echo off
REM Image Conversion Script for Windows - Generate responsive WebP + AVIF variants
REM Usage: convert-images.bat
REM Requires: FFmpeg installed and in PATH

setlocal enabledelayedexpansion

set SOURCE_DIR=heroImage
set OUTPUT_DIR=heroImage
set WEBP_QUALITY=65
set AVIF_QUALITY=50

echo.
echo ========================================
echo 🖼️  Image Optimization Script
echo ========================================
echo Source: %SOURCE_DIR%
echo Output: %OUTPUT_DIR%
echo.

REM Check if FFmpeg is installed
where ffmpeg >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ✗ FFmpeg not found!
    echo Install from: https://ffmpeg.org/download.html
    echo Or use: choco install ffmpeg
    pause
    exit /b 1
)

REM Process each image
for %%F in (%SOURCE_DIR%\*.png %SOURCE_DIR%\*.jpg %SOURCE_DIR%\*.jpeg) do (
    set "filename=%%~nF"
    set "nameonly=%%~nF"
    for /f "tokens=1* delims=." %%A in ("!nameonly!") do set "nameonly=%%A"
    
    echo.
    echo Converting !nameonly!...
    
    REM Generate 320w
    ffmpeg -i "%%F" -vf "scale=320:-1" -q:v %WEBP_QUALITY% "%OUTPUT_DIR%\!nameonly!-320w.webp" -y >nul 2>&1
    ffmpeg -i "%%F" -vf "scale=320:-1" -c:v libaom-av1 -crf %AVIF_QUALITY% "%OUTPUT_DIR%\!nameonly!-320w.avif" -y >nul 2>&1
    echo ✓ Created !nameonly!-320w
    
    REM Generate 480w
    ffmpeg -i "%%F" -vf "scale=480:-1" -q:v %WEBP_QUALITY% "%OUTPUT_DIR%\!nameonly!-480w.webp" -y >nul 2>&1
    ffmpeg -i "%%F" -vf "scale=480:-1" -c:v libaom-av1 -crf %AVIF_QUALITY% "%OUTPUT_DIR%\!nameonly!-480w.avif" -y >nul 2>&1
    echo ✓ Created !nameonly!-480w
    
    REM Generate 768w
    ffmpeg -i "%%F" -vf "scale=768:-1" -q:v %WEBP_QUALITY% "%OUTPUT_DIR%\!nameonly!-768w.webp" -y >nul 2>&1
    ffmpeg -i "%%F" -vf "scale=768:-1" -c:v libaom-av1 -crf %AVIF_QUALITY% "%OUTPUT_DIR%\!nameonly!-768w.avif" -y >nul 2>&1
    echo ✓ Created !nameonly!-768w
    
    REM Generate 1200w
    ffmpeg -i "%%F" -vf "scale=1200:-1" -q:v %WEBP_QUALITY% "%OUTPUT_DIR%\!nameonly!-1200w.webp" -y >nul 2>&1
    ffmpeg -i "%%F" -vf "scale=1200:-1" -c:v libaom-av1 -crf %AVIF_QUALITY% "%OUTPUT_DIR%\!nameonly!-1200w.avif" -y >nul 2>&1
    echo ✓ Created !nameonly!-1200w
)

echo.
echo ========================================
echo ✓ Image conversion complete!
echo ========================================
echo.
echo Generated files:
dir /b "%OUTPUT_DIR%\*.webp" "%OUTPUT_DIR%\*.avif" 2>nul
echo.
echo Next steps:
echo 1. Verify file sizes (should be ^<150KB for hero, ^<80KB for cards^)
echo 2. Delete original oversized images
echo 3. Deploy to production
echo.
pause
