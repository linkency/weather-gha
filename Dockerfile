# Tworzę obraz bazowy do budowania aplikacji
FROM php:8.1-cli AS builder

# Tworzę katalog roboczy w kontenerze
WORKDIR /app

# Kopiuję wszystkie pliki do kontenera
COPY . .

# Zrobiłam plik entrypoint.sh wykonywalnym
RUN chmod +x entrypoint.sh


# Tworzę końcowy obraz aplikacji
FROM php:8.1-cli

# Dodaję informację o autorze
LABEL org.opencontainers.image.authors="Svitlana Lysiuk"

# Tworzę katalog roboczy
WORKDIR /app

# Kopiuję zbudowaną aplikację z etapu builder
COPY --from=builder /app /app

# Sprawdzam, czy aplikacja działa na porcie 8000
HEALTHCHECK --interval=30s --timeout=3s CMD curl -f http://localhost:8000 || exit 1

# Ustawiam skrypt startowy aplikacji
ENTRYPOINT ["./entrypoint.sh"]

# Uruchamiam wbudowany serwer PHP na porcie 8000
CMD ["php", "-S", "0.0.0.0:8000", "index.php"]

