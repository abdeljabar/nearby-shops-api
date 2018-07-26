## Hidden Founders web coding challenge - API (Server Side)
A [coding challenge](https://github.com/hiddenfounders/web-coding-challenge) provided by Hidden Founders.

## Note 
For the client-side part of the project, click here [here](https://bitbucket.org/taoufikallah88/nearby-shops-web)

## Overview
The coding challenge is a web project whose objective is listing shops nearby

* Signing up using email and password
* Signing in using email and password
* Displaying list of nearby shops
* Adding/liking a shop to the list of preferred shops
* Disliking a shop and hiding it from the list of nearby shops for 2 hours
* Removing/unliking a shop from the list of preferred shops

## Technologies used

- [Symfony 4](https://symfony.com/4)
- [Doctrine](https://github.com/doctrine/doctrine2)
- [Doctrine Extensions](https://github.com/beberlei/DoctrineExtensions)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)

## Requirement

- PHP 7 or newer
- [Composer](https://getcomposer.org/)
- MySQL

## Quick Project Setup - Installation

##### 1. Clone the repository using the following command in terminal:
    git clone git@bitbucket.org:taoufikallah88/nearby-shops-api.git
    
##### 2. Move to project root folder
    cd nearby-shops-api
    
##### 3. Install dependencies:
    composer install 
         
##### 5. Start the php server
    bin/console server:run
    
## Documentation

##### 1. Accessing the API homepage
    http://localhost:8000
    
This page should return a json resource response:
    
    {
        "success": 1,
        "message": "API URLs.",
        "result": {
            "shops_uri": "/shops/",
            "shops_with_location_uri": "/shops/?location=0,0",
            "preferred_shops_uri": "/shops/?liked=true"
        }
    }
    
If the user is not logged in, the response will be:
    
    {
        "code": 401,
        "message": "JWT Token not found"
    }
    
##### 2. Signing in
    http://localhost:8000/login_check
    
User has to provide his email & password to login

Upon successful login
    
    