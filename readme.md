## php-twitter-aggregator

This script uses the [twitter-api-php](https://github.com/J7mbo/twitter-api-php) library to retrieve twitter feeds, aggregate them and cache them. If included in a WordPress theme or plugin, it'll find the WP functions, and the cache folder will be put into the uploads folder. Enough ado, here's a sample integration.

![Github](https://img.shields.io/github/release/jpederson/php-twitter-aggregator.svg) ![MIT](https://img.shields.io/github/license/jpederson/php-twitter-aggregator.svg)

*****

### Install via Composer

```sh
composer require jpederson/twitter-aggregator
```

Include composer's autoloader:

```php
require_once 'vendor/autoload.php';
```

*****

### Obtain Twitter API Keys

Check out the URL below to create a Twitter app and get your keys to use their API.

https://dev.twitter.com/apps/new

*****

### Code Examples

#### Instantiate

```php
// generate an aggregator object
$ta = new twitterAggregator( array(

	// twitter API consumer key, secret, and oath token and oauth secret
    'consumer_key' => "[CONSUMER KEY]",
    'consumer_secret' => "[CONSUMER SECRET]",
    'oauth_access_token' => "[OAUTH ACCESS TOKEN]",
    'oauth_access_token_secret' => "[OAUTH ACCESS TOKEN SECRET]",

    // comma separated list of twitter handles to pull
    'usernames' => "[TWITTER USERNAMES]",

    // set the number of tweets to show
    'count' => 10,

	// set an update interval (minutes)
    'update_interval' => 10,

    // set the cache directory name/path
    'cache_dir' => 'cache',

    // boolean, exclude replies, default true
    'exclude_replies' => true,

    // boolean, include retweets, default true
    'include_rts' => true

) );
```

#### Methods:

There are multiple ways to use the aggregator once it's instantiated. You can display it, with or without styles, or retrieve the widget code.

```php
// display the widget
$ta->display();

// display the widget without any styles
$ta->display_unstyled();

// fetch the widget code
$code = $ta->widget();

// fetch data
$data = $ta->fetch();
```

If you'd like to directly access the api response data (not limited by the `count` option), use the `$ta->data` property after the aggregator object is instantiated.

*****

Developed by [James Pederson](https://jpederson.com).