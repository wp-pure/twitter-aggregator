<?php


/************************************
  wordpress functionality
 ***********************************/

// check if we're inside wordpress
if ( function_exists( 'add_shortcode' ) ) {


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

    // options in the array.
    global $ta_options;
    $ta_options = array( 
        'ta_consumer_key', 
        'ta_consumer_secret', 
        'ta_oauth_access_token', 
        'ta_oauth_access_token_secret', 
        'ta_usenames'
    );


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

        // if we're using the pure framework
        if ( defined( 'PURE' ) ) {

            // add as a submenu item
            add_submenu_page( 'pure', 'Twitter Aggregator Settings', 'Twitter Aggregator', 'manage_options', 'theme_twitter_aggregator', 'ta_options_page' );
        } else {

            // otherwise, add as an item under 'Settings'
            add_options_page( 'Twitter Aggregator Settings', 'Twitter Aggregator', 'manage_options', 'ta', 'ta_options_page' );
        }
    }
    add_action( 'admin_menu', 'ta_register_options_page', 10 );



    // the actual page output function
    function ta_options_page() {
        global $ta_options;
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Twitter Aggregator Settings</h1>
            <p>Enter your Twitter API key and oAuth settings generated at <a href="https://dev.twitter.com/apps/new" target="_blank">https://dev.twitter.com/apps/new</a>, and provide a comma-separated list of twitter handles for the tool to fetch.</p>
            <hr>
            <style>.ta-field { padding: 10px; min-width: 280px; width: 100%; }</style>
            <form method="post" action="options.php">
            <?php

            // settings field group
            settings_fields( 'ta_options_group' );

            // loop through the options and output fields.
            foreach ( $ta_options as $opt ) {
                ?>
                <p><label for="<?php print $opt ?>"><?php print ucwords( str_replace( "_", " ", str_replace( "ta_", "", $opt ) ) ); ?></label><br>
                    <input type="text" class="ta-field" id="<?php print $opt; ?>" name="<?php print $opt; ?>" value="<?php echo get_option( $opt ); ?>" /></p>
                <?php
            }

            // add the submit button.
            submit_button();

            ?>
            </form>
        </div>
        <?php
    }

}

