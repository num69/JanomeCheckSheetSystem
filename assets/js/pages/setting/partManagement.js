var partPagination;
var partFormMode = "edit";

$(async function () {
    $("#editPartModal").appendTo("body");

    if (window.appAnimation) {
        window.appAnimation.showSummaryCards();
    }

    syncFiltersFromQueryString();

    partPagination = window.app.createPagination({
        containerId: "partPagination",
        pageSize: parseInt(window.app.getQueryParam("pageSize", 10), 10) || 10,
        pageSizeOptions: [10, 25, 50, 100],
        onPageChange: async function (page) {
            await loadParts(page);
        },
        onPageSizeChange: async function () {
            await loadParts(1);
        }
    });

    $("#btnSearchPart").on("click", async function () {
        await loadParts(1);
    });

    $("#btnResetPart").on("click", async function () {
        resetFilters();
        $(".js-page-size").val("10");
        partPagination.reset();

        window.app.setQueryParams({
            keyword: "",
            page: 1,
            pageSize: 10
        }, true);

        await loadParts(1);
    });

    $("#btnAddPart").on("click", function () {
        openCreateModal();
    });

    $(document).on("click", ".js-edit-part", async function () {
        var partId = parseInt($(this).data("partId"), 10) || 0;

        if (partId <= 0) {
            await window.app.showWarning("ไม่พบข้อมูลชิ้นส่วนที่เลือก");
            return;
        }

        await openEditModal(partId);
    });

    $(document).on("click", ".js-delete-part", async function () {
        var partId = parseInt($(this).data("partId"), 10) || 0;

        if (partId <= 0) {
            await window.app.showWarning("ไม่พบข้อมูลชิ้นส่วนที่เลือก");
            return;
        }

        await deletePart(partId);
    });

    $("#editPartForm").on("submit", async function (event) {
        event.preventDefault();
        await savePartForm(event.currentTarget);
    });

    $("#partKeyword").on("keypress", function (event) {
        if (event.which === 13) {
            event.preventDefault();
            $("#btnSearchPart").click();
        }
    });

    await loadParts(parseInt(window.app.getQueryParam("page", 1), 10) || 1);
});

function syncFiltersFromQueryString() {
    $("#partKeyword").val(window.app.getQueryParam("keyword", ""));
}

function resetFilters() {
    $("#partKeyword").val("");
}

function getFilters() {
    return {
        keyword: $.trim($("#partKeyword").val() || "")
    };
}

async function loadParts(page) {
    page = parseInt(page, 10) || 1;

    var filters = getFilters();
    var pageSize = partPagination.getPageSize();

    window.app.setQueryParams({
        keyword: filters.keyword,
        page: page,
        pageSize: pageSize
    }, true);

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/parts/getParts.php"),
            type: "GET",
            dataType: "json",
            data: {
                keyword: filters.keyword,
                page: page,
                pageSize: pageSize
            }
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลชิ้นส่วนได้");
            return;
        }

        renderPartTable(response.data.items || []);
        partPagination.render(response.data.pagination || {});

        if (window.appAnimation) {
            window.appAnimation.showTableRows();
        }
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการเชื่อมต่อ API");
    }
}

function renderPartTable(rows) {
    var html = "";

    if (!rows || rows.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>';
    } else {
        $.each(rows, function (_, row) {
            html += "<tr>"
                + "<td>" + escapeHtml(row.PartCode) + "</td>"
                + "<td>" + escapeHtml(row.PartName) + "</td>"
                + "<td>" + escapeHtml(row.CreatedAtText || row.CreatedAt || "-") + "</td>"
                + "<td class=\"text-center\">"
                + "<button type=\"button\" class=\"btn btn-sm btn-outline-secondary entity-action-btn js-edit-part mr-1\" data-part-id=\"" + escapeHtml(row.PartId) + "\" title=\"แก้ไข\">"
                + "<i class=\"fas fa-edit\"></i>"
                + "</button>"
                + "<button type=\"button\" class=\"btn btn-sm btn-outline-danger entity-action-btn js-delete-part\" data-part-id=\"" + escapeHtml(row.PartId) + "\" title=\"ลบ\">"
                + "<i class=\"fas fa-trash\"></i>"
                + "</button>"
                + "</td>"
                + "</tr>";
        });
    }

    $("#partTableBody").html(html);
}

function escapeHtml(value) {
    return $("<div>")
        .text(value === null || value === undefined ? "" : value)
        .html();
}

async function openEditModal(partId) {
    partFormMode = "edit";
    clearEditForm();
    setModalMode("edit");

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/parts/getPartById.php"),
            type: "GET",
            dataType: "json",
            data: {
                partId: partId
            }
        });

        if (!response.success || !response.data) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลชิ้นส่วนได้");
            return;
        }

        fillEditForm(response.data);
        $("#editPartModal").modal("show");
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการดึงข้อมูลชิ้นส่วน");
    }
}

function fillEditForm(part) {
    $("#editPartId").val(part.PartId || "");
    $("#editPartCode").val(part.PartCode || "");
    $("#editPartName").val(part.PartName || "");
}

function clearEditForm() {
    $("#editPartForm")[0].reset();
    $("#editPartId").val("");
}

function openCreateModal() {
    partFormMode = "create";
    clearEditForm();
    setModalMode("create");
    $("#editPartModal").modal("show");
}

function setModalMode(mode) {
    var isCreateMode = mode === "create";

    $("#editPartModalLabel").html(
        "<i class=\"fas "
        + (isCreateMode ? "fa-plus-circle" : "fa-edit")
        + " mr-2\"></i>"
        + (isCreateMode ? "เพิ่มชิ้นส่วน" : "แก้ไขชิ้นส่วน")
    );

    $("#btnSavePart").html(
        "<i class=\"fas fa-save mr-1\"></i>"
        + (isCreateMode ? "บันทึกชิ้นส่วนใหม่" : "บันทึกการแก้ไข")
    );
}

async function savePartForm(formElement) {
    var saveButton = $("#btnSavePart");
    saveButton.prop("disabled", true);

    try {
        var formData = new FormData(formElement);
        var isCreateMode = partFormMode === "create";

        if (!isCreateMode) {
            var partId = $.trim(String(formData.get("partId") || ""));
            if (!partId) {
                await window.app.showWarning("ไม่พบรหัสชิ้นส่วนที่ต้องการบันทึก");
                return;
            }
        }

        var apiPath = isCreateMode
            ? "api/parts/createPart.php"
            : "api/parts/updatePart.php";

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
                ? "เพิ่มชิ้นส่วนเรียบร้อยแล้ว"
                : "บันทึกข้อมูลเรียบร้อยแล้ว"
        );

        $("#editPartModal").modal("hide");

        var currentPage = 1;
        if (!isCreateMode) {
            currentPage = partPagination ? partPagination.getPage() : 1;
        }

        await loadParts(currentPage);
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    } finally {
        saveButton.prop("disabled", false);
    }
}

async function deletePart(partId) {
    var isConfirmed = window.confirm("ยืนยันการลบข้อมูลชิ้นส่วนนี้ใช่หรือไม่?");
    if (!isConfirmed) {
        return;
    }

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/parts/deletePart.php"),
            type: "POST",
            dataType: "json",
            data: {
                partId: partId
            }
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถลบข้อมูลชิ้นส่วนได้");
            return;
        }

        await window.app.showSuccess("ลบชิ้นส่วนเรียบร้อยแล้ว");

        var currentPage = partPagination ? partPagination.getPage() : 1;
        await loadParts(currentPage);
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการลบข้อมูล");
    }
}
