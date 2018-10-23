<?php
    $file = "ids.php";

    $config = require('config.php');
    /* Include files */
    $get_ids = file_get_contents($config['group_list']);
    $log = $config['log'];
    $file_redirects = $config['redirects'];
    /* Convert groups to ids array */
    $add = explode(",\r\n", $get_ids);
    echo '<pre>';
    print_r($add);
    echo '</pre>';
    // check if all is ok with the array
    if(count($add) < 3) {
        exit("Array is too short");
    }
    /* Repeat for max times */
    for($n=0; $n < $config['max']; $n++) {
        var_dump($add[$n]);
        $request_params = array(
            'owner_id' => $add[$n],
            'message' =>  $config['content_message'],
            'attachments' =>  $config['content_attachments'],
            'v' => '5.84',
            'access_token' =>  $config['access_token']
        );

        $get_params = http_build_query($request_params);

        $result = json_decode(file_get_contents('https://api.vk.com/method/wall.post?'. $get_params));
        $today = date("Y-m-d H:i:s");
        if(!empty($result->error)) {
            $error = $today." -> ".$result->error->error_msg. " :error \r";
            file_put_contents($log, $error, FILE_APPEND | LOCK_EX);
            continue;
        }
        else if($result->response) {
            var_dump($add[$n]);
            $success = $today." -> ".$result->response->post_id. " :success \r";
            file_put_contents($log, $success, FILE_APPEND | LOCK_EX);
            $redirect = "https://vk.com/club".abs($add[$n]).",\r";
            file_put_contents($file_redirects, $redirect, FILE_APPEND | LOCK_EX);

            if (($key = array_search($add[$n], $add)) !== false) {
                unset($add[$key]);
            }

            $save = updateList($add);
            file_put_contents($file, $save, LOCK_EX);
        }
        // print_r($add);
        sleep(100);
    }

    function updateList($add) {
        foreach($add as $element) {
            $result[] = $element.",\r\n";
        }

        return $result;
    }

echo "done";

?>