#Obraz końcowy
FROM php:8.2-cli

# Dane autora zgodnie z OCI
LABEL org.opencontainers.image.title="Weather App" \
      org.opencontainers.image.description="Prosta aplikacja pogodowa w PHP" \
      org.opencontainers.image.authors="Svitlana Lysiuk <svitlllanalysiuk@gmail.com>" \
      org.opencontainers.image.version="1.0"

# Utworzenie katalogu aplikacji
WORKDIR /app

# Skopiowanie plików aplikacji
COPY index.php .
COPY data.php .
COPY entrypoint.sh .
COPY style.css .
COPY logs/ ./logs/

# Nadanie praw do skryptu uruchamiającego
RUN chmod +x entrypoint.sh && chmod 777 logs

# Otworzenie portu aplikacji
EXPOSE 8000

# Sprawdzenie zdrowia kontenera
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl --fail http://localhost:8000 || exit 1

# Polecenie startowe
ENTRYPOINT ["./entrypoint.sh"]
