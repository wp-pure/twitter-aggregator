<?php

require_once( 'vendor/autoload.php' );
require_once( 'twitterAggregator.php' );

// generate an aggregator object
$ta = new twitterAggregator( array(

    // twitter API consumer key, secret, and oath token and oauth secret
    'consumer_key' => "UFBxe5cHwmGbDxHf3H9jDAGar",
    'consumer_secret' => "HSozmjgxMvNa74D8Sz5RL6Nav56uK0LKLvIvUu6FAgjNH7uClt",
    'oauth_access_token' => "29196496-q1Wllv60i94w1Wlpt6Ztzimfu5IvQOxOcxt8uwEN1",
    'oauth_access_token_secret' => "SziLDM5qOVAqGrPMvqTKEEWQ7Z4qgmA66aLJh1uOeOfVT",

    // comma separated list of twitter handles to pull
    'usernames' => "reuters,ap,propublica",

    // set the number of tweets to show
    'count' => 20,

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
