#!/bin/bash

# Script để lấy URL NGROK đầy đủ
# Usage: ./get_ngrok_url.sh

# Đợi một chút để NGROK khởi động
sleep 2

# Lấy URL từ NGROK API
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels 2>/dev/null | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

if [ ! -z "$NGROK_URL" ]; then
    # Tạo URL đầy đủ với path
    FULL_URL="${NGROK_URL}/web-photobooth/public/?p=landing"
    
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "✅ URL công khai (đầy đủ):"
    echo ""
    echo "   $FULL_URL"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    
    # Copy vào clipboard nếu có thể (macOS)
    if command -v pbcopy &> /dev/null; then
        echo "$FULL_URL" | pbcopy
        echo "✅ Đã copy URL vào clipboard!"
        echo ""
    fi
    
    # Return URL để có thể dùng trong script khác
    echo "$FULL_URL"
else
    echo "⚠️  NGROK chưa chạy hoặc chưa sẵn sàng"
    echo "💡 Vui lòng chạy ./start_ngrok.sh trước"
    exit 1
fi

