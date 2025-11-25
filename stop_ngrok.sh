#!/bin/bash

# Script để dừng NGROK
# Usage: ./stop_ngrok.sh

echo "🛑 Stopping NGROK..."

# Tìm và kill process NGROK
pkill -f ngrok

if [ $? -eq 0 ]; then
    echo "✅ NGROK đã được dừng"
else
    echo "ℹ️  Không tìm thấy process NGROK đang chạy"
fi

