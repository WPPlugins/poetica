<?php
    global $poetica_plugin;

    $accessToken = get_user_option('poetica_user_access_token', false);
    $groupAccessToken = get_option('poetica_group_access_token', null);

    $posts_array = get_posts(
      array(
        'post_status' =>'any',
        'meta_key'   => 'poeticaLocation',
        'numberposts'   => 200
      )
    );
    foreach($posts_array as $post) {
      $newContent = $poetica_plugin->get_poetica_content($post->ID);

      if($newContent != $post->post_content) {
        $post->post_content = $newContent;
        wp_update_post($post);
      }
      delete_post_meta($post->ID, 'poeticaLocation');
    }

    $posts_array = get_posts(
      array(
        'post_status' =>'any',
        'meta_key'   => 'poeticaApiLocation',
        'numberposts'   => 200
      )
    );
    foreach($posts_array as $post) {
      delete_post_meta($post->ID, 'poeticaApiLocation');
    }

    $pages_array = get_posts(
      array(
        'post_status' => 'any',
        'post_type'   => 'page',
        'meta_key'    => 'poeticaLocation',
        'numberposts'   => 200
      )
    );
    foreach($pages_array as $page) {
      $newContent = $poetica_plugin->get_poetica_content($page->ID);

      if($newContent != $post->post_content) {
        $post->post_content = $newContent;
        wp_update_post($post);
      }
      delete_post_meta($page->ID, 'poeticaLocation');
    }

    $pages_array = get_posts(
      array(
        'post_status' => 'any',
        'post_type'   => 'page',
        'meta_key'    => 'poeticaApiLocation',
        'numberposts'   => 200
      )
    );
    foreach($pages_array as $page) {
      delete_post_meta($page->ID, 'poeticaApiLocation');
    }

    wp_remote_get($this->domains[self::$env].'/api/track.json?category=wpplugin&action=switchall&group_access_token='.$groupAccessToken.'&access_token='.$accessToken, array());

    delete_option('poetica_group_access_token');
    delete_option('poetica_group_subdomain');
    delete_option('poetica_verification_token');

    $users = get_users(
      array(
        'meta_query'  => array(
          'key'     => 'poetica_user_access_token',
          'value'   => '',
          'compare' => '>',
        )
      )
    );
    foreach ( $users as $user ) {
      delete_user_option($user->ID, 'poetica_user_access_token');
    }

    exit(wp_safe_redirect(admin_url()));
?>
