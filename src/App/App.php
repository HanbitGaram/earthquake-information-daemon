<?php
namespace App;
use Library\File;
use Library\FileCacheLibrary;
use Library\HttpClient;
use Library\Translator;

class App {
    public string $endpoint = 'https://api.p2pquake.net/v2/history?codes=551&limit=1';
    public string $cachePath = __DIR__.'/../../cache';
    public string $cacheName = 'p2pquake-history';
    public int $cacheTime = 3;
    public array|null $data = null;

    protected function request(){
        if(!FileCacheLibrary::isCache($this->cachePath, $this->cacheName, $this->cacheTime)){
            $client = new HttpClient('GET', [
                'url' => $this->endpoint,
                'headers' => ['Accept: application/json'],
                'timeout' => 5,
                'follow' => true,
                'showHeader' => false
            ]);
            $response = $client->send();

            if($response !== false && isset($response['body'])){
                $this->data = json_decode($response['body'], true);
                if(json_last_error() === JSON_ERROR_NONE){
                    FileCacheLibrary::setHCache($this->cachePath, $this->cacheName, $this->data);
                    return $this->data;
                }
            }
        }else{
            $this->data = FileCacheLibrary::getHCache($this->cachePath, $this->cacheName, $this->cacheTime);
            return $this->data;
        }
        return false;
    }
    public function run(){
        if(!is_dir($this->cachePath)){
            echo "캐시 디렉토리가 존재하지 않습니다. 캐시 디렉토리를 생성합니다.\n";
            mkdir($this->cachePath, 0755, true);
            if(!is_dir($this->cachePath)){
                echo "캐시 디렉토리 생성에 실패했습니다. 권한을 확인해주세요.\n";
                return false;
            }
        }
        if(!is_file(__DIR__.'/../../token.txt')){
            echo "토큰 파일이 존재하지 않습니다. 토큰 파일을 생성해주세요.\n";
            return false;
        }
        $loadFile = new File();
        $token = $loadFile->load(__DIR__.'/../../token.txt');

        $this->request();
        if(!is_array($this->data) || empty($this->data) || count($this->data) === 0){
            return false;
        }

        $data = $this->data[0];
        if($data['id'] === $loadFile->load($this->cachePath.'/id.txt')){
            echo "이미 처리된 데이터입니다.\n";
            return false;
        }

        $text = [
            '진원지'=>'미상',
            '규모'=>'미상',
            '발생시간'=>'미상',
            '최대진도'=>'미상',
            '국내 해일 발생 여부'=>'미상',
            '해외에서의 해일 발생여부'=>'미상',
            '데이터제공자'=>'미상',
            '발표시간'=>'미상',
            '알림정보'=>'미상',
            '진원 발생 위치 정보'=>'미상'
        ];

        if(isset($data['earthquake']['hypocenter'])){
            $hypo = $data['earthquake']['hypocenter'];
            $text['진원지'] = $hypo['name'] ?? '미상';
            $text['규모'] = $hypo['magnitude'] ?? '미상';
            $text['발생시간'] = $data['earthquake']['time'] ?? '미상';
            $text['최대진도'] = isset($data['earthquake']['maxScale']) ? Translator::maxScale($data['earthquake']['maxScale']) : '미상';
            $text['국내 해일 발생 여부'] = isset($data['earthquake']['domesticTsunami']) ? Translator::domesticTsunami($data['earthquake']['domesticTsunami']) : '미상';
            $text['해외에서의 해일 발생여부'] = isset($data['earthquake']['foreignTsunami']) ? Translator::foreignTsunami($data['earthquake']['foreignTsunami']) : '미상';
            $text['진원 발생 위치 정보'] = '위도: '.$hypo['latitude'].' 경도: '.$hypo['longitude'].' 깊이: '.$hypo['depth'].'km';
        }

        if(isset($data['points']) && is_array($data['points']) && count($data['points']) > 0){
           foreach($data['points'] as $number => $point) {
                if(isset($point['addr']) && isset($point['scale'])){
                    $text['발생지역'.($number+1)] = Translator::prefectures($point['pref'] ?? '미상') . ' - '.$point['addr'];
                }
            }
        }

        if(isset($data['issue'])){
            $text['데이터제공자'] = Translator::source($data['issue']['source']?? '미상');
            $text['발표시간'] = $data['issue']['time'] ?? '미상';
            $text['알림정보'] = Translator::type($data['issue']['type']?? '미상');
        }

        $realText = "[일본 지진 발생 정보]\n";
        foreach ($text as $key => $value) {
            $realText .= $key . ': ' . $value . "\n";
        }

        $http = new HttpClient('POST', [
            'url' => 'https://ani.work/api/v1/statuses',
            'headers' => [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Bearer '. trim($token),
                'Idempotency-Key: '.$data['id']
            ],
            'data' => [
                'status' => $realText,
            ],
            'json' => true,
            'showHeader' => true
        ]);

        $res = $http->send();

        if($res){
            $response = $res['body'];
            if(isset($response['error'])){
                echo "오류 발생: " . $response['error'] . "\n";
            } else {
                echo "성공적으로 전송되었습니다. ID: " . $data['id'] . "\n";
                $loadFile->save($this->cachePath.'/id.txt', $data['id']);
            }
        }

    }
}
