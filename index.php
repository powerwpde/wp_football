<?php

function ajax_update_live_score( ) {
    $fixture = $_POST['fixture'];

    // We use transients to cache requests
    if ( ! get_transient( 'live_score_' . $fixture ) ) {
        $data = fetch_live_score( $fixture );
        set_transient( 'live_score_' . $fixture, $data, 60 );
    }

    $live_score = get_transient( 'live_score_' . $fixture );

    wp_send_json_success( $live_score );  // Return live score data with json success

    wp_die(); // you got killed
}

function fetch_live_score( $fixture ) {
    // WP REMOTE GET (maybe use try / catch for better error handling)
    $endpoint = 'https://v3.football.api-sports.io/fixtures';
    $query = build_query( [
        'id'    => $fixture
    ] );
    $url = $endpoint . '' . $query;

    $headers = [
        'x-rapidapi-key' => 'XxXxXxXxXxXxXxXxXxXxXxXx',
        'x-rapidapi-host' => 'v3.football.api-sports.io'
    ];
    $response = wp_remote_get( $url, $headers );

    // Check if there is an error
    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        // Something went wrong
        return; // We have troubles to fetch data
    }

    $response = $response['body'];

    return $response;
}


# Get team fixtures by team id (update daily wp_schedule_event)
# print next event as data attribute
# if date and time is reached do ajax until game is over