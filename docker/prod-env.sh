#!/bin/sh

if [[ ! -e /app/.env.local ]]; then

echo "
APP_ENV=prod
APP_DEBUG=off
APP_CURRENCY_FREAK_LOOKUP_API_KEY=$1
APP_BIN_TABLE_LOOKUP_API_KEY=$2
APP_BIN_CODES_LOOKUP_API_KEY=$3
" > /app/.env.local

fi