#!/bin/sh

if [[ ! -e /app/.env.test.local ]]; then

echo "
APP_CURRENCY_FREAK_LOOKUP_API_KEY=$1
APP_BIN_TABLE_LOOKUP_API_KEY=$2
" > /app/.env.test.local

fi