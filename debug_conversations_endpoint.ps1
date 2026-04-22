$headers = @{
    "X-API-Key" = "vg_28b507a7516d22bff49761cfb70b5309ab2d58acf9fee33d"
    "X-Company-Id" = "1"
}

try {
    Write-Host "Testing /api/whatsapp/conversations with detailed error..."
    $response = Invoke-RestMethod -Uri "http://localhost:3001/api/whatsapp/conversations" -Headers $headers -Method Get
    Write-Host "Conversations Response: $($response | ConvertTo-Json -Depth 10)"
} catch {
    Write-Host "Conversations Error: $($_.Exception.Message)"
    Write-Host "Status Code: $($_.Exception.Response.StatusCode)"
    if($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $reader.DiscardBufferedData()
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body: $responseBody"
    }
}

# Let's also test other endpoints to see what's available
try {
    Write-Host "`nTesting /api/whatsapp endpoint..."
    $response = Invoke-RestMethod -Uri "http://localhost:3001/api/whatsapp" -Headers $headers -Method Get
    Write-Host "WhatsApp Root Response: $($response | ConvertTo-Json -Depth 10)"
} catch {
    Write-Host "WhatsApp Root Error: $($_.Exception.Message)"
}