<?php
    $file = "ids.php";
    $config = require('config.php');
    $group_ids = [];

    $user_id = 210700286;

    $request_params = array(
        'q' => "Песни гитара",
        'type' => "group",
        'sort' => "2",
        'count' => 100,
        'v' => '5.84',
        'access_token' => $config['access_token']
    );
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/groups.search?'. $get_params));
        // print_r($result);

        foreach($result->response->items as $group) {
            // print_r($group);
            $request_params = array(
                "group_id" => $group->id,
                'fields' => "can_post, can_upload_video, wall",
                'v' => '5.84',
                'access_token' => $config['access_token']
            );

            $get_params = http_build_query($request_params);
            $check_can_post = json_decode(file_get_contents('https://api.vk.com/method/groups.getById?'. $get_params));
            if($check_can_post->response[0]->can_post == 1) {
                array_push($group_ids, "-".$check_can_post->response[0]->id.",\r\n");
            }
            sleep(1);
        }
        // print_r($group_ids);
        file_put_contents($file, $group_ids, FILE_APPEND | LOCK_EX);
        
    echo "DONE";
?>