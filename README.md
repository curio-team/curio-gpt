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

    Be sure to fill at least:
    - `OPENAI_API_KEY` with your OpenAI API key.
    - `SD_CLIENT_ID` and `SD_CLIENT_SECRET` with a valid SD Client App information.

5. Generate an application key:

    ```bash
    php artisan key:generate
    ```

6. Link the storage directory:

    ```bash
    php artisan storage:link
    ```

7. Run migrations:

    ```bash
    php artisan migrate
    ```

8. Run the development server:

    ```bash
    composer run dev
    ```

Access the application at `http://127.0.0.1:8000` and start configuring your AI assistant!
