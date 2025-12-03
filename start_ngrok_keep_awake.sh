#!/bin/bash

# Script để chạy NGROK và giữ MacBook không sleep
# Usage: ./start_ngrok_keep_awake.sh [port]

echo "🚀 Starting NGROK with Keep-Awake mode..."
echo ""

# Kiểm tra NGROK đã cài chưa
if ! command -v ngrok &> /dev/null; then
    echo "❌ NGROK chưa được cài đặt!"
    echo "📥 Vui lòng cài đặt NGROK:"
    echo "   brew install ngrok/ngrok/ngrok"
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
    exit 1
fi

echo "📡 Starting NGROK tunnel..."
echo "   Local: http://localhost:$PORT"
echo ""
echo "💡 MacBook sẽ KHÔNG sleep khi script này đang chạy"
echo "💡 Nhấn Ctrl+C để dừng NGROK và cho phép MacBook sleep lại"
echo ""

# Lưu PID của caffeinate để có thể kill sau
CAFFEINATE_PID=""

# Function để cleanup khi exit
cleanup() {
    echo ""
    echo "🛑 Stopping NGROK and allowing MacBook to sleep..."
    
    # Kill ngrok nếu đang chạy
    if [ ! -z "$NGROK_PID" ]; then
        kill $NGROK_PID 2>/dev/null
    fi
    
    # Kill caffeinate nếu đang chạy
    if [ ! -z "$CAFFEINATE_PID" ]; then
        kill $CAFFEINATE_PID 2>/dev/null
    fi
    
    # Kill tất cả ngrok processes
    pkill -f "ngrok http" 2>/dev/null
    
    echo "✅ Đã dừng NGROK. MacBook có thể sleep bình thường."
    exit 0
}

# Trap Ctrl+C để cleanup
trap cleanup SIGINT SIGTERM

# Bắt đầu caffeinate để giữ máy không sleep
# -d: prevent display sleep
# -i: prevent idle sleep
# -m: prevent disk sleep
echo "🔋 Enabling keep-awake mode..."
caffeinate -d -i -m -w $$ &
CAFFEINATE_PID=$!

# Chạy NGROK ở background
ngrok http $PORT > /tmp/ngrok.log 2>&1 &
NGROK_PID=$!

# Đợi NGROK khởi động
sleep 3

# Lấy URL đầy đủ
if [ -f "./get_ngrok_url.sh" ]; then
    ./get_ngrok_url.sh
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ NGROK đang chạy (PID: $NGROK_PID)"
echo "✅ Keep-awake mode: ON (MacBook sẽ không sleep)"
echo ""
echo "📝 Xem logs tại: http://localhost:4040"
echo "🛑 Nhấn Ctrl+C để dừng NGROK và cho phép MacBook sleep"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Đợi NGROK chạy (hoặc đến khi bị interrupt)
wait $NGROK_PID

# Cleanup khi ngrok tự dừng
cleanup

