# Simple Dropbox

## What is it?
** A PHP library for integrating Dropbox v2 APIs into your application. 
** https://www.postman.com/dropbox-api/workspace/dropbox-s-public-workspace/overview

1. Installation
2. Token Generation
3. Dropbox Actions Supported
4. Usage


## Installation

PHP versions 7.1 and higher are currently supported.

The PECL mbstring extension is required.

It is recommended to use composer to install the library.

```shell
composer require deskola/simple-dropbox
```

## Token Generation

### Generate Refresh token
1. Go to Dropbox App console and create a new app
2. App ClientId and CLientSecrete will be generated.
3. Use this Stackoverflow solution https://stackoverflow.com/questions/70641660/how-do-you-get-and-use-a-refresh-token-for-the-dropbox-api-python-3-x/71794390#71794390 to generate a token. From the same solution you can proceed and generate a refresh token.
4. Or use the inbuilt fuction to generate the refresh token.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Deskola\SimpleDropbox\Dropbox;

$credentials = array($clientKey, $clientSecrete);
$dropbox = new Dropbox($credentials);
$response = $dropbox->generate_refresh_token($code); //$code is from step 3 above
print_r($response);

```

### Refresh Token
Drobox token are short lived and expire after 4 hours. Using the refresh token above. we can generate new tokens 

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Deskola\SimpleDropbox\Dropbox;

$credentials = array($clientKey, $clientSecrete);
$dropbox = new Dropbox($credentials);
$response = $dropbox->refresh_token($refreshToken); //$credentials is from step 3 above or code above
print_r($response);

```


## Requirements

| actions  | Description  |   |   |   |
|---|---|---|---|---|
| create_folder  | create a folder  |   |   |   |
| list_folders  | list all folder in your application  |   |   |   |
| file_upload  | upload a file into your app  |   |   |   |
| download  | download a file from your app  |   |   |   |
| delete  | delete a file  |   |   |   |
| file_search  | search for a specific file in your dropbox app  |   |   |   |
| move  | move file/folder from one path to another path within your app  |   |   |   |
| copy  | copy file/folder from one path to another path within your app  |   |   |   |
| preview  | preview file  |   |   |   |


## Usage
