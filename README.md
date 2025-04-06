## Onlysearch - Challange

This document serves as a guide for the challange, a Laravel application designed to scrape & search profile data based on a username input.

### Technologies

* PHP 8.4 (Docker image)
* PostgreSQL 17.4 (Docker image)
* Redis (Docker image)
* Laravel Framework

### Prerequisites

* Docker installed and running

### Installation

1. Clone the repository:

```bash
git clone https://github.com/lucasacoutinho/ch-onlysearch.git
```

2. Navigate to the project directory:

```bash
cd ch-onlysearch
```

3. Copy the `.env.example` file to `.env`

```bash
cp .env.example .env
```

4. Build the Docker images and start the containers:

```bash
docker compose up -d
```

### Usage

**API Documentation:**

The API documentation is available at `http://localhost/docs` (assuming the service is running on your local machine). This provides a detailed overview of the available endpoints, request parameters, and response formats.

**Testing:**

To run the tests, use the following command:



```bash
docker compose exec ch-onlysearch-api bash
```

```bash
php artisan test
```

### Additional Notes

* This API utilizes a Docker environment for ease of deployment and consistency.
* The provided Dockerfile configures Nginx as the web server and sets up a cron job to process scheduled transactions daily.
