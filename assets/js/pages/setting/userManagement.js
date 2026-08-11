var userPagination;
var activeEditUserId = 0;
var userFormMode = "edit";

$(async function () {
    $("#editUserModal").appendTo("body");
    bindUploadPreviewEvents();

    if (window.appAnimation) {
        window.appAnimation.showSummaryCards();
    }

    syncFiltersFromQueryString();

    userPagination = window.app.createPagination({
        containerId: "userPagination",
        pageSize: parseInt(window.app.getQueryParam("pageSize", 10), 10) || 10,
        pageSizeOptions: [10, 25, 50, 100],
        onPageChange: async function (page) {
            await loadUsers(page);
        },
        onPageSizeChange: async function () {
            await loadUsers(1);
        }
    });

    $("#btnSearchUser").on("click", async function (event) {
        const btn = event.currentTarget;
        btn.disabled = true;

        try {
            const delay = window.app.delay(500);
            await loadUsers(1);
            await delay;
        } finally {
            btn.disabled = false;
        }
    });

    $("#btnResetUser").on("click", async function (event) {
        const btn = event.currentTarget;
        btn.disabled = true;

        try {
            resetFilters();
            $(".js-page-size").val("10");
            userPagination.reset();

            window.app.setQueryParams({
                keyword: "",
                page: 1,
                pageSize: 10
            }, true);
            const delay = window.app.delay(500);
            await loadUsers(1);
            await delay;
        } finally {
            btn.disabled = false;
        }
    });

    $("#btnAddUser").on("click", function () {
        openCreateModal();
    });

    $(document).on("click", ".js-edit-user", async function () {
        var userId = parseInt($(this).data("userId"), 10) || 0;

        if (userId <= 0) {
            await window.app.showWarning("ไม่พบข้อมูลผู้ใช้งานที่เลือก");
            return;
        }

        await openEditModal(userId);
    });

    $(document).on("click", ".js-delete-user", function () {
        window.app.showWarning("ยังไม่เปิดใช้งานการลบผู้ใช้งาน");
    });

    $("#editUserForm").on("submit", async function (event) {
        event.preventDefault();
        await saveUserForm(event.currentTarget);
    });

    $("#keyword").on("keypress", async function (event) {
        if (event.which === 13) { // Enter key
            event.preventDefault();
            $("#btnSearchUser").click();
        }
    });

    await loadUsers(parseInt(window.app.getQueryParam("page", 1), 10) || 1);
});

function syncFiltersFromQueryString() {
    $("#keyword").val(window.app.getQueryParam("keyword", ""));
}

function resetFilters() {
    $("#keyword").val("");
}

function getFilters() {
    return {
        keyword: $.trim($("#keyword").val() || ""),
    };
}

async function loadUsers(page) {
    if (!userPagination) {
        return;
    }

    page = parseInt(page, 10) || 1;

    var filters = getFilters();
    var pageSize = userPagination.getPageSize();

    window.app.setQueryParams({
        keyword: filters.keyword,
        page: page,
        pageSize: pageSize
    }, true);

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/users/getUsers.php"),
            type: "GET",
            dataType: "json",
            data: {
                keyword: filters.keyword,
                page: page,
                pageSize: pageSize
            }
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลผู้ใช้งานได้");
            return;
        }

        renderUserTable(
            response.data.items || [],
            response.data.pagination || {},
            pageSize
        );

        userPagination.render(response.data.pagination || {});

        if (window.appAnimation) {
            window.appAnimation.showTableRows();
        }
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการเชื่อมต่อ API");
    }
}

function renderUserTable(rows, pagination, pageSize) {
    var html = "";
    var currentPage = parseInt(pagination.page, 10) || 1;
    var currentPageSize = parseInt(pagination.pageSize, 10) || pageSize || 10;

    if (!rows || rows.length === 0) {
        html = "<tr><td colspan=\"5\" class=\"text-center text-muted py-4\">ไม่พบข้อมูล</td></tr>";
    } else {
        $.each(rows, function (index, row) {
            var displayName =  `${row.NameEn || ""}`;
            displayName += row.NameTh ? ` (${row.NameTh})` : "";
            var userGroup = String(row.UserGroup || "").trim();
            var groupLabel = userGroup === "1"
                ? '<span class="badge badge-primary">หัวหน้า</span>'
                : '<span class="badge badge-secondary">พนักงาน</span>';
            
            html += 
                "<tr>"
                    + "<td>" + escapeHtml(row.UserCode) + "</td>"
                    + "<td>" + escapeHtml(displayName) + "</td>"
                    + "<td>" + escapeHtml(row.Position) + "</td>"
                    + "<td>" + groupLabel + "</td>"
                    + "<td class=\"text-center\">"
                        + "<button type=\"button\" class=\"btn btn-sm btn-outline-secondary entity-action-btn js-edit-user mr-1\" data-user-id=\"" + escapeHtml(row.UserId) + "\" title=\"แก้ไข\">"
                            + "<i class=\"fas fa-edit\"></i>"
                        + "</button>"
                        + "<button type=\"button\" class=\"btn btn-sm btn-outline-danger entity-action-btn js-delete-user\" data-user-id=\"" + escapeHtml(row.UserId) + "\" title=\"ลบ\">"
                            + "<i class=\"fas fa-trash\"></i>"
                        + "</button>"
                    + "</td>"
              + "</tr>";
        });
    }

    $("#userTableBody").html(html);
}

function escapeHtml(value) {
    return $("<div>")
        .text(value === null || value === undefined ? "" : value)
        .html();
}

async function openEditModal(userId) {
    userFormMode = "edit";
    activeEditUserId = userId;
    clearEditForm();
    setModalMode("edit");

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/users/getUserById.php"),
            type: "GET",
            dataType: "json",
            data: {
                userId: userId
            }
        });

        if (!response.success || !response.data) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลผู้ใช้งานได้");
            return;
        }

        fillEditForm(response.data);
        $("#editUserModal").modal("show");
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้งาน");
    }
}

function fillEditForm(user) {
    $("#editUserId").val(user.UserId || "");
    $("#editUserCode").val(user.UserCode || "");
    $("#editNameEn").val(user.NameEn || "");
    $("#editNameTh").val(user.NameTh || "");
    $("#editPosition").val(user.Position || "");
    $("#editEmail").val(user.Email || "");
    $("#editUsername").val(user.Username || "");
    $("#editPassword").val("");
    setServerPreview(
        "image",
        user.UserImage || "",
        "#editUserImagePreview",
        "#editUserImagePlaceholder",
        "#editUserImageOverlayText",
        "#currentUserImageText"
    );

    setServerPreview(
        "signature",
        user.UserSignature || "",
        "#editUserSignaturePreview",
        "#editUserSignaturePlaceholder",
        "#editUserSignatureOverlayText",
        "#currentUserSignatureText"
    );

    setSelectValueOrAppend("#editUserGroup", String(user.UserGroup || "").trim());
    setSelectValueOrAppend("#editStatusCode", String(user.StatusCode || "").trim());
}

function clearEditForm() {
    $("#editUserForm")[0].reset();
    $("#editUserId").val("");
    $("#editPassword").val("");

    resetUploadPreview(
        "#editUserImagePreview",
        "#editUserImagePlaceholder",
        "#editUserImageOverlayText",
        "#currentUserImageText",
        "ไฟล์ปัจจุบัน: -",
        "อัปโหลดรูป"
    );

    resetUploadPreview(
        "#editUserSignaturePreview",
        "#editUserSignaturePlaceholder",
        "#editUserSignatureOverlayText",
        "#currentUserSignatureText",
        "ไฟล์ปัจจุบัน: -",
        "อัปโหลดลายเซ็น"
    );
}

function openCreateModal() {
    userFormMode = "create";
    activeEditUserId = 0;
    clearEditForm();
    setModalMode("create");
    $("#editUserModal").modal("show");
}

function setModalMode(mode) {
    var isCreateMode = mode === "create";

    $("#editUserModalLabel").html(
        "<i class=\"fas "
        + (isCreateMode ? "fa-user-plus" : "fa-user-edit")
        + " mr-2\"></i>"
        + (isCreateMode ? "เพิ่มผู้ใช้งาน" : "แก้ไขผู้ใช้งาน")
    );

    $("#btnSaveUser").html(
        "<i class=\"fas fa-save mr-1\"></i>"
        + (isCreateMode ? "บันทึกผู้ใช้งานใหม่" : "บันทึกการแก้ไข")
    );

    $("#editPassword").prop("required", isCreateMode);

    if (isCreateMode) {
        $("#editPassword").attr("placeholder", "ระบุรหัสผ่าน");
        $("#editUserImageOverlayText").text("อัปโหลดรูป");
        $("#editUserSignatureOverlayText").text("อัปโหลดลายเซ็น");
    } else {
        $("#editPassword").attr("placeholder", "เว้นว่างเพื่อไม่เปลี่ยน");
    }
}

function bindUploadPreviewEvents() {
    $("#editUserImage").on("change", function () {
        previewSelectedFile(
            this,
            "#editUserImagePreview",
            "#editUserImagePlaceholder",
            "#editUserImageOverlayText",
            "#currentUserImageText",
            "เปลี่ยนรูป"
        );
    });

    $("#editUserSignature").on("change", function () {
        previewSelectedFile(
            this,
            "#editUserSignaturePreview",
            "#editUserSignaturePlaceholder",
            "#editUserSignatureOverlayText",
            "#currentUserSignatureText",
            "เปลี่ยนลายเซ็น"
        );
    });
}

function setServerPreview(type, filename, imageSelector, placeholderSelector, overlaySelector, infoSelector) {
    if (!filename) {
        $(infoSelector).text("ไฟล์ปัจจุบัน: -");
        return;
    }

    var uploadUrl = window.app.apiUrl("uploads/" + encodeURIComponent(filename));

    $(imageSelector)
        .attr("src", uploadUrl)
        .removeClass("d-none");

    $(placeholderSelector).addClass("d-none");
    $(overlaySelector).text(type === "image" ? "เปลี่ยนรูป" : "เปลี่ยนลายเซ็น");
    $(infoSelector).text("ไฟล์ปัจจุบัน: " + filename);
}

function resetUploadPreview(imageSelector, placeholderSelector, overlaySelector, infoSelector, infoText, overlayText) {
    $(imageSelector)
        .attr("src", "")
        .addClass("d-none");

    $(placeholderSelector).removeClass("d-none");
    $(overlaySelector).text(overlayText);
    $(infoSelector).text(infoText);
}

function previewSelectedFile(inputElement, imageSelector, placeholderSelector, overlaySelector, infoSelector, overlayText) {
    if (!inputElement || !inputElement.files || inputElement.files.length === 0) {
        return;
    }

    var file = inputElement.files[0];
    var reader = new FileReader();

    reader.onload = function (event) {
        $(imageSelector)
            .attr("src", event.target.result)
            .removeClass("d-none");

        $(placeholderSelector).addClass("d-none");
        $(overlaySelector).text(overlayText);
        $(infoSelector).text("ไฟล์ใหม่: " + file.name);
    };

    reader.readAsDataURL(file);
}

function setSelectValueOrAppend(selector, value) {
    if (!value) {
        $(selector).val("");
        return;
    }

    var normalizedValue = String(value).toUpperCase();
    var hasOption = $(selector + " option[value='" + normalizedValue + "']").length > 0;

    if (!hasOption) {
        $(selector).append(
            "<option value=\"" + escapeHtml(normalizedValue) + "\">"
            + escapeHtml(normalizedValue)
            + "</option>"
        );
    }

    $(selector).val(normalizedValue);
}

async function saveUserForm(formElement) {
    var saveButton = $("#btnSaveUser");
    saveButton.prop("disabled", true);

    try {
        var formData = new FormData(formElement);
        var isCreateMode = userFormMode === "create";

        if (!isCreateMode) {
            var userId = $.trim(String(formData.get("userId") || ""));
            if (!userId) {
                await window.app.showWarning("ไม่พบรหัสผู้ใช้งานที่ต้องการบันทึก");
                return;
            }
        }

        var apiPath = isCreateMode
            ? "api/users/createUser.php"
            : "api/users/updateUser.php";

        var response = await $.ajax({
            url: window.app.apiUrl(apiPath),
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถบันทึกข้อมูลได้");
            return;
        }

        await window.app.showSuccess(
            isCreateMode
                ? "เพิ่มผู้ใช้งานเรียบร้อยแล้ว"
                : "บันทึกข้อมูลเรียบร้อยแล้ว"
        );
        $("#editUserModal").modal("hide");

        var currentPage = 1;
        if (!isCreateMode) {
            currentPage = userPagination ? userPagination.getPage() : 1;
        }

        await loadUsers(currentPage);
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    } finally {
        saveButton.prop("disabled", false);
    }
}