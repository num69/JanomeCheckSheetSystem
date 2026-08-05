<?php
// ต้องตั้ง session_name ก่อน session_start เสมอ
if (session_status() == PHP_SESSION_NONE) {

    // ชื่อ session ของโปรเจคนี้ ห้ามซ้ำกับระบบอื่น
    session_name("FGWHSESSID");

    // ตั้ง cookie path ให้ผูกกับโปรเจคนี้
    // ถ้ารันที่ /CheckSheetSystem ให้ใช้ path นี้
    $cookiePath = "/CheckSheetSystem";

    // ถ้ารัน root ตรง ๆ เช่น http://localhost/ ให้เปลี่ยนเป็น "/"
    // $cookiePath = "/";

    session_set_cookie_params(
        0,              // lifetime: 0 = ปิด browser แล้วหมดอายุ
        $cookiePath,    // path
        "",             // domain
        false,          // secure: ถ้าใช้ https จริง ค่อยเปลี่ยนเป็น true
        true            // httponly
    );

    session_start();
}
?>