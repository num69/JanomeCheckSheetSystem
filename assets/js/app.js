"use strict";

/**
 * ============================================================
 * APP FUNCTION INDEX
 * ============================================================
 *
 * FORMAT
 * - window.app.formatNumber(value)
 *
 * ALERT / DIALOG
 * - window.app.showSuccess(message, options)
 * - window.app.showWarning(message, options)
 * - window.app.showError(message, options)
 * - window.app.confirmDelete(callback)
 *
 * LOADING
 * - window.app.showScLoading(text)
 * - window.app.hideScLoading()
 * - window.app.delay(ms)
 * - window.app.loadingDelay(ms, callback)
 *
 * AJAX / API
 * - window.app.handleAjaxError(xhr, defaultMessage)
 * - window.app.apiUrl(path)
 *
 * QUERY STRING
 * - window.app.getQueryParam(name, defaultValue)
 * - window.app.setQueryParams(values, replaceHistory)
 *
 * PAGINATION
 * - window.app.createPagination(options)
 * - window.app.renderAppPagination(
 *       $container,
 *       pagination,
 *       onPageChange
 *   )
 * - window.app.appendPaginationButton(
 *       $pages,
 *       text,
 *       page,
 *       disabled,
 *       active,
 *       onPageChange
 *   )
 * - window.app.getPaginationRange(currentPage, totalPages)
 *
 * PAGE INITIALIZATION
 * - Select2 initialization
 * - Sidebar initialization
 *
 * ============================================================
 */

window.app = window.app || {};


/**
 * ============================================================
 * FORMAT
 * ============================================================
 */

window.app.formatNumber = function (value) {
    var number = Number(value);

    if (isNaN(number)) {
        return "0";
    }

    return number.toLocaleString("th-TH");
};


/**
 * ============================================================
 * ALERT / DIALOG
 * ============================================================
 */

window.app.showSuccess = function (message, options) {
    options = options || {};

    return Swal.fire(
        $.extend(
            {
                icon: "success",
                title: "สำเร็จ",
                text: message,
                timer: 1500,
                showConfirmButton: false,
                scrollbarPadding: false
            },
            options
        )
    );
};


window.app.showWarning = function (message, options) {
    options = options || {};

    return Swal.fire(
        $.extend(
            {
                icon: "warning",
                title: "คำเตือน",
                text: message,
                scrollbarPadding: false
            },
            options
        )
    );
};


window.app.showError = function (message, options) {
    options = options || {};

    return Swal.fire(
        $.extend(
            {
                icon: "error",
                title: "เกิดข้อผิดพลาด",
                text: message,
                scrollbarPadding: false
            },
            options
        )
    );
};


window.app.confirmDelete = function (callback) {
    return Swal.fire({
        title: "ยืนยันการลบ?",
        text: "คุณต้องการลบข้อมูลนี้ใช่หรือไม่",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "ลบ",
        cancelButtonText: "ยกเลิก",
        scrollbarPadding: false
    }).then(function (result) {
        if (
            result.isConfirmed &&
            typeof callback === "function"
        ) {
            return callback();
        }

        return false;
    });
};


/**
 * ============================================================
 * LOADING
 * ============================================================
 */

window.app.showScLoading = function (text) {
    var loadingText = text || "กำลังโหลดข้อมูล...";

    $("#pageLoading .loading-text").text(loadingText);
    $("#pageLoading").removeClass("d-none");
};


window.app.hideScLoading = function () {
    $("#pageLoading").addClass("d-none");
};


window.app.delay = function (ms) {
    return new Promise(function (resolve) {
        setTimeout(resolve, ms);
    });
};


window.app.loadingDelay = async function (ms, callback) {
    window.app.showScLoading();

    try {
        await window.app.delay(ms);

        if (typeof callback === "function") {
            return await callback();
        }

        return null;
    } finally {
        window.app.hideScLoading();
    }
};


/**
 * ============================================================
 * AJAX / API
 * ============================================================
 */

window.app.handleAjaxError = function (
    xhr,
    defaultMessage
) {
    var message =
        defaultMessage ||
        "ไม่สามารถเชื่อมต่อระบบได้";

    if (
        xhr &&
        xhr.responseJSON &&
        xhr.responseJSON.message
    ) {
        message = xhr.responseJSON.message;
    } else if (xhr && xhr.status === 401) {
        message =
            "Session หมดอายุ กรุณาเข้าสู่ระบบใหม่";
    } else if (xhr && xhr.status === 403) {
        message =
            "คุณไม่มีสิทธิ์ดำเนินการ";
    } else if (xhr && xhr.status === 404) {
        message =
            "ไม่พบ API ที่เรียกใช้งาน";
    } else if (xhr && xhr.status === 500) {
        message =
            "เกิดข้อผิดพลาดภายใน Server";
    }

    return window.app.showError(message);
};


window.app.apiUrl = function (path) {
    var baseUrl = "";

    if (
        window.APP_CONFIG &&
        window.APP_CONFIG.baseUrl
    ) {
        baseUrl = window.APP_CONFIG.baseUrl;
    }

    return (
        baseUrl +
        String(path || "").replace(/^\/+/, "")
    );
};


/**
 * ============================================================
 * QUERY STRING
 * ============================================================
 */

window.app.getQueryParam = function (
    name,
    defaultValue
) {
    var params = new URLSearchParams(
        window.location.search
    );

    var value = params.get(name);

    if (
        value === null ||
        value === ""
    ) {
        return defaultValue;
    }

    return value;
};


window.app.setQueryParams = function (
    values,
    replaceHistory
) {
    var params = {};
    var currentQuery =
        window.location.search.substring(1);

    if (currentQuery !== "") {
        var parts = currentQuery.split("&");

        $.each(parts, function (_, part) {
            if (!part) {
                return;
            }

            var pair = part.split("=");
            var key = decodeURIComponent(pair[0]);

            var value = pair.length > 1
                ? decodeURIComponent(
                    pair.slice(1).join("=")
                )
                : "";

            params[key] = value;
        });
    }

    $.each(values || {}, function (key, value) {
        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            delete params[key];
        } else {
            params[key] = String(value);
        }
    });

    var queryParts = [];

    $.each(params, function (key, value) {
        queryParts.push(
            encodeURIComponent(key) +
            "=" +
            encodeURIComponent(value)
        );
    });

    var newUrl = window.location.pathname;

    if (queryParts.length > 0) {
        newUrl += "?" + queryParts.join("&");
    }

    newUrl += window.location.hash;

    if (replaceHistory === true) {
        window.history.replaceState(
            null,
            document.title,
            newUrl
        );

        return;
    }

    window.history.pushState(
        null,
        document.title,
        newUrl
    );
};


/**
 * ============================================================
 * PAGINATION
 * ============================================================
 */

window.app.createPagination = function (options) {
    var settings = $.extend(
        {
            containerId: "",
            pageSize: 10,
            pageSizeOptions: [
                10,
                25,
                50,
                100
            ],
            useQueryString: true,
            onPageChange: function () {},
            onPageSizeChange: function () {}
        },
        options
    );

    if (settings.useQueryString) {
        settings.pageSize =
            parseInt(
                window.app.getQueryParam(
                    "pageSize",
                    settings.pageSize
                ),
                10
            ) || settings.pageSize;
    }

    var $container = $(
        "#" + settings.containerId
    );

    if ($container.length === 0) {
        console.error(
            "Pagination container not found: " +
            settings.containerId
        );

        return null;
    }

    var pageSizeOptions =
        window.app.createPageSizeOptions(
            settings.pageSizeOptions,
            settings.pageSize
        );

    var html =
        "<div class=\"app-pagination-wrapper\">" +

            "<div class=\"app-pagination-size\">" +
                "<span>แสดง</span>" +

                "<select " +
                    "class=\"" +
                    "form-control " +
                    "form-control-sm " +
                    "js-page-size" +
                    "\"" +
                ">" +
                    pageSizeOptions +
                "</select>" +

                "<span>รายการต่อหน้า</span>" +
            "</div>" +

            "<div " +
                "class=\"" +
                "app-pagination-info " +
                "js-pagination-info" +
                "\"" +
            ">" +
                "0 - 0 จาก 0 รายการ" +
            "</div>" +

            "<nav>" +
                "<ul " +
                    "class=\"" +
                    "pagination " +
                    "pagination-sm " +
                    "mb-0 " +
                    "js-pagination-pages" +
                    "\"" +
                "></ul>" +
            "</nav>" +

        "</div>";

    $container.html(html);

    var $pageSizeSelect =
        $container.find(".js-page-size");

    $pageSizeSelect.on(
        "change",
        function () {
            var pageSize =
                parseInt($(this).val(), 10) || 10;

            settings.pageSize = pageSize;

            if (settings.useQueryString) {
                window.app.setQueryParams({
                    page: 1,
                    pageSize: pageSize
                });
            }

            settings.onPageSizeChange(pageSize);
        }
    );

    return {
        render: function (pagination) {
            window.app.renderAppPagination(
                $container,
                pagination,
                function (page) {
                    if (settings.useQueryString) {
                        window.app.setQueryParams({
                            page: page,
                            pageSize:
                                settings.pageSize
                        });
                    }

                    settings.onPageChange(page);
                }
            );
        },

        reset: function () {
            $container
                .find(".js-pagination-info")
                .text("0 - 0 จาก 0 รายการ");

            $container
                .find(".js-pagination-pages")
                .empty();
        },

        getPage: function () {
            return (
                parseInt(
                    window.app.getQueryParam(
                        "page",
                        1
                    ),
                    10
                ) || 1
            );
        },

        getPageSize: function () {
            return (
                parseInt(
                    $pageSizeSelect.val(),
                    10
                ) || settings.pageSize
            );
        }
    };
};


window.app.createPageSizeOptions = function (
    pageSizeOptions,
    currentPageSize
) {
    var html = "";

    $.each(
        pageSizeOptions,
        function (_, size) {
            var selected =
                size === currentPageSize
                    ? " selected"
                    : "";

            html +=
                "<option " +
                    "value=\"" + size + "\"" +
                    selected +
                ">" +
                    size +
                "</option>";
        }
    );

    return html;
};


window.app.renderAppPagination = function (
    $container,
    pagination,
    onPageChange
) {
    var $pages =
        $container.find(
            ".js-pagination-pages"
        );

    var $info =
        $container.find(
            ".js-pagination-info"
        );

    $pages.empty();

    if (
        !pagination ||
        parseInt(
            pagination.totalRows,
            10
        ) === 0
    ) {
        $info.text(
            "0 - 0 จาก 0 รายการ"
        );

        return;
    }

    var currentPage =
        parseInt(pagination.page, 10);

    var totalPages =
        parseInt(pagination.totalPages, 10);

    var totalRows =
        parseInt(pagination.totalRows, 10);

    var from =
        parseInt(pagination.from, 10);

    var to =
        parseInt(pagination.to, 10);

    $info.text(
        window.app.formatNumber(from) +
        " - " +
        window.app.formatNumber(to) +
        " จาก " +
        window.app.formatNumber(totalRows) +
        " รายการ"
    );

    window.app.appendPaginationButton(
        $pages,
        "ก่อนหน้า",
        currentPage - 1,
        !pagination.hasPrevious,
        false,
        onPageChange
    );

    var pageRange =
        window.app.getPaginationRange(
            currentPage,
            totalPages
        );

    $.each(
        pageRange,
        function (_, page) {
            if (page === "...") {
                $pages.append(
                    "<li " +
                        "class=\"" +
                        "page-item disabled" +
                        "\"" +
                    ">" +
                        "<span " +
                            "class=\"page-link\"" +
                        ">" +
                            "..." +
                        "</span>" +
                    "</li>"
                );

                return;
            }

            window.app.appendPaginationButton(
                $pages,
                page,
                page,
                false,
                page === currentPage,
                onPageChange
            );
        }
    );

    window.app.appendPaginationButton(
        $pages,
        "ถัดไป",
        currentPage + 1,
        !pagination.hasNext,
        false,
        onPageChange
    );
};


window.app.appendPaginationButton = function (
    $pages,
    text,
    page,
    disabled,
    active,
    onPageChange
) {
    var className = "page-item";

    if (disabled) {
        className += " disabled";
    }

    if (active) {
        className += " active";
    }

    var $item = $(
        "<li class=\"" + className + "\">" +
            "<button " +
                "type=\"button\" " +
                "class=\"page-link\"" +
            ">" +
                text +
            "</button>" +
        "</li>"
    );

    if (!disabled && !active) {
        $item
            .find("button")
            .on("click", function () {
                onPageChange(page);
            });
    }

    $pages.append($item);
};


window.app.getPaginationRange = function (
    currentPage,
    totalPages
) {
    var pages = [];

    if (totalPages <= 7) {
        for (
            var page = 1;
            page <= totalPages;
            page++
        ) {
            pages.push(page);
        }

        return pages;
    }

    pages.push(1);

    var startPage = Math.max(
        2,
        currentPage - 1
    );

    var endPage = Math.min(
        totalPages - 1,
        currentPage + 1
    );

    if (currentPage <= 3) {
        startPage = 2;
        endPage = 4;
    }

    if (
        currentPage >=
        totalPages - 2
    ) {
        startPage = totalPages - 3;
        endPage = totalPages - 1;
    }

    if (startPage > 2) {
        pages.push("...");
    }

    for (
        var pageNumber = startPage;
        pageNumber <= endPage;
        pageNumber++
    ) {
        pages.push(pageNumber);
    }

    if (
        endPage <
        totalPages - 1
    ) {
        pages.push("...");
    }

    pages.push(totalPages);

    return pages;
};


window.app.createAirDatepicker = function (
    selector,
    options
) {
    if (
        typeof AirDatepicker !== "function" ||
        $(selector).length === 0
    ) {
        return null;
    }

    var settings = $.extend(
        {
            locale: AirDatepickerLocaleTh,
            classes: "datepicker-lg",
            dateFormat: "yyyy-MM-dd",
            autoClose: true,

            position: function ({
                $datepicker,
                $target,
                $pointer,
                isViewChange,
                done
            }) {
                var popper = window.createPopper(
                    $target,
                    $datepicker,
                    {
                        placement: "bottom",

                        onFirstUpdate: function () {
                            if (
                                !isViewChange &&
                                window.appAnimation
                            ) {
                                window.appAnimation
                                    .showDatepicker(
                                        $datepicker
                                    );
                            }
                        },

                        modifiers: [
                            {
                                name: "offset",
                                options: {
                                    offset: [0, 10]
                                }
                            },
                            {
                                name: "arrow",
                                options: {
                                    element: $pointer
                                }
                            },
                            {
                                name: "computeStyles",
                                options: {
                                    gpuAcceleration: false
                                }
                            }
                        ]
                    }
                );

                return function () {
                    if (
                        window.appAnimation &&
                        typeof window.appAnimation
                            .hideDatepicker === "function"
                    ) {
                        window.appAnimation.hideDatepicker(
                            $datepicker,
                            function () {
                                popper.destroy();
                                done();
                            }
                        );

                        return;
                    }

                    popper.destroy();
                    done();
                };
            }
        },
        options || {}
    );

    return new AirDatepicker(
        selector,
        settings
    );
};

/**
 * ===========================================================
 * STORAGE
 * ===========================================================
 */

window.app.storage = {
    getKey: function (key) {
        var appPath = window.app.getAppRootPath();

        return "app:" + appPath + ":" + key;
    },

    set: function (key, value, expireMinutes) {
        var storageKey = this.getKey(key);
        var expiresAt = null;

        if (expireMinutes) {
            expiresAt =
                Date.now() +
                (expireMinutes * 60 * 1000);
        }

        var data = {
            value: value,
            savedAt: Date.now(),
            expiresAt: expiresAt
        };

        localStorage.setItem(
            storageKey,
            JSON.stringify(data)
        );
    },

    get: function (key) {
        var storageKey = this.getKey(key);
        var storedValue = localStorage.getItem(
            storageKey
        );

        if (!storedValue) {
            return null;
        }

        try {
            var data = JSON.parse(storedValue);

            if (
                data.expiresAt &&
                Date.now() >= data.expiresAt
            ) {
                localStorage.removeItem(storageKey);

                return null;
            }

            return data.value;

        } catch (error) {
            localStorage.removeItem(storageKey);

            return null;
        }
    },

    remove: function (key) {
        localStorage.removeItem(
            this.getKey(key)
        );
    }
};

/**
 * ============================================================
 * PAGE INITIALIZATION
 * ============================================================
 */

$(function () {
    window.app.initializeSelect2();
    window.app.initializeSidebar();
});


window.app.initializeSelect2 = function () {
    var $select2 = $(".select2");

    if (
        $select2.length === 0 ||
        typeof $.fn.select2 !== "function"
    ) {
        return;
    }

    $select2.select2({
        theme: "bootstrap4",
        width: "100%"
    });
};

window.app.initializeSidebar = async function () {
    var mobileQuery = window.matchMedia(
        "(max-width: 991.98px)"
    );

    var isMobileLayout = function () {
        return mobileQuery.matches;
    };

    var setExpandedState = function () {
        var expanded = false;

        if (isMobileLayout()) {
            expanded = $("body").hasClass(
                "sidebar-open"
            );
        } else {
            expanded = !$("body").hasClass(
                "sidebar-collapsed"
            );
        }

        $(".sidebar-toggle").attr(
            "aria-expanded",
            expanded ? "true" : "false"
        );
    };

    var setSubmenuState = function ($menuItem, isOpen) {
        $menuItem.toggleClass("open", isOpen);
        $menuItem.find(".sidebar-submenu-toggle").attr(
            "aria-expanded",
            isOpen ? "true" : "false"
        );
    };

    $(".sidebar-submenu-item").each(function () {
        var $menuItem = $(this);
        setSubmenuState(
            $menuItem,
            $menuItem.hasClass("open")
        );
    });

    var sidebarState =
        localStorage.getItem(
            "sidebarCollapsed"
        );

    if (
        sidebarState === "1" &&
        !isMobileLayout()
    ) {
        $("body").addClass(
            "sidebar-collapsed"
        );
    }

    setExpandedState();

    $(".sidebar-toggle").on(
        "click",
        function () {
            var $body = $("body");

            if (isMobileLayout()) {
                $body.toggleClass(
                    "sidebar-open"
                );
                setExpandedState();
                return;
            }

            $body.toggleClass(
                "sidebar-collapsed"
            );

            var isCollapsed =
                $body.hasClass(
                    "sidebar-collapsed"
                );

            localStorage.setItem(
                "sidebarCollapsed",
                isCollapsed ? "1" : "0"
            );

            setExpandedState();
        }
    );

    $(".sidebar-overlay").on(
        "click",
        function () {
            $("body").removeClass(
                "sidebar-open"
            );

            setExpandedState();
        }
    );

    $(document).on(
        "click",
        ".sidebar .sidebar-submenu-toggle",
        function (event) {
            event.preventDefault();
            
            var $button = $(this);  
            var caret = $button.find(".sidebar-submenu-caret")[0];

            var $body = $("body");
            var $menuItem = $(this).closest(
                ".sidebar-submenu-item"
            );
            var submenu = $menuItem.children(".sidebar-submenu")[0];
            var isOpening = !$menuItem.hasClass("open");

            if (
                !isMobileLayout() &&
                $body.hasClass("sidebar-collapsed")
            ) {
                $body.removeClass("sidebar-collapsed");
                localStorage.setItem(
                    "sidebarCollapsed",
                    "0"
                );
                setExpandedState();
            }

            if (isOpening) {
                window.appAnimation.showSubmenuItems(submenu);
                setSubmenuState(
                        $menuItem,
                        isOpening 
                    );
            }
            else{
                window.appAnimation.hideSidebarSubmenu(submenu, function () {
                    setSubmenuState(
                        $menuItem,
                        isOpening 
                    );
                });
            }
        }
    );

    $(document).on(
        "click",
        ".sidebar .nav-link",
        function () {
            if (
                $(this).hasClass(
                    "sidebar-submenu-toggle"
                )
            ) {
                return;
            }

            if (!isMobileLayout()) {
                return;
            }

            $("body").removeClass(
                "sidebar-open"
            );

            setExpandedState();
        }
    );

    $(document).on(
        "keydown",
        function (event) {
            if (
                event.key === "Escape" &&
                $("body").hasClass(
                    "sidebar-open"
                )
            ) {
                $("body").removeClass(
                    "sidebar-open"
                );

                setExpandedState();
            }
        }
    );

    var onResize = function () {
        if (!isMobileLayout()) {
            var desktopSidebarState =
                localStorage.getItem(
                    "sidebarCollapsed"
                );

            $("body").removeClass(
                "sidebar-open"
            );

            if (desktopSidebarState === "1") {
                $("body").addClass(
                    "sidebar-collapsed"
                );
            } else {
                $("body").removeClass(
                    "sidebar-collapsed"
                );
            }
        } else {
            $("body").removeClass(
                "sidebar-collapsed"
            );
        }

        setExpandedState();
    };

    if (typeof mobileQuery.addEventListener === "function") {
        mobileQuery.addEventListener(
            "change",
            onResize
        );
    } else if (
        typeof mobileQuery.addListener === "function"
    ) {
        mobileQuery.addListener(onResize);
    }

    await window.app.loadSidebarMenu();

    window.appAnimation.sidebarMenuAnimation();
};


window.app.getSidebarMenuApiUrl = function () {
    return window.app.apiUrl(
        "api/menu/getMenu.php"
    );
};


window.app.getAppRootPath = function () {
    var baseUrl = "./";

    if (
        window.APP_CONFIG &&
        window.APP_CONFIG.baseUrl
    ) {
        baseUrl = window.APP_CONFIG.baseUrl;
    }

    try {
        var rootPath = new URL(
            baseUrl || "./",
            window.location.href
        ).pathname;

        if (rootPath === "") {
            return "/";
        }

        if (rootPath.slice(-1) !== "/") {
            rootPath += "/";
        }

        return rootPath;
    } catch (error) {
        return "/";
    }
};


window.app.getCurrentAppRelativePath = function () {
    var rootPath = window.app.getAppRootPath();
    var currentPath = window.location.pathname || "/";

    if (currentPath.indexOf(rootPath) === 0) {
        return currentPath
            .substring(rootPath.length)
            .replace(/^\/+/, "");
    }

    return currentPath.replace(/^\/+/, "");
};


window.app.isSidebarItemActive = function (item, currentPath) {
    if (!item || typeof item !== "object") {
        return false;
    }

    var normalizedCurrentPath = String(currentPath || "").replace(/^\/+/, "");
    var href = String(item.href || "").replace(/^\/+/, "");

    if (href && href === normalizedCurrentPath) {
        return true;
    }

    if ($.isArray(item.activePaths)) {
        for (var index = 0; index < item.activePaths.length; index++) {
            var activePath = String(item.activePaths[index] || "")
                .replace(/^\/+/, "");

            if (activePath && activePath === normalizedCurrentPath) {
                return true;
            }
        }
    }

    return false;
};


window.app.escapeHtml = function (value) {
    return $("<div>").text(
        value === null || value === undefined
            ? ""
            : String(value)
    ).html();
};


window.app.sidebarItemHasActiveChild = function (
    children,
    currentPath
) {
    if (!$.isArray(children)) {
        return false;
    }

    for (var index = 0; index < children.length; index++) {
        var child = children[index];

        if (!child || typeof child !== "object") {
            continue;
        }

        if (child.type === "submenu") {
            if (
                window.app.sidebarItemHasActiveChild(
                    child.children,
                    currentPath
                )
            ) {
                return true;
            }

            continue;
        }

        if (window.app.isSidebarItemActive(child, currentPath)) {
            return true;
        }
    }

    return false;
};


window.app.renderSidebarMenuItem = function (
    item,
    currentPath
) {
    if (!item || typeof item !== "object") {
        return "";
    }

    var type = item.type || "link";
    var label = window.app.escapeHtml(item.label);
    var icon = item.icon ? window.app.escapeHtml(item.icon) : "";

    if (type === "section") {
        var sectionHtml = "<div class=\"sidebar-heading text-uppercase px-2 mt-3 mb-2\">" +
            label +
            "</div>";

        if ($.isArray(item.children)) {
            for (var sectionIndex = 0; sectionIndex < item.children.length; sectionIndex++) {
                sectionHtml += window.app.renderSidebarMenuItem(
                    item.children[sectionIndex],
                    currentPath
                );
            }
        }

        return sectionHtml;
    }

    if (type === "submenu") {
        var children = $.isArray(item.children) ? item.children : [];
        var isOpen = window.app.sidebarItemHasActiveChild(
            children,
            currentPath
        );
        var submenuId = "submenu-" + String(item.label || "submenu")
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");

        var submenuHtml = "<div class=\"sidebar-submenu-item" +
            (isOpen ? " open" : "") +
            "\">" +
            "<button type=\"button\" class=\"nav-link d-flex align-items-center sidebar-submenu-toggle\" aria-expanded=\"" +
            (isOpen ? "true" : "false") +
            "\" aria-controls=\"" +
            submenuId +
            "\">" +
            "<span class=\"d-flex align-items-center\">" +
            (icon ? "<i class=\"" + icon + " mr-3\"></i>" : "") +
            "<span>" +
            label +
            "</span>" +
            "</span>" +
            "<i class=\"fas fa-chevron-down sidebar-submenu-caret\"></i>" +
            "</button>" +
            "<div id=\"" +
            submenuId +
            "\" class=\"sidebar-submenu\">";

        for (var submenuIndex = 0; submenuIndex < children.length; submenuIndex++) {
            var child = children[submenuIndex];

            if (!child || typeof child !== "object") {
                continue;
            }

            var childLabel = window.app.escapeHtml(child.label);
            var childHref = String(child.href || "#").replace(/^\/+/, "");
            var childActive = window.app.isSidebarItemActive(
                child,
                currentPath
            );
            var childUrl = window.app.apiUrl(childHref);

            submenuHtml += "<a href=\"" +
                window.app.escapeHtml(childUrl) +
                "\" class=\"sidebar-submenu-link d-flex align-items-center" +
                (childActive ? " active" : "") +
                "\">" +
                "<span class=\"d-flex align-items-center\">" +
                    "<i class=\"fas fa-angle-right mr-3\"></i>"+
                "</span>" +
                "<span>" +
                    childLabel +
                "</span>" +
                "</a>";
        }

        submenuHtml += "</div></div>";

        return submenuHtml;
    }

    var href = String(item.href || "#").replace(/^\/+/, "");
    var isActive = window.app.isSidebarItemActive(item, currentPath);
    var url = window.app.apiUrl(href);

    return "<a href=\"" +
        window.app.escapeHtml(url) +
        "\" class=\"nav-link d-flex align-items-center" +
        (isActive ? " active" : "") +
        "\">" +
        (icon ? "<i class=\"" + icon + " mr-3\"></i>" : "") +
        "<span>" +
        label +
        "</span>" +
        "</a>";
};


window.app.renderSidebarMenu = function (menuItems) {
    var $menu = $(".sidebar-nav[data-sidebar-menu='true']");

    if ($menu.length === 0) {
        $menu = $(".sidebar-nav").first();
    }

    if ($menu.length === 0) {
        return;
    }

    var currentPath = window.app.getCurrentAppRelativePath();
    var html = "";

    if ($.isArray(menuItems)) {
        for (var index = 0; index < menuItems.length; index++) {
            html += window.app.renderSidebarMenuItem(
                menuItems[index],
                currentPath
            );
        }
    }

    $menu.html(html);
};


window.app.loadSidebarMenu = async function () {
    var $menu = $(".sidebar-nav[data-sidebar-menu='true']");

    if ($menu.length === 0) {
        return;
    }

    var menuApiUrl = window.app.getSidebarMenuApiUrl();

    var cacheKey = window.APP_CONFIG.SIDEBAR_MENU_CACHE_KEY;
    var cacheDuration = 10 * 60 * 1000; // 10 นาที
    var currentTime = Date.now();
    var cachedData = null;

    /*
     * อ่านเมนูจาก localStorage
     */
    try {
        var cachedValue = window.app.storage.get(cacheKey);

        if (cachedValue) {
            cachedData = JSON.parse(cachedValue);
        }
    } catch (error) {
        window.app.storage.remove(cacheKey);
    }
    cachedData = null; // ลบ cache ชั่วคราวเพื่อทดสอบการโหลดเมนูจาก API
    /*
     * ใช้ cache หากยังไม่เกิน 10 นาที
     */
    if (
        cachedData &&
        $.isArray(cachedData.menu) &&
        typeof cachedData.savedAt === "number" &&
        currentTime - cachedData.savedAt < cacheDuration
    ) {
        window.app.renderSidebarMenu(
            cachedData.menu
        );

        return;
    }

    /*
     * Cache ไม่มีหรือหมดอายุ ให้โหลดจาก API
     */
    try {
        var response = await $.ajax({
            url: menuApiUrl,
            method: "GET",
            dataType: "json",
        });

        var menuItems = [];

        if (
            response &&
            response.success &&
            response.data &&
            $.isArray(response.data.menu)
        ) {
            menuItems = response.data.menu;
        }

        /*
         * บันทึกเมนูและเวลาลง localStorage
         */
        window.app.storage.set(
            cacheKey,
            JSON.stringify({
                menu: menuItems,
                savedAt: Date.now()
            }),
            10 // expire in 10 minutes
        );

        window.app.renderSidebarMenu(menuItems);

    } catch (error) {
        /*
         * หาก API มีปัญหา แต่มี cache เก่า ให้ใช้ cache เก่าไปก่อน
         */
        if (
            cachedData &&
            $.isArray(cachedData.menu)
        ) {
            window.app.renderSidebarMenu(
                cachedData.menu
            );

            return;
        }

        $menu.html(
            '<div class="text-center text-muted py-3">' +
            'โหลดเมนูไม่สำเร็จ' +
            '</div>'
        );
    }
};
