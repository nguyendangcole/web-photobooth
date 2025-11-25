#!/bin/bash

# Script để chạy NGROK cho MAMP
# Usage: ./start_ngrok.sh [port]

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

# Port mặc định của MAMP
PORT=80

# Kiểm tra có custom port không
if [ ! -z "$1" ]; then
    PORT=$1
fi

# Kiểm tra MAMP đang chạy chưa
if ! lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  MAMP có vẻ chưa chạy trên port $PORT"
    echo "💡 Vui lòng start MAMP trước!"
    echo ""
    read -p "Bạn có muốn tiếp tục không? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo "📡 Starting NGROK tunnel..."
echo "   Local: http://localhost:$PORT"
echo ""
echo "💡 Sau khi NGROK khởi động, chạy lệnh sau để lấy URL đầy đủ:"
echo "   ./get_ngrok_url.sh"
echo ""
echo "💡 Hoặc xem tại: http://localhost:4040"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Chạy NGROK ở background và lấy URL sau 3 giây
ngrok http $PORT > /tmp/ngrok.log 2>&1 &
NGROK_PID=$!

# Đợi NGROK khởi động
sleep 3

# Lấy URL đầy đủ
if [ -f "./get_ngrok_url.sh" ]; then
    ./get_ngrok_url.sh
fi

echo ""
echo "📝 NGROK đang chạy (PID: $NGROK_PID)"
echo "🛑 Nhấn Ctrl+C để dừng NGROK"
echo ""

# Đợi NGROK chạy
wait $NGROK_PID

