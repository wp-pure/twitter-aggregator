## wp-twitter-aggregator

This script uses the [twitter-api-php](https://github.com/J7mbo/twitter-api-php) library to retrieve twitter feeds, aggregate them and cache them in the uploads folder of WordPress. It's designed to be included in your custom theme(s) so you can avoid using an additional plugin to handle this for you. It has caching so you only hit the twitter API every 10 minutes (or so - you can change the cache timeout). Enough ado, here's a sample integration.

### In `functions.php`

```php
// this path can be updated to store the aggregator in any directory of your theme.
require_once( 'twitter-aggregator/widget.php' );
```

### In your theme template file
```php
// add your own key and oauth settings into this array
$aggregator_settings = array(
    'consumer_key' => "UFBxe5cHwmGbDxHf3H9jDAGar",
    'consumer_secret' => "HSozmjgxMvNa74D8Sz5RL6Nav56uK0LKLvIvUu6FAgjNH7uClt",
    'oauth_access_token' => "29196496-q1Wllv60i94w1Wlpt6Ztzimfu5IvQOxOcxt8uwEN1",
    'oauth_access_token_secret' => "SziLDM5qOVAqGrPMvqTKEEWQ7Z4qgmA66aLJh1uOeOfVT",
    'usernames' => "jpederson",
    'limit' => "10"
);

twitter_aggregator_widget( $aggregator_settings );
```

Developed by [James Pederson](http://jpederson.com)