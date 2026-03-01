# Hostinger FTP Deployment Script
# This script uploads the legal pages files to Hostinger

# FTP Configuration
$ftpHost = "ftp.lakumartspace.com"  # Replace with your FTP host
$ftpUser = "u812122863"              # Replace with your FTP username
$ftpPass = "Nema202610!LakumDB"      # Replace with your FTP password
$ftpPort = 21

# Files to deploy
$filesToDeploy = @(
    @{ local = "terms.php"; remote = "/public_html/terms.php" },
    @{ local = "privacy.php"; remote = "/public_html/privacy.php" },
    @{ local = "api/get_legal_page.php"; remote = "/public_html/api/get_legal_page.php" },
    @{ local = "api/save_legal_page.php"; remote = "/public_html/api/save_legal_page.php" },
    @{ local = "admin/add-press.html"; remote = "/public_html/admin/add-press.html" },
    @{ local = "admin/add-pricing.html"; remote = "/public_html/admin/add-pricing.html" }
)

Write-Host "╔════════════════════════════════════════════════════════════════╗"
Write-Host "║         Hostinger FTP Deployment - Legal Pages                ║"
Write-Host "╚════════════════════════════════════════════════════════════════╝"
Write-Host ""

# Create FTP credentials
$securePassword = ConvertTo-SecureString $ftpPass -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($ftpUser, $securePassword)

$deployedCount = 0
$failedCount = 0

foreach ($file in $filesToDeploy) {
    $localPath = $file.local
    $remotePath = $file.remote
    
    Write-Host "Uploading: $localPath → $remotePath" -ForegroundColor Cyan
    
    try {
        # Create FTP URI
        $ftpUri = "ftp://$ftpHost$remotePath"
        
        # Read file content
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        
        # Create FTP request
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = $credential
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.KeepAlive = $false
        
        # Upload file
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        # Get response
        $response = $ftpRequest.GetResponse()
        $response.Close()
        
        Write-Host "✅ Success" -ForegroundColor Green
        $deployedCount++
    }
    catch {
        Write-Host "❌ Failed: $_" -ForegroundColor Red
        $failedCount++
    }
    
    Write-Host ""
}

Write-Host "╔════════════════════════════════════════════════════════════════╗"
Write-Host "║                    Deployment Summary                         ║"
Write-Host "╠════════════════════════════════════════════════════════════════╣"
Write-Host "║ Deployed: $deployedCount files" -ForegroundColor Green
Write-Host "║ Failed:   $failedCount files" -ForegroundColor $(if ($failedCount -gt 0) { "Red" } else { "Green" })
Write-Host "╚════════════════════════════════════════════════════════════════╝"

if ($failedCount -eq 0) {
    Write-Host ""
    Write-Host "✅ All files deployed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:"
    Write-Host "1. Visit: https://lakumartspace.com/terms.php?lang=ar"
    Write-Host "2. Verify Arabic content loads"
    Write-Host "3. Check browser console (F12) for success messages"
}
else {
    Write-Host ""
    Write-Host "❌ Some files failed to deploy" -ForegroundColor Red
    Write-Host "Please check the errors above and retry"
}
