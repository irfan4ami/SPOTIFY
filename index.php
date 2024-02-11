<?php
error_reporting(E_ALL);

require('function.php');
require('api.php');

main();

function banner(){
    echo @color('green', "
    _____ ____    ___   ______  ____  _____  __ __ 
    / ___/|    \  /   \ |      Tl    j|     ||  T  T
   (   \_ |  o  )Y     Y|      | |  T |   __j|  |  |
    \__  T|   _/ |  O  |l_j  l_j |  | |  l_  |  ~  |
    /  \ ||  |   |     |  |  |   |  | |   _] l___, |
    \    ||  |   l     !  |  |   j  l |  T   |     !
     \___jl__j    \___/   l__j  |____jl__j   l____/     
");
echo @color('yellow', database()[2]);
echo @color('nevy', ' { '.database()[1].' }');
}


function create(){
    $random = json_decode(random()[1]);
    $nama = $random->fullname;
    $email = $random->email;
    $password = database()[2];
    echo @color('green', "$email : ");
    $session_id = spotify_create($nama, $email, $password);
    $challenge_id = spotify_challenge($session_id);
    $solution = capsolver_bypass();
    $bypass = spotify_bypass($session_id, $challenge_id, $solution[0], $solution[1]);
    if ($bypass[3] != 200){
        echo @color('red', "FAILED");
    } else {
        echo @color('nevy', "SUCCESS");
        save("$email->$password\n", "akun_spotify.txt");
    }
}


function main(){
    banner();
    if (database()[2] == '' || database()[1] == ''){
        echo @color('red', "\n\nLENGKAPI DATA PADA FILE API");
        die();
    }
    input:
    echo @color('green', "\n\nAmount : ");
    $amount = input();
    if ($amount >= 21){
        echo @color('red', "\nMINIMAL MAKSIMAL");
        goto input;
    }

    for ($i = 1; $i <= $amount; $i++){
        echo @color('yellow', "\n[$i] ");
        create();
    }
}