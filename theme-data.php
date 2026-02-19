<?php
/**
 * Theme Constants and Data
 */

function get_yoruo_categories() {
    $categories = [
        ['name' => 'キャバクラ', 'icon' => 'fa-gem', 'id' => 'キャバクラ'],
        ['name' => 'ガールズバー', 'icon' => 'fa-martini-glass-citrus', 'id' => 'ガールズバー'],
        ['name' => 'スナック', 'icon' => 'fa-music', 'id' => 'スナック'],
        ['name' => 'ラウンジ', 'icon' => 'fa-coffee', 'id' => 'ラウンジ'],
        ['name' => 'ホストクラブ', 'icon' => 'fa-star', 'id' => 'ホストクラブ'],
        ['name' => 'バー', 'icon' => 'fa-glass-martini', 'id' => 'バー'],
        ['name' => 'クラブ', 'icon' => 'fa-music', 'id' => 'クラブ'],
        ['name' => 'ニュークラブ', 'icon' => 'fa-gem', 'id' => 'ニュークラブ'],
        ['name' => '黒服(ボーイ)', 'icon' => 'fa-users', 'id' => 'Boy'],
        ['name' => '幹部候補', 'icon' => 'fa-user-tie', 'id' => 'Manager'],
        ['name' => '送りドライバー', 'icon' => 'fa-car', 'id' => 'Driver'],
        ['name' => 'キャッシャー', 'icon' => 'fa-coins', 'id' => 'Cashier'],
        ['name' => 'キッチン', 'icon' => 'fa-utensils', 'id' => 'Kitchen'],
        ['name' => 'ヘアメイク', 'icon' => 'fa-scissors', 'id' => 'HairMake'],
    ];

    foreach ($categories as &$cat) {
        $term = get_term_by('name', $cat['name'], 'job_category');
        $cat['count'] = $term ? $term->count : 0;
    }

    return $categories;
}

function get_yoruo_guides() {
    return [
        ['title' => '適職タイプ診断', 'copy' => '君の武器は何か？5分でわかる適職診断', 'micro' => '累計1万人が診断！', 'icon' => 'fa-bullseye'],
        ['title' => '稼げるノウハウ集', 'copy' => '月収100万への最短ルート。デキる男の処世術', 'micro' => '現役店長が徹底監修', 'icon' => 'fa-book-open'],
        ['title' => '未経験スタートガイド', 'copy' => '知識ゼロからプロへ。初日に差がつく基礎知識', 'micro' => '初心者の8割が閲覧中', 'icon' => 'fa-user-check'],
    ];
}

function get_yoruo_quick_tags() {
    return ['日払いOK', '未経験歓迎', '経験者優遇', '送迎あり', '寮・社宅あり', '託児所あり', '自由シフト', '週1日からOK', '昇給随時', 'ノルマなし'];
}

function get_yoruo_feature_tags() {
    return [
        '経験・資格' => ['未経験歓迎', '経験者優遇', 'ブランクOK', '学生歓迎', 'フリーター歓迎', '副業・WワークOK'],
        '待遇・福利厚生' => ['日払いOK', '週払いOK', '高額バックあり', '昇給随時', '賞与あり', '交通費支給', '送迎あり', '寮・社宅あり', '託児所あり', '社会保険完備', '社員登用あり'],
        '働き方' => ['自由シフト', '週1日からOK', '1日3h以内OK', '土日祝のみOK', '平日のみOK', '夜からの仕事', '深夜・早朝の仕事', '短期間OK'],
        '職場環境' => ['ノルマなし', 'ペナルティなし', 'ドレス・スーツ貸出', 'ヘアメイク完備', '履歴書不要', 'オンライン面接OK', 'ニューオープン', 'アットホーム']
    ];
}

function get_yoruo_salary_options() {
    return [
        ['id' => '月給30万円〜', 'name' => '月給30万円〜', 'icon' => 'fa-money-bill-wave'],
        ['id' => '月給50万円〜', 'name' => '月給50万円〜', 'icon' => 'fa-gem'],
        ['id' => '月給80万円〜', 'name' => '月給80万円〜', 'icon' => 'fa-trophy'],
        ['id' => '日給1.2万円〜', 'name' => '日給1.2万円〜', 'icon' => 'fa-hand-holding-dollar'],
        ['id' => '時給1,500円〜', 'name' => '時給1,500円〜', 'icon' => 'fa-clock'],
        ['id' => '高額バックあり', 'name' => '高額バックあり', 'icon' => 'fa-percent'],
        ['id' => '即日日払いOK', 'name' => '即日日払いOK', 'icon' => 'fa-wallet'],
    ];
}

function get_yoruo_work_styles() {
    return [
        ['id' => '正社員・転職', 'name' => '正社員・転職', 'icon' => 'fa-briefcase'],
        ['id' => 'アルバイト', 'name' => 'アルバイト', 'icon' => 'fa-utensils'],
        ['id' => '派遣', 'name' => '派遣', 'icon' => 'fa-calculator'],
        ['id' => '業務委託', 'name' => '業務委託', 'icon' => 'fa-scissors'],
        ['id' => 'フリーランス', 'name' => 'フリーランス', 'icon' => 'fa-laptop-code'],
        ['id' => '新卒', 'name' => '新卒', 'icon' => 'fa-graduation-cap'],
    ];
}

function get_yoruo_prefectures() {
    return [
        '東京都', '神奈川県', '埼玉県', '千葉県', '大阪府', '京都府', '兵庫県', '愛知県', '福岡県', '北海道', '沖縄県'
    ];
}
