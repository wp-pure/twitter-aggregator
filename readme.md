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

#### In `functions.php`:

```php
// this path can be updated to store the aggregator in any directory of your theme.
require_once( 'twitter-aggregator/widget.php' );
```

*****

#### Get Your Twitter API Keys

Check out the URL below to create a Twitter app and get your keys to use their API.

https://dev.twitter.com/apps/new

*****

#### In your theme template file:

```php
// add your own key and oauth settings into this array
$aggregator_settings = array(
    'consumer_key' => "[CONSUMER KEY]",
    'consumer_secret' => "[CONSUMER SECRET]",
    'oauth_access_token' => "[OAUTH ACCESS TOKEN]",
    'oauth_access_token_secret' => "[OAUTH ACCESS TOKEN SECRET]",
    'usernames' => "[TWITTER USERNAMES]", // comma separated list of twitter handles to fetch
    'limit' => 20, // the number of tweets you'd like to display
    'update_interval' => 20, // the minimum number of minutes to wait before refreshing the caches of each username.
    'cache_dir' => 'cache' // the cache directory name/path
);

// output the actual widget
twitter_aggregator_widget( $aggregator_settings );
```

Developed by [James Pederson](http://jpederson.com)