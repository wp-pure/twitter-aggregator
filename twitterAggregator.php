<?php


/************************************
  twitter aggregator
 ***********************************/

class twitterAggregator {

    // our options array
    private $option = array(

        // twitter API consumer key, secret, and oath token and oauth secret
        'consumer_key' => "",
        'consumer_secret' => "",
        'oauth_access_token' => "",
        'oauth_access_token_secret' => "",

        // comma separated list of twitter handles to pull
        'usernames' => "",

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

    );

    // debug mode will display errors and stop script processing
    public $debug = false;

    // this property will hold the error array if one occurs
    public $error = false;

    // data property to contain entire fetched timelines
    public $data = array();

    // data array trimmed down to the requested count
    public $data_trimmed = array();



    // the main constructor function, loads settings if included
    public function __construct( $options ) {

        // if settings were passed to the constructor
        if ( !empty( $options ) ) {

            // merge the options passed to the constructor, into the object options property
            $this->option = array_merge( $this->option, $options );
        }

        // check if we're in WordPress
        if ( function_exists( 'wp_upload_dir' ) ) {
            
            // get upload directory info
            $upload_info = wp_upload_dir();

            // set up some variables to store cache urls
            $this->option['cache_dir'] = $upload_info['basedir'] . '/cache';
        }
    }



    // get twitter timelines and return them
    public function fetch() {

        // if we've already got the data stored in a property (the request has already taken place)
        if ( !empty( $this->data ) ) {

            // return it
            return $this->data;

        } else {

            // put together the cache file url for this set of usernames
            $cache_file = $this->option['cache_dir'] . '/twitter-' . md5( $this->option['usernames'] ) . '.txt';

            // if cache folder doesn't exist
            if ( !file_exists( $this->option['cache_dir'] ) ) {

                // make the cache directory
                if ( !mkdir( $this->option['cache_dir'], '777', 1 ) ) {

                    // register an error array if there's trouble making the directory
                    $this->error( 'Failed to create cache directory.' );
                }
            }

            // check if we have a cache file
            if ( file_exists( $cache_file ) ) {

                // refresh the cache if the cache file is older than our update_interval value ago
                if ( filemtime( $cache_file ) < ( time() - ( $this->option['update_interval'] * 60 ) ) ) {
                    $cached = false;
                } else {
                    $cached = true;
                }
            } else {
                $cached = false;
            }
            

            // if we don't have the timeline cached, grab it again.
            if ( $cached === false ) {

                // split apart usernames
                $usernames = explode( ",", $this->option['usernames'] );

                // empty array to place results into
                $response_final = array();

                // loop through usernames
                if ( !empty( $usernames ) ) {
                    foreach ( $usernames as $username ) {

                        // pull some statuses
                        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

                        // parameters to pass to twitter api
                        $query_args = array(

                            // the twitter handle we're looking up
                            'screen_name' => trim( $username ),

                            // don't include replies.
                            'exclude_replies' => $this->option['exclude_replies'],

                            // don't include replies.
                            'include_rts' => $this->option['include_rts'],

                            // request 5 times the number of records we need, since the api counts tweets before excluding replies and retrweets (if those are set)
                            'count' => ( $this->option['count'] * 5 )

                        );

                        // build the query string
                        $query = '?' . http_build_query( $query_args );

                        // use the get method
                        $method = 'GET';

                        // open up a twitter API object for us
                        $twitter = new TwitterAPIExchange( $this->option );

                        // execute response
                        $response = $twitter->setGetfield( $query )->buildOauth( $url, $method )->performRequest();

                        // convert the response from json to an array
                        $response_array = json_decode( $response );

                        // loop through response, and set up the array by date
                        if ( isset( $response_array->errors ) ) {

                            // output errors returned by the API if applicable.
                            return $this->error( $response_array->errors[0]->message );

                        } else {

                            // otherwise, loop through the response array, and build our own array keyed by timestamp
                            foreach ( $response_array as $result ) {
                                $date = strtotime( $result->created_at );
                                $response_final[ $date ] = $result;
                            }
                        }
                    }

                    // sort response array in reverse order by the key (tweet timestamp)
                    krsort( $response_final );

                    // write the cache file
                    if ( !file_put_contents( $cache_file, serialize( $response_final ) ) ) {

                        // return a write error
                        return $this->error( "Could not write cache file." );
                    }

                } else {

                    // output 'no username' error if we didn't get any twitter handles to fetch.
                    return $this->error( "No usernames specified." );
                }

            } else {

                // grab cache file if we have it
                $response_final = unserialize( file_get_contents( $cache_file ) );
            }

            // set the data into an object property
            $this->data = $response_final;
            
            // trim the array down to the count requested in the 'count' option
            $this->data_trimmed = array_slice( $response_final, 0, $this->option['count'] );

            // return the tweets
            return $this->data_trimmed;

        }
    }



    // get the widget code
    public function widget() {

        $html = '<style>' . file_get_contents( __DIR__ . '/widget.css' ) . '</style>';

        // get the timelines
        $html .= $this->widget_unstyled();

        // return widget with styles
        return $html;
    }



    // get the widget code
    public function widget_unstyled() {

        // get the timelines
        $this->fetch();

        // output timeline
        if ( !empty( $this->data_trimmed ) ) {

            $html = '<div class="twitter-aggregator">';

            // set up a counter to output a tweet number for each tweet as we display thme.
            $tweet_count = 1;

            // loop through the trimmed data
            foreach ( $this->data_trimmed as $tweet ) {

                // generate the code for each tweet
                $html .= '<div class="twitter-aggregator-tweet tweet-' . $tweet_count  . '">';
                    $html .= '<div class="twitter-aggregator-tweet-profile-pic"><img src="' . str_replace( 'http://', 'https://', $tweet->user->profile_image_url ) . '" alt="Tweet by @' . $tweet->user->screen_name . '"></div>';
                    $html .= '<h3 class="twitter-aggregator-tweet-profile-name"><a href="https://twitter.com/' . $tweet->user->screen_name . '">' . $tweet->user->screen_name . '</a></h3>';
                    $html .= '<div class="twitter-aggregator-tweet-time">' . $this->ago( $tweet->created_at ) . ' ago</div>';
                    $html .= '<div class="twitter-aggregator-tweet-text">' . $this->make_clickable( $tweet->text ) . '</div>';
                $html .= '</div>';

                // increment counter
                $tweet_count++;
            }

            $html .= '</div>';

        }

        return $html;
    }



    // display the widget
    public function display() {
        print $this->widget();
    }



    // display the widget
    public function display_unstyled() {
        print $this->widget_unstyled();
    }



    // helper function to get a string representing the difference between two times in a human readible format
    public function ago( $tm, $rcs = 0 ) {
        if ( is_string( $tm ) ) $tm = strtotime( $tm );
        $cur_tm = time();
        $dif = $cur_tm - $tm;
        $pds = array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' );
        $lngh = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );
        for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
        $no = floor( $no ); if( $no <> 1 ) $pds[$v] .='s';
        $x=sprintf("%d %s ",$no,$pds[$v]);
        if ( ( $rcs == 1 ) && ( $v >= 1 ) && ( ( $cur_tm - $_tm ) > 0 ) ) $x .= time_ago($_tm);
        return $x;
    }



    // helper function to parse and make urls clickable.
    public function make_clickable( $string ) {
        $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';   
        $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
        return $string;
    }

    

    // error function, will return an error, or 
    public function error( $message ) {

        // set the error array into an object property
        $this->error = array(
            'error' => true,
            'error_message' => $message
        );

        // check our error display setting to see if we need to display them
        if ( $this->debug ) {

            // display error array dump and end processing
            print_r( $this->error );
            die;

        } else {

            // otherwise return the error array
            return $this->error;

        }
    }
}


/************************************
  wordpress functionality
 ***********************************/

// check if we're inside wordpress
if ( function_exists( 'add_shortcode' ) ) {

    // twitter aggregator options for the wordpress settings page and the shortcode
    $ta_options = array( 
        'ta_consumer_key', 
        'ta_consumer_secret', 
        'ta_oauth_access_token', 
        'ta_oauth_access_token_secret', 
        'ta_usenames'
    );


    /************************************
      shortcode [twitter-aggregator]
     ***********************************/
    function twitter_aggregator_shortcode( $atts ) {

        // default parameters
        $a = shortcode_atts( array(
            'consumer_key' => get_option( "ta_consumer_key", "" ),
            'consumer_secret' => get_option( "ta_consumer_secret", "" ),
            'oauth_access_token' => get_option( "ta_oauth_access_token", "" ),
            'oauth_access_token_secret' => get_option( "ta_oauth_access_token_secret", "" ),
            'usernames' => get_option( "ta_usenames", "reuters,ap,propublica" ),
            'update_interval' => 10,
            'styles' => true,
            'count' => 10,
            'exclude_replies' => true,
            'include_rts' => true
        ), $atts );


        // generate an aggregator object
        $ta = new twitterAggregator( $a );

        if ( $a['styles'] ) {
            // display the widget
            return $ta->widget();
        } else {
            // display the widget
            return $ta->widget_unstyled();
        }
    }

    // register our function as a shortcode
    add_shortcode( 'twitter-aggregator', 'twitter_aggregator_shortcode' );



    /************************************
      settings interface
     ***********************************/

    // register the settings for the first time.
    function ta_register_settings() {
        global $ta_options;

        // loop through the options and add them.
        foreach ( $ta_options as $opt ) {
            add_option( $opt, '');
            register_setting( 'ta_options_group', $opt );
        }
    }
    add_action( 'admin_init', 'ta_register_settings' );


    // register the options page in the admin menu
    function ta_register_options_page() {
        add_options_page('Twitter Aggregator Settings', 'Twitter Aggregator', 'manage_options', 'ta', 'ta_options_page');
    }
    add_action('admin_menu', 'ta_register_options_page');


    // the actual page output function
    function ta_options_page() {
        global $ta_options;
        
        ?>
        <h2>Twitter Aggregator Settings</h2>
        <form method="post" action="options.php">
        <?php

        // settings field group
        settings_fields( 'ta_options_group' );

        // loop through the options and output fields.
        foreach ( $ta_options as $opt ) {
            ?>
            <p><label for="<?php print $opt ?>"><?php print ucwords( str_replace( "ta_", "", str_replace( "_", " ", $opt ) ) ); ?></label><br>
                <input type="text" id="<?php print $opt; ?>" name="<?php print $opt; ?>" value="<?php echo get_option( $opt ); ?>" /></p>
            <?php
        }

        // add the submit button.
        submit_button();

        ?>
        </form>
        <?php
    }

}
