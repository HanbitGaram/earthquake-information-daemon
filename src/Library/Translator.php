<?php
namespace Library;

// 번역이 불가능하다 싶으면 바로 원본을 반환하기

class Translator {
    public static function prefectures(string $prefectureName): string
    {
        return match ($prefectureName) {
            '東京都' => '도쿄도(東京都)',
            '北海道' => '홋카이도(北海道)',
            '大阪府' => '오사카부(大阪府)',
            '愛知県' => '아이치현(愛知県)',
            '神奈川県' => '가나가와현(神奈川県)',
            '福岡県' => '후쿠오카현(福岡県)',
            '千葉県' => '치바현(千葉県)',
            '埼玉県' => '사이타마현(埼玉県)',
            '兵庫県' => '효고현(兵庫県)',
            '京都府' => '교토부(京都府)',
            '静岡県' => '시즈오카현(静岡県)',
            '茨城県' => '이바라키현(茨城県)',
            '広島県' => '히로시마현(広島県)',
            '宮城県' => '미야기현(宮城県)',
            '新潟県' => '니가타현(新潟県)',
            '岐阜県' => '기후현(岐阜県)',
            '栃木県' => '도치기현(栃木県)',
            '群馬県' => '군마현(群馬県)',
            '福島県' => '후쿠시마현(福島県)',
            '長野県' => '나가노현(長野県)',
            '岡山県' => '오카야마현(岡山県)',
            '熊本県' => '구마모토현(熊本県)',
            '鹿児島県' => '가고시마현(鹿児島県)',
            '三重県' => '미에현(三重県)',
            '長崎県' => '나가사키현(長崎県)',
            '愛媛県' => '에히메현(愛媛県)',
            '山口県' => '야마구치현(山口県)',
            '大分県' => '오이타현(大分県)',
            '石川県' => '이시카와현(石川県)',
            '奈良県' => '나라현(奈良県)',
            '和歌山県' => '와카야마현(和歌山県)',
            '佐賀県' => '사가현(佐賀県)',
            '香川県' => '가가와현(香川県)',
            '青森県' => '아오모리현(青森県)',
            '秋田県' => '아키타현(秋田県)',
            '山形県' => '야마가타현(山形県)',
            '富山県' => '도야마현(富山県)',
            '福井県' => '후쿠이현(福井県)',
            '滋賀県' => '시가현(滋賀県)',
            '島根県' => '시마네현(島根県)',
            '鳥取県' => '돗토리현(鳥取県)',
            '徳島県' => '도쿠시마현(徳島県)',
            '高知県' => '고치현(高知県)',
            '山梨県' => '야마나시현(山梨県)',
            '沖縄県' => '오키나와현(沖縄県)',
            default => $prefectureName,
        };
    }

    public static function source(string $source): string
    {
        return match ($source) {
            '気象庁' => '기상청(気象庁)',
            default => $source,
        };
    }

    public static function type(string $type): string
    {
        return match ($type) {
            'ScalePrompt' => '진도속보(震度速報)',
            'Destination' => '진원에 관한 정보(震源に関する情報)',
            'ScaleAndDestination' => '진도·진원에 관한 정보(震度・震源に関する情報)',
            'DetailScale' => '각지의 진도 정보(各地の震度に関する情報)',
            'Foreign' => '원거리 지진 정보(遠地地震に関する情報)',
            'Other' => '기타 정보',
            default => $type,
        };
    }
    public static function maxScale(string $scale): string
    {
        return match ($scale) {
            '10' => '1',
            '20' => '2',
            '30' => '3',
            '40' => '4',
            '50' => '5',
            '60' => '6',
            '70' => '7',
            '80' => '8',
            '90' => '9',
            '-1' => '정보없음',
            default => $scale,
        };
    }

    public static function domesticTsunami(string $tsunami): string
    {
        return match ($tsunami) {
            'None' => '없음',
            'Unknown' => '알 수 없음',
            'Checking' => '조사중',
            'NonEffective' => '약간의 해면 변동이 예상되지만 피해의 걱정 없음',
            'Watch' => '해일 주의보',
            'Warning' => '해일 예보',
            default => $tsunami,
        };
    }

    public static function foreignTsunami(string $tsunami): string
    {
        return match ($tsunami) {
            'None' => '없음',
            'Unknown' => '알 수 없음',
            'Checking' => '조사중',
            'NonEffectiveNearby'=>'진원 근방에서 작은 쓰나미의 가능성이 있지만, 피해의 걱정 없음',
            'WarningNearby'=>'진원 근방에서 쓰나미의 가능성이 있음',
            'WarningPacific'=>'태평양에서 쓰나미의 가능성이 있음',
            'WarningPacificWide'=>'태평양의 광역에서 쓰나미의 가능성이 있음',
            'WarningIndian'=>'인도양에서 쓰나미의 가능성이 있음',
            'WarningIndianWide'=>'인도양의 광역에서 쓰나미의 가능성이 있음',
            'Potential'=>'쓰나미의 가능성이 있음',
            default => $tsunami,
        };
    }
}
