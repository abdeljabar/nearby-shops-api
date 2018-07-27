## Hidden Founders web coding challenge - API (Server Side)
A [coding challenge](https://github.com/hiddenfounders/web-coding-challenge) provided by Hidden Founders.

For the client-side part of the project [click here](https://bitbucket.org/taoufikallah88/nearby-shops-web)

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

## Requirements

- PHP 7 or newer
- [Composer](https://getcomposer.org/)
- MySQL

## Quick Project Setup - Installation

##### 1. Clone the repository using the following command in terminal:
    git clone git@bitbucket.org:taoufikallah88/nearby-shops-api.git
    
##### 2. Move to the project root folder
    cd nearby-shops-api
    
##### 3. Install dependencies:
    composer install 

##### 4. Configure the LexikJWTAuthenticationBundle
[Click here for more information](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#installation)
         
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
            "shops_with_location_uri": "/shops/?location=LAT_PLACEHOLD,LONG_PLACEHOLDER",
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
    
User is required to provide their email & password to login

Upon successful login, the response returns a new token:

    {
        "code": 200,
        "message": "JWT_KEY_PLACEHOLDER"
    }
    
##### 3. Registration
    http://localhost:8000/register

Upon successful registration the api returns the following response:

    {
        "success": 1,
        "message": "User was created successfully."
    }
    
    with the http code: 201
    
##### 4. Retrieving shops nearby
On success, the API returns the following response:

    {
        "success": 1,
        "message": "Result found.",
        "result": [
            {
                "name": "",
                "email": "",
                "city": "",
                "picture": "http://placehold.it/150x150",
                "location": {
                    "type": "point",
                    "coordinates": [
                        "",
                        ""
                    ]
                },
                "like_action_uri": "/shops/SHOP_ID?action=like",
                "unlike_action_uri": "/shops/SHOP_ID?action=unlike",
                "dislike_action_uri": "/shops/SHOP_ID?action=dislike"
            }
        ]
    }

##### 5. Adding shop to the list of preferred shops
    http://localhost:8000/shops/SHOP_ID?action=like
    
On success, the API returns the following response:
        
    {
        "success": 1,
        "message": "Shop liked."
    }
    
If shop is already liked then this response is returned:

    {
        "success": 0,
        "message": "Shop already liked."
    }

##### 5. Removing shop from the list of preferred shops
    http://localhost:8000/shops/SHOP_ID?action=unlike
    
On success, the API returns the following response:
    
    {
        "success": 1,
        "message": "Shop unliked."
    }

##### 5. Disliking shop and preventing it from appearing in the nearby shops list
    http://localhost:8000/shops/SHOP_ID?action=dislike
    
On success, the API returns the following response:
    
    {
        "success": 1,
        "message": "Shop disliked."
    }

If shop is already disliked then update its date & time with the same response returned:

    {
        "success": 1,
        "message": "Shop diliked."
    }
    
