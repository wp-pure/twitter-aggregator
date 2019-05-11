## php-twitter-aggregator

This script uses the [twitter-api-php](https://github.com/J7mbo/twitter-api-php) library to retrieve twitter feeds, aggregate them and cache them. If included in a WordPress theme or plugin, it'll find the WP functions, and the cache folder will be put into the uploads folder. Enough ado, here's a sample integration.

#### Clone it:

```sh
// clone the repo
git clone git@github.com:jpederson/php-twitter-aggregator.git
```

#### Install via Composer

```sh
composer require jamespederson/php-twitter-aggregator
```

*****

#### Get Your Twitter API Keys

Check out the URL below to create a Twitter app and get your keys to use their API.

https://dev.twitter.com/apps/new

*****

#### How to Use the Aggregator

```php
// include the aggregator class
require_once( 'twitterAggregator.php' );

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

// display the widget
$ta->display();
```

Developed by [James Pederson](http://jpederson.com)