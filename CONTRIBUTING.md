# Contributing

## Docker

To get started, you will need to have Docker installed on your machine. 
If you do not have Docker installed, you can download it [here](https://www.docker.com/products/docker-desktop).

When you build the Docker container, it will install all the necessary dependencies and container will be ready to handle connection.
```bash
docker compose build
docker compose up
```
Then you can launch the following command to run the tests:
```bash
docker compose exec geo-parser-app vendor/bin/phpunit
```

## PHP Linters: Php-CS-Fixer, PHP-Mess-detector PHP-Stan 

This project uses PHP-CS-Fixer, PHP-Mess-detector and PHP-Stan to ensure code quality.
These tools are set up in the `quality` directory.
Before committing,
you should run some commands to ensure that your code is properly formatted and free of errors.
Read the [readme.md file](./quality/readme.md) for more information.

## Development
- Code formatting MUST follow PSR-2.
- Issues SHOULD include code and/or data to reproduce the issue.
- PR's for issues SHOULD include test(s) for issue.
- PR's SHOULD have adequate documentation (commit messages, comments, etc.) to readily convey what and/or why.  
- Code SHOULD attempt to follow [Object Calisthenics](http://www.xpteam.com/jeff/writings/objectcalisthenics.rtf) methodology.
