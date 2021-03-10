# BEEZNESSLY
![Beeznessly](https://i.postimg.cc/VNbWDnn2/Capture-d-e-cran-2021-01-28-a-11-41-17.png)

_Tables of content_
1. About The Project
  - Built With
2. Getting Started
  - Prerequisites
  - Install
3. Usage
4. Acknowledgements
5. Authors

# About The Project

This project is in partnership with the company Beeznessly. Beeznessly has for goal to connect entrepreneurs with digital experts and facilitate the download of ebooks.

## Built With

* [Symfony](https://github.com/symfony/symfony)
* [Bootstrap](https://getbootstrap.com/)
* [JQuery](https://jquery.com/)
* [GrumPHP](https://github.com/phpro/grumphp)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* [PHPStan](https://github.com/phpstan/phpstan)
* [PHPMD](https://phpmd.org/)
* [ESLint](https://eslint.org/)
* [Sass-Lint](https://github.com/sasstools/sass-lint)
* [Travis CI](https://github.com/marketplace/travis-ci)
* [Twig](https://github.com/twigphp/Twig)
* [MySQL](https://www.mysql.com/fr/)

# Getting Started

This is an example of how you may give instructions on setting up your project locally. To get a local copy up and running follow these simple example steps.

## Prerequisites

1. Check composer is installed
2. Check yarn & node are installed

## Install

1. Clone this project
2. Run >composer install
3. Run >yarn install
4. Run >yarn encore dev< to build assets
5. Connect the database in the env.local
6. Run >php bin/console d:d:c
7. Run >php bin/console make:migration
8. Run >bin/console doctrine:migrations:migrate
9. You can load fixtures if needed

# Usage

  As a expert you can:
1. Register
2. Login
3. Ask for a new password
4. Upload a logo , profil picture and banner for their expert page
5. Create, Edit and Upload their ebooks
6. See who downloads their ebooks and how much
7. See who send a message

  As a entrepreneur you can:
1. Register
2. Login
3. Ask for a new password
4. Upload a profil picture
5. Download ebooks
6. See their ebooks download
7. Send message to expert

  As an admin you can (easyadmin):
1. Validate an account expert
2. Validate an ebook
3. Create, modify and delete expertise
4. Create, modify and delete type of user
5. See their message

# Acknowledgements

*[Font Awesome](https://fontawesome.com/)

# Authors

Wild Code School students (Juliette, Océane, Paul, Marie-Camille)