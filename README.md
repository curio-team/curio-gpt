# 🤖 Curio GPT

Curio GPT is a configurable AI assistant built on top of Laravel, designed to be easily extended and customized to fit your education needs.

## Getting Started

To get started with Curio GPT, follow these steps:

1. Clone the repository:

    ```bash
    git clone https://github.com/curio-team/curio-gpt
    ```

2. Navigate to the project directory:

    ```bash
    cd curio-gpt
    ```

3. Install dependencies:

    ```bash
    npm install
    composer install
    ```

4. Set up your environment variables:

    ```bash
    cp .env.example .env
    ```

5. Generate an application key:

    ```bash
    php artisan key:generate
    ```

6. Create the database and run migrations:

    ```bash
    touch database/database.sqlite
    php artisan migrate
    ```

7. Run the development server:

    ```bash
    composer run dev
    ```

Access the application at `http://localhost:8000` and start configuring your AI assistant!
