#!/bin/bash

# Script để chạy NGROK cho MAMP
# Usage: ./start_ngrok.sh

echo "🚀 Starting NGROK for MAMP..."
echo ""

# Kiểm tra NGROK đã cài chưa
if ! command -v ngrok &> /dev/null; then
    echo "❌ NGROK chưa được cài đặt!"
    echo "📥 Vui lòng cài đặt NGROK:"
    echo "   brew install ngrok/ngrok/ngrok"
    echo "   hoặc download từ: https://ngrok.com/download"
    exit 1
fi

# Kiểm tra MAMP đang chạy chưa
if ! lsof -Pi :8888 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  MAMP có vẻ chưa chạy trên port 8888"
    echo "💡 Vui lòng start MAMP trước!"
    echo ""
    read -p "Bạn có muốn tiếp tục không? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Port mặc định của MAMP
PORT=8888

# Kiểm tra có custom port không
if [ ! -z "$1" ]; then
    PORT=$1
fi

echo "📡 Starting NGROK tunnel..."
echo "   Local: http://localhost:$PORT"
echo "   Public: https://xxxx.ngrok-free.app"
echo ""
echo "💡 Tips:"
echo "   - Nhấn Ctrl+C để dừng NGROK"
echo "   - Xem dashboard tại: http://localhost:4040"
echo "   - Copy URL từ terminal và share cho người khác"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Chạy NGROK
ngrok http $PORT

