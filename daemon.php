<?php

include_once(__DIR__.'/vendor/autoload.php');

use App\App;

$app = new App();
$count = 0;
while(true){
    if($count >= 100){
        echo "프로세스 과열 방지 종료\n";
        break;
    }
    try{
        $app->run();
    }catch(Throwable $e){

    }

    $count++;
    sleep(5);
}
