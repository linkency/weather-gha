#!/bin/sh
echo "[`date`] Autor: Svitlana Lysiuk | Port: 8000" >> /app/logs/startup.log
php -S 0.0.0.0:8000 -t /app
