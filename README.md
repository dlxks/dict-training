# Dict-Training Hangman Game

A Laravel-based web application for dictionary training through an interactive Hangman game. Features include user registration/login (with Google OAuth), lobby, game creation/playing with challenges, and email verification.

## Quick Setup (Docker Deployment)

**Docker Deployment:**

```
docker compose down && docker compose up --build -d
```

Wait 60s for DB init. Access http://localhost:8000

**MySQL Access:** localhost:3307 (override), forge/forge, hangman_db

**Or Local Setup (without Docker):**

1. **Run the setup script**:

    ```
    composer run setup
    ```

    This installs PHP dependencies, copies `.env.example` to `.env`, generates app key, runs migrations, installs JS deps, and builds assets.

2. **Configure Environment (.env)**:
    - Set `APP_URL=http://localhost:8000` (or your domain).
    - Database config:
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3307  # If using Docker
        DB_DATABASE=hangman_db
        DB_USERNAME=root
        DB_PASSWORD=root
        ```
    - Mail config (for email verification): Use 'log' driver initially (`MAIL_MAILER=log`) or configure SMTP.

3. **Optional: Start MySQL with Docker**:

    ```
    docker compose up -d
    ```

    Starts MySQL container on port 3307.

4. **Google OAuth Setup**:
    - Go to [Google Cloud Console](https://console.cloud.google.com/apis/credentials).
    - Create OAuth 2.0 Client ID (Web application).
    - Authorized redirect URIs: `http://localhost:8000/auth/google/callback` (adjust for APP_URL).
    - Add to `.env`:
        ```
        GOOGLE_CLIENT_ID=your_client_id
        GOOGLE_CLIENT_SECRET=your_client_secret
        GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
        ```

5. **Start the development server**:
    ```
    php artisan serve
    ```
    Visit `http://localhost:8000`.

## Email Verification

New registrations/sign-ins require email verification.

- Verification link is sent via mail (check `storage/logs/laravel.log` if using `MAIL_MAILER=log`).
- Resend link available on `/email/verify/resend`.

## Playing the Game

- Register/Login (email or Google).
- Visit lobby `/lobby`.
- Create or join games `/games`.
- Play Hangman with dictionary challenges!

## Development

- Run dev stack: `composer run dev` (server + queue + vite).
- Tests: `composer run test`.
- Queue: `php artisan queue:work`.

## License

MIT License.
