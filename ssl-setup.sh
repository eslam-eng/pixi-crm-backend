#!/bin/bash
set -e

DOMAIN="pixicrm.barmagiat.com"
EMAIL="tools@mijra.io"

# Ensure folders exist
mkdir -p ./letsencrypt/www ./letsencrypt/live ./docker/nginx

# Step 1: Start nginx with HTTP only
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d nginx

# Step 2: Request SSL certificate
docker run --rm \
  -v $(pwd)/letsencrypt:/etc/letsencrypt \
  -v $(pwd)/letsencrypt/www:/var/www/certbot \
  certbot/certbot certonly --webroot -w /var/www/certbot \
  -d $DOMAIN --email $EMAIL --agree-tos --no-eff-email

# Step 3: Restart stack with HTTPS enabled
docker compose -f docker-compose.yml -f docker-compose.prod.yml down
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

echo "✅ SSL setup complete!"
echo "Access your project at: https://$DOMAIN:8443"
