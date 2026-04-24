<?php
/**
 * config/nav.php  ─  サイドバーナビゲーション定義
 */
return [
    // ── 共通 ──────────────────────────────────
    'home' => [
        'href'  => '/',
        'label' => 'Home',
        'icon'  => 'M2 2h12a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1zm0 3v8h12V5H2zm0-2v1h12V3H2z',
    ],

    // ── ユーザー専用 ──────────────────────────
    'jobs' => [
        'href'  => '/jobs',
        'label' => '求人リスト',
        'icon'  => 'M11 8a3 3 0 100-6 3 3 0 000 6zm-7.5 6a6 6 0 0113 0H3.5z',
        'role'  => 'user',
    ],
    'chat' => [
        'href'        => '/chat',
        'label'       => 'アドバイザーへ相談',
        'icon'        => 'M2 1h12a1 1 0 011 1v9a1 1 0 01-1 1H5l-3 3V2a1 1 0 011-1zm1 2v7h9V3H3zm1 1h7v1H4V4zm0 3h5v1H4V7z',
        'role'        => 'user',
        'show_unread' => true,
    ],
    'resume' => [
        'href'  => '/resume',
        'label' => '履歴書・職務経歴書',
        'icon'  => 'M4 1h8a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V2a1 1 0 011-1zm0 1v12h8V2H4zm1 2h6v1H5V4zm0 3h6v1H5V7zm0 3h4v1H5v-1z',
        'role'  => 'user',
    ],
    'manual' => [
        'href'  => '#',
        'label' => '面接攻略マニュアル',
        'icon'  => 'M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8zm9-1v4H7V7h2zm0-3v2H7V4h2z',
        'role'  => 'user',
    ],

    // ── 管理者専用 ────────────────────────────
    'admin_screenings' => [
        'href'  => '/admin/screenings',
        'label' => '選考管理',
        'icon'  => 'M1 3h14v2H1V3zm0 4h14v2H1V7zm0 4h10v2H1v-2z',
        'role'  => 'admin',
    ],
    'admin' => [
        'href'  => '/admin',
        'label' => '求人管理',
        'icon'  => 'M1 2h14v2H1V2zm0 4h14v2H1V6zm0 4h14v2H1v-2zm0 4h8v2H1v-2z',
        'role'  => 'admin',
    ],
    'admin_chat' => [
        'href'        => '/admin/chat',
        'label'       => 'チャット管理',
        'icon'        => 'M2 1h12a1 1 0 011 1v9a1 1 0 01-1 1H5l-3 3V2a1 1 0 011-1zm1 2v7h9V3H3zm1 1h7v1H4V4zm0 3h5v1H4V7z',
        'role'        => 'admin',
        'show_unread' => true,
    ],
    'admin_users' => [
        'href'  => '/admin/users',
        'label' => 'ユーザー管理',
        'icon'  => 'M8 1a3 3 0 100 6 3 3 0 000-6zM3 14a5 5 0 0110 0H3z',
        'role'  => 'admin',
    ],
];
