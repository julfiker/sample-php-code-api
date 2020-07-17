## How to configure Spoly API Backend

### Prerequisites

##### PHP
- PHP >= 5.5.9
- OpenSSL PHP Extension
- PDO PHP Extension
- GD PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- Curl PHP Extension
- Install composer

##### Apache
- mod-rewrite extension

##### MySQL
- MySQL >= 5.6 (for full text search support)

### How to run the application
- Clone the git repository and checkout the target branch. 'develop' branch is the active development branch.
- Go to the application folder and run 'composer install'
- Create a virtual host configuration for 'public' folder.
- Give write permission to 'storage' folder including all sub-directories.
- Create a MySQL database for spoly-api.
- Copy the env.example file as .env file. Update the required configuration values (Keys, DB, Mail etc.)
- Run 'php artisan migrate' to run all migrations
- Run 'php artisan db:seed' to populate database with some default data.

### How to test the application
- Run your application first to make sure everything is configured properly.
- Update the phpunit.xml file to use test db, mail etc. instances
- Run 'phpunit' to run all test cases.
- Run 'phpunit --filter YourTestCaseClassName' to run tests in a single test class.