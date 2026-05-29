#!/bin/bash
set -e

echo "🚀 Deploying AxiaOrto..."

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Install PHP dependencies (production only)
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend
echo "🔨 Building frontend..."
cd frontend && npm ci && npm run build && cd ..

# Laravel optimizations
echo "⚡ Caching config, routes..."
php artisan config:cache
php artisan route:cache

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

echo "✅ Deploy complete!"
