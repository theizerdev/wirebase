$headers = @{
    "X-API-Key" = "test"
    "X-Company-Id" = "1"
}

try {
    Write-Host "Testing /api/whatsapp/status..."
    $response = Invoke-RestMethod -Uri "http://localhost:3001/api/whatsapp/status" -Headers $headers -Method Get
    Write-Host "Status Response: $($response | ConvertTo-Json -Depth 10)"
} catch {
    Write-Host "Status Error: $($_.Exception.Message)"
}

try {
    Write-Host "`nTesting /api/whatsapp/conversations..."
    $response = Invoke-RestMethod -Uri "http://localhost:3001/api/whatsapp/conversations" -Headers $headers -Method Get
    Write-Host "Conversations Response: $($response | ConvertTo-Json -Depth 10)"
} catch {
    Write-Host "Conversations Error: $($_.Exception.Message)"
}