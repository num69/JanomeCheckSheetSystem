<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";

authorized();

$menu = array(
        array(
            "type" => "link",
            "label" => "Home",
            "icon" => "fas fa-home",
            "href" => "home.php"
        ),
        array(
            "type" => "section",
            "label" => "Warehouse",
            "children" => array(
                array(
                    "type" => "link",
                    "label" => "Receive",
                    "icon" => "fas fa-dolly",
                    "href" => "pages/receive/index.php"
                ),
                array(
                    "type" => "link",
                    "label" => "Issue / Delivery",
                    "icon" => "fas fa-truck-loading",
                    "href" => "pages/issue/index.php"
                ),
                array(
                    "type" => "link",
                    "label" => "Transfer",
                    "icon" => "fas fa-exchange-alt",
                    "href" => "pages/transfer/index.php"
                ),
                array(
                    "type" => "submenu",
                    "label" => "Report",
                    "icon" => "fas fa-chart-line",
                    "children" => array(
                        array(
                            "type" => "link",
                            "label" => "Report Overview",
                            "href" => "pages/warehouse/report/index.php"
                        ),
                        array(
                            "type" => "link",
                            "label" => "FG Stock",
                            "href" => "pages/warehouse/report/fgStock.php"
                        )
                    )
                )
            )
        ),
        array(
            "type" => "section",
            "label" => "Operation",
            "children" => array(
                array(
                    "type" => "link",
                    "label" => "Scan Barcode",
                    "icon" => "fas fa-barcode",
                    "href" => "pages/scan/index.php"
                ),
                array(
                    "type" => "link",
                    "label" => "Stock Check",
                    "icon" => "fas fa-clipboard-check",
                    "href" => "pages/stock-check/index.php"
                )
            )
        ),
        array(
            "type" => "section",
            "label" => "System",
            "children" => array(
                array(
                    "type" => "link",
                    "label" => "Status",
                    "icon" => "fas fa-cogs",
                    "href" => "pages/status/index.php"
                ),
                array(
                    "type" => "submenu",
                    "label" => "Master Data",
                    "icon" => "fas fa-cog",
                    "children" => array(
                        array(
                            "type" => "link",
                            "label" => "Setup Master",
                            "href" => "pages/master/index.php"
                        )
                    )
                )
            )
        )
    );

// sleep(2); // Simulate a delay of 5 seconds

jsonResponseOk(array(
    "menu" => $menu,
    "generatedAt" => date(DATE_ATOM)
));