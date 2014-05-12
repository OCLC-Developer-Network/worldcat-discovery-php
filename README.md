# Worldcat::Discovery

A PHP library for WorldCat Discovery API. 

## Installation

The easiest way to install the OCLC Auth library is with Composer. Composer is a PHP dependency management tool that let's you declare the dependencies your project requires and installs them for you.

Sample Composer file

```javascript
{
"name" : "MyApp",

	"repositories": [
	{
	"type": "git",
	"url": "https://github.com/OCLC-Developer-Network/worldcat-discovery-php.git"
	}
	],
	"require" : {
	"OCLC/Auth" : ">=1.0"
	}
}
```

#### Step 1: Prepare your project

In a Terminal Window

```bash
$ cd {YOUR-PROJECT-ROOT}
$ pico composer.json
```

Copy the contents of the sample composer file above to the `composer.json` file.

#### Step 2: Use composer to install the dependencies

Download composer and use the `install` command to read the JSON file created in step 1 to install the WSKey library in a vendor direcory

```bash
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

#### Step 3: Include the libraries in your code
To start using this library you need to include the OCLC Auth library in your code. Add the following to the top of your file:
```php
<?php
require_once('vendor/autoload.php');
```

## Usage

### Find a Bibliographic Resource in WorldCat


### Search for Bibliographic Resources in WorldCat