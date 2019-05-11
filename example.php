<?php

require( 'twitter-aggregator.php' );

?>
<link href="example.css" rel="stylesheet" />
<?php
// add your own key and oauth settings into this array
$aggregator_settings = array(
    'consumer_key' => "UFBxe5cHwmGbDxHf3H9jDAGar",
    'consumer_secret' => "HSozmjgxMvNa74D8Sz5RL6Nav56uK0LKLvIvUu6FAgjNH7uClt",
    'oauth_access_token' => "29196496-q1Wllv60i94w1Wlpt6Ztzimfu5IvQOxOcxt8uwEN1",
    'oauth_access_token_secret' => "SziLDM5qOVAqGrPMvqTKEEWQ7Z4qgmA66aLJh1uOeOfVT",
    'usernames' => "jamespederson", // comma separated list of twitter handles to fetch
    'limit' => 20, // the number of tweets you'd like to display
    'update_interval' => 20, // the timeout
    'cache_dir' => 'cache'
);

// output the actual widget
twitter_aggregator_widget( $aggregator_settings );

