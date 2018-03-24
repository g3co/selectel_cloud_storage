<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 20.12.2017
 * Time: 14:54
 */


const USER = 'user_id';
const PASS = 'password';

register_shutdown_function(function () {
    print_r(error_get_last());
});

require 'vendor/autoload.php';
require 'definitions.php';

$redis = new Redis();
$redis->connect(REDIS_SERVER, REDIS_PORT, REDIS_CONNECTION_TIMEOUT);

$token = json_decode($redis->get('Storage'), true);

$client = new \GuzzleHttp\Client();

if (!$token) {
    $res = $client->request('GET', 'https://auth.selcdn.ru/',[
        'headers' => [
            'X-Auth-User' => USER,
            'X-Auth-Key' => PASS
        ]
    ]);

    if ($res->getStatusCode() == 204) {
        $token = [
            'token' => $res->getHeaderLine('X-Storage-Token'),
            'url' => $res->getHeaderLine('X-Storage-Url'),
        ];
        $redis->set('Storage', json_encode($token), (int)$res->getHeaderLine('X-Expire-Auth-Token'));
    }
}

$fileName = $_GET['filename'];

$res = $client->request('PUT', $token['url'] . 'pub_school/'. $fileName,[
    'headers' => [
        'content-type' => 'x-storage/sendmefile+inplace',
        'x-auth-token' => $token['token']
    ]
]);

if ($res->getStatusCode() == '201') {
    echo json_encode(['error'=>false, 'url' => $token['url'] . 'pub_school/'. $fileName]);
} else {
    echo json_encode(['error'=>true, 'url' => null]);
}