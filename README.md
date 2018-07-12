# artificial-blog
[![Build Status](https://semaphoreci.com/api/v1/nospoon/artificial-blog/branches/master/badge.svg)](https://semaphoreci.com/nospoon/artificial-blog)

## How to
1) Run `composer install`
2) Initialize the virtual machine by running `vagrant up`
3) ssh into the VM and run the following commands:
  - `php artisan key:generate`
  - `php artisan migrate`
  - `php artisan passport:install`
4) Go into the [application front-end](http://192.168.10.10) and create a user account
5) After logging in, create a new Personal Access Token on the 'Access Tokens' tab
6) You should now be able to authenticate using the newly created token by using

If you are using Insomnia REST client, you can import the 'insomnia_workspace.json' configuration file attached in the project root.
