# Secret Server API

## Introduction

This is a demo API project that allows to store your secret message in database. The secrets can be expired after a
period of time, depends on the creation, and it's only available for a specified views. After the creation the secrets
available via a random generated hash. You can call a specific endpoint with hash, and you get the original secret.

## Usage

Check the documentation link and you will every information that you
need. [https://api.secret-server.ribar.hu/](https://api.secret-server.ribar.hu/)

## Installation steps

### Dev requirements

+ PHP 8.2 or newer
+ Required PHP extensions: xml, dom, mbstring, intl, pdo, mysql
+ Composer 2.8.3 or newer
+ Smyfony CLI 5.10.5 or newer

To install the project, follow these steps:

1. **Clone the Repository:**

   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. **Install Dependencies:**

   Use the composer to install the necessary dependencies.:

   ```bash
   composer install
   ```

3. **Configure Environment Variables:**

   Copy `.env.example` to `.env` and fill in the required values.


4. **Run the Application:**

   Start the application using the next command.

   ```bash
   symfony server:start
   ```

5. **Access the Application:**

   Open your browser and navigate to `http://localhost:8000` to access the application.