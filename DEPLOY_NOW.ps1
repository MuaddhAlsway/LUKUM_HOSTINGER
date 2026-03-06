# Deployment Script for Hostinger
# This script uploads the modified files to your Hostinger server

# Configuration
$FTP_HOST = "ftp.lakumartspace.com"
$FTP_USER = "your_ftp_username"
$FTP_PASS = "your_ftp_password"
$LOCAL_PATH = "C:\xampp\htdocs\LUKUM_HOSTINGER"
$REMOTE_PATH = "/public_html"

# Files to deploy
$FILES_TO_DEPLOY = @(
    "event.php",
    "blog.php",
    "press.php",
    "api/get_blogs_working.php",
    "api/get_press.php",
    "api/check-database-now.php",
    "api/test-apis-simple.php"
)

Write-Host "Starting deployment to Hostinger..." -ForegroundColor Green

# Create FTP connection
$FTP_CRED = New-Object System.Net.NetworkCredential($FTP_USER, $FTP_PASS)

foreach ($file in $FILES_TO_DEPLOY) {
    $localFile = Join-Path $LOCAL_PATH $file
    $remoteFile = "$REMOTE_PATH/$file"
    
    if (Test-Path $localFile) {
        Write-Host "Uploading: $file" -ForegroundColor Yellow
        
        # Create FTP request
        $uri = "ftp://$FTP_HOST$remoteFile"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = $FTP_CRED
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.KeepAlive = $false
        
        # Upload file
        $fileStream = [System.IO.File]::OpenRead($localFile)
        $ftpStream = $ftpRequest.GetRequestStream()
        $fileStream.CopyTo($ftpStream)
        $ftpStream.Close()
        $fileStream.Close()
        
        $response = $ftpRequest.GetResponse()
        Write-Host "✓ Uploaded: $file" -ForegroundColor Green
        $response.Close()
    } else {
        Write-Host "✗ File not found: $localFile" -ForegroundColor Red
    }
}

Write-Host "Deployment complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Verify deployment by visiting:" -ForegroundColor Cyan
Write-Host "https://lakumartspace.com/blog.php?lang=en"
Write-Host "https://lakumartspace.com/press.php?lang=en"
Write-Host "https://lakumartspace.com/event.php?id=1&lang=en"
