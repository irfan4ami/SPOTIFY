<?php


function database(){
    $api_spotify = '142b583129b2df829de3656f9eb484e6'; 
    $api_capsolver = ''; // apicapsolver
    $password = ''; // password_akun
    return array(
        $api_spotify,
        $api_capsolver,
        $password
    );
}


function random(){
    $url = "https://gitburn.my.id/fake-generator/";
    $header = array();
        $header[] = 'Host: gitburn.my.id';
        $header[] = 'Content-Type: application/json';
        $header[] = 'User-Agent: ShellGoPlus%20Production/36 CFNetwork/1404.0.5 Darwin/22.3.0';
    $get = get_post($url, null, $header);
    return $get;
}


function spotify_create($nama, $email, $password){
    $url = "https://spclient.wg.spotify.com/signup/public/v2/account/create";
    $ttl = rand(1980, 2010)."-".rand(10, 12)."-".rand(10, 31);
    $gender = rand(1, 2);
    $api_spotify = database()[0];
    $body = '{"account_details":{"birthdate":"'.$ttl.'","consent_flags":{"eula_agreed":true,"send_email":false,"third_party_email":false},"display_name":"'.$nama.'","email_and_password_identifier":{"email":"'.$email.'","password":"'.$password.'"},"gender":"'.$gender.'"},"callback_uri":"","client_info":{"api_key":"'.$api_spotify.'","app_version":"v2","capabilities":[1],"installation_id":"","platform":""},"tracking":{"creation_flow":"","creation_point":"","referrer":""},"recaptcha_token":"","submission_id":""}';
    //echo "$body\n\n";
    $header = array();
        $header[] = 'Host: spclient.wg.spotify.com';
        $header[] = 'Content-Type: application/json';
        $header[] = 'User-Agent: ShellGoPlus%20Production/36 CFNetwork/1404.0.5 Darwin/22.3.0';
    $get = get_post($url, $body, $header);
    //echo "$get[1]\n\n";
    if ($get[3] != 200){
        echo @color('red', "\nTerjadi Kesalahan");
        main();
    }
    if (isset(json_decode($get[1])->challenge)){
        $session_id = json_decode($get[1])->challenge->session_id;
    } else if(!isset(json_decode($get[1])->login_token)) {
        echo @color('red', "\nAKUN SUDAH TERDAFTAR");
        save("$email;$password\n", "akun_spotify.txt");
        main();
    } else {
        echo @color('red', "\nTerjadi Kesalahan");
        main();
    }
    return $session_id;
}


function spotify_challenge($session_id){
    $url = "https://challenge.spotify.com/api/v1/get-session";
    $body = '{"session_id": "'.$session_id.'"}';
    $header = array();
        $header[] = 'Host: challenge.spotify.com';
        $header[] = 'Content-Type: application/json';
        $header[] = 'User-Agent: ShellGoPlus%20Production/36 CFNetwork/1404.0.5 Darwin/22.3.0';
    $get = get_post($url, $body, $header);
    $chalange_id = json_decode($get[1])->in_progress->challenge_details->challenge_id;
    return $chalange_id;
}


function capsolver_bypass(){
    $url = "https://api.capsolver.com/createTask";
    $api_capsolver = database()[1];
    $body = '{
        "clientKey": "'.$api_capsolver.'",
        "task": {
            "type": "ReCaptchaV2TaskProxyLess",
            "websiteURL": "https://www.spotify.com/id-id/signup",
            "websiteKey": "6LeO36obAAAAALSBZrY6RYM1hcAY7RLvpDDcJLy3",
            "pageAction": "signup"
        }
    }';
    $header = array();
        $header[] = 'Host: api.capsolver.com';
        $header[] = 'Content-Type: application/json';
        $header[] = 'User-Agent: ShellGoPlus%20Production/36 CFNetwork/1404.0.5 Darwin/22.3.0';
    $get = get_post($url, $body, $header);
    if ($get[3] != 200){
        echo @color('red', "\nTerjadi Kesalahan generate chapcha");
        main();
    }
    $task_id = json_decode($get[1])->taskId;
    request:
    $url2 = "https://api.capsolver.com/getTaskResult";
    $body2 = '{
        "clientKey": "'.$api_capsolver.'",
        "taskId": "'.$task_id.'"
    }';
    $get2 = get_post($url2, $body2, $header);
    //echo "$get2[1]\n\n";
    if ($get2[3] != 200){
        echo @color('red', "\nTerjadi Kesalahan generate solution chapcha");
        main();
    }
    if (json_decode($get2[1])->status == "processing"){
        echo '.';
        goto request;
    } else if (json_decode($get2[1])->status == "ready"){
        $solution = json_decode($get2[1])->solution;
    } else {
        echo @color('red', "$get2[1]\n\n");
        die();
    }

    return array(
        $solution->gRecaptchaResponse,
        $solution->userAgent,
    );
    }



function spotify_bypass($session_id, $challenge_id, $gRecaptchaResponse, $userAgent){
    $url = "https://challenge.spotify.com/api/v1/invoke-challenge-command";
    $body = '{"session_id":"'.$session_id.'","challenge_id":"'.$challenge_id.'","recaptcha_challenge_v1":{"solve":{"recaptcha_token":"'.$gRecaptchaResponse.'"}}}';
    $header = array();
        $header[] = 'Host: challenge.spotify.com';
        $header[] = 'Content-Type: application/json';
        $header[] = 'User-Agent: '.$userAgent;
    $get = get_post($url, $body, $header);
    if ($get[3] != 200){
        echo @color('red', "\nTerjadi Kesalahan saat login ");
        main();
    }
    $url2 = "https://spclient.wg.spotify.com/signup/public/v2/account/complete-creation";
    $body2 = '{"session_id": "'.$session_id.'"}';
    $header2 = array();
        $header2[] = 'Host: spclient.wg.spotify.com';
        $header2[] = 'Content-Type: application/json';
        $header2[] = 'User-Agent: ShellGoPlus%20Production/36 CFNetwork/1404.0.5 Darwin/22.3.0';
    $get2 = get_post($url2, $body2, $header2);
    return $get2;
}

