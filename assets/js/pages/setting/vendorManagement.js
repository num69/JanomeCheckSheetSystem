var vendorPagination;
var vendorFormMode = "edit";

$(async function () {
    $("#editVendorModal").appendTo("body");

    if (window.appAnimation) {
        window.appAnimation.showSummaryCards();
    }

    syncFiltersFromQueryString();

    vendorPagination = window.app.createPagination({
        containerId: "vendorPagination",
        pageSize: parseInt(window.app.getQueryParam("pageSize", 10), 10) || 10,
        pageSizeOptions: [10, 25, 50, 100],
        onPageChange: async function (page) {
            await loadVendors(page);
        },
        onPageSizeChange: async function () {
            await loadVendors(1);
        }
    });

    $("#btnSearchVendor").on("click", async function () {
        await loadVendors(1);
    });

    $("#btnResetVendor").on("click", async function () {
        resetFilters();
        $(".js-page-size").val("10");
        vendorPagination.reset();

        window.app.setQueryParams({
            keyword: "",
            page: 1,
            pageSize: 10
        }, true);

        await loadVendors(1);
    });

    $("#btnAddVendor").on("click", function () {
        openCreateModal();
    });

    $(document).on("click", ".js-edit-vendor", async function () {
        var vendorId = parseInt($(this).data("vendorId"), 10) || 0;

        if (vendorId <= 0) {
            await window.app.showWarning("ไม่พบข้อมูลผู้ผลิตที่เลือก");
            return;
        }

        await openEditModal(vendorId);
    });

    $(document).on("click", ".js-delete-vendor", async function () {
        var vendorId = parseInt($(this).data("vendorId"), 10) || 0;

        if (vendorId <= 0) {
            await window.app.showWarning("ไม่พบข้อมูลผู้ผลิตที่เลือก");
            return;
        }

        await deleteVendor(vendorId);
    });

    $("#editVendorForm").on("submit", async function (event) {
        event.preventDefault();
        await saveVendorForm(event.currentTarget);
    });

    $("#vendorKeyword").on("keypress", function (event) {
        if (event.which === 13) {
            event.preventDefault();
            $("#btnSearchVendor").click();
        }
    });

    await loadVendors(parseInt(window.app.getQueryParam("page", 1), 10) || 1);
});

function syncFiltersFromQueryString() {
    $("#vendorKeyword").val(window.app.getQueryParam("keyword", ""));
}

function resetFilters() {
    $("#vendorKeyword").val("");
}

function getFilters() {
    return {
        keyword: $.trim($("#vendorKeyword").val() || "")
    };
}

async function loadVendors(page) {
    page = parseInt(page, 10) || 1;

    var filters = getFilters();
    var pageSize = vendorPagination.getPageSize();

    window.app.setQueryParams({
        keyword: filters.keyword,
        page: page,
        pageSize: pageSize
    }, true);

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/vendors/getVendors.php"),
            type: "GET",
            dataType: "json",
            data: {
                keyword: filters.keyword,
                page: page,
                pageSize: pageSize
            }
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลผู้ผลิตได้");
            return;
        }

        renderVendorTable(response.data.items || []);
        vendorPagination.render(response.data.pagination || {});

        if (window.appAnimation) {
            window.appAnimation.showTableRows();
        }
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการเชื่อมต่อ API");
    }
}

function renderVendorTable(rows) {
    var html = "";

    if (!rows || rows.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>';
    } else {
        $.each(rows, function (_, row) {
            html += "<tr>"
                + "<td>" + escapeHtml(row.VendorCode) + "</td>"
                + "<td>" + escapeHtml(row.VendorName) + "</td>"
                + "<td>" + escapeHtml(row.CreatedAtText || row.CreatedAt || "-") + "</td>"
                + "<td class=\"text-center\">"
                + "<button type=\"button\" class=\"btn btn-sm btn-outline-secondary entity-action-btn js-edit-vendor mr-1\" data-vendor-id=\"" + escapeHtml(row.VendorId) + "\" title=\"แก้ไข\">"
                + "<i class=\"fas fa-edit\"></i>"
                + "</button>"
                + "<button type=\"button\" class=\"btn btn-sm btn-outline-danger entity-action-btn js-delete-vendor\" data-vendor-id=\"" + escapeHtml(row.VendorId) + "\" title=\"ลบ\">"
                + "<i class=\"fas fa-trash\"></i>"
                + "</button>"
                + "</td>"
                + "</tr>";
        });
    }

    $("#vendorTableBody").html(html);
}

function escapeHtml(value) {
    return $("<div>")
        .text(value === null || value === undefined ? "" : value)
        .html();
}

async function openEditModal(vendorId) {
    vendorFormMode = "edit";
    clearEditForm();
    setModalMode("edit");

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/vendors/getVendorById.php"),
            type: "GET",
            dataType: "json",
            data: {
                vendorId: vendorId
            }
        });

        if (!response.success || !response.data) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลผู้ผลิตได้");
            return;
        }

        fillEditForm(response.data);
        $("#editVendorModal").modal("show");
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ผลิต");
    }
}

function fillEditForm(vendor) {
    $("#editVendorId").val(vendor.VendorId || "");
    $("#editVendorCode").val(vendor.VendorCode || "");
    $("#editVendorName").val(vendor.VendorName || "");
}

function clearEditForm() {
    $("#editVendorForm")[0].reset();
    $("#editVendorId").val("");
}

function openCreateModal() {
    vendorFormMode = "create";
    clearEditForm();
    setModalMode("create");
    $("#editVendorModal").modal("show");
}

function setModalMode(mode) {
    var isCreateMode = mode === "create";

    $("#editVendorModalLabel").html(
        "<i class=\"fas "
        + (isCreateMode ? "fa-plus-circle" : "fa-edit")
        + " mr-2\"></i>"
        + (isCreateMode ? "เพิ่มผู้ผลิต" : "แก้ไขผู้ผลิต")
    );

    $("#btnSaveVendor").html(
        "<i class=\"fas fa-save mr-1\"></i>"
        + (isCreateMode ? "บันทึกผู้ผลิตใหม่" : "บันทึกการแก้ไข")
    );
}

async function saveVendorForm(formElement) {
    var saveButton = $("#btnSaveVendor");
    saveButton.prop("disabled", true);

    try {
        var formData = new FormData(formElement);
        var isCreateMode = vendorFormMode === "create";

        if (!isCreateMode) {
            var vendorId = $.trim(String(formData.get("vendorId") || ""));
            if (!vendorId) {
                await window.app.showWarning("ไม่พบรหัสผู้ผลิตที่ต้องการบันทึก");
                return;
            }
        }

        var apiPath = isCreateMode
            ? "api/vendors/createVendor.php"
            : "api/vendors/updateVendor.php";

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
                ? "เพิ่มผู้ผลิตเรียบร้อยแล้ว"
                : "บันทึกข้อมูลเรียบร้อยแล้ว"
        );

        $("#editVendorModal").modal("hide");

        var currentPage = 1;
        if (!isCreateMode) {
            currentPage = vendorPagination ? vendorPagination.getPage() : 1;
        }

        await loadVendors(currentPage);
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    } finally {
        saveButton.prop("disabled", false);
    }
}

async function deleteVendor(vendorId) {
    var isConfirmed = window.confirm("ยืนยันการลบข้อมูลผู้ผลิตนี้ใช่หรือไม่?");
    if (!isConfirmed) {
        return;
    }

    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/vendors/deleteVendor.php"),
            type: "POST",
            dataType: "json",
            data: {
                vendorId: vendorId
            }
        });

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถลบข้อมูลผู้ผลิตได้");
            return;
        }

        await window.app.showSuccess("ลบผู้ผลิตเรียบร้อยแล้ว");

        var currentPage = vendorPagination ? vendorPagination.getPage() : 1;
        await loadVendors(currentPage);
    } catch (xhr) {
        await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการลบข้อมูล");
    }
}
