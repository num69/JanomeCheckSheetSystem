var reportPagination;
var defaultStartDate;
var defaultEndDate;
var startDatePicker;
var endDatePicker;
$(async function () {
    if (window.appAnimation) {
        window.appAnimation.showSummaryCards();
    }

    loadDefaultDateRange();

    initStockDatePicker();

    loadFilterFromQueryString(); // ไม่ส่งค่า setDefault หมายถึงใช้ค่าจาก URL

    var currentPage = parseInt(
        window.app.getQueryParam("page", 1),
        10
    ) || 1;

    var currentPageSize = parseInt(
        window.app.getQueryParam("pageSize", 10),
        10
    ) || 10;

    reportPagination = window.app.createPagination({
        containerId: "reportPagination",
        pageSize: currentPageSize,
        pageSizeOptions: [1, 10, 25, 50, 100],

        onPageChange: async (page) => {
            await loadStockInReport(page);
        },

        onPageSizeChange: async () => {
            await loadStockInReport(1);
        }
    });

    $("#btnReset").on("click", async function (event) {
        event.currentTarget.disabled = true;
        let delay = window.app.delay(350);

        loadFilterFromQueryString(true);

        reportPagination.reset();

        window.app.setQueryParams({
            startDate: $("#startDate").val(),
            endDate: $("#endDate").val(),
            page: 1,
            pageSize: 10
        }, true);

        await loadStockInReport(1);
        await loadSummaryReport();

        await delay;

        event.currentTarget.disabled = false;
    });

    $("#btnSearch").on("click", async (event) => {
        let delay = window.app.delay(350);
        event.currentTarget.disabled = true;
        await loadStockInReport(1);
        await loadSummaryReport();
        await delay;
        event.currentTarget.disabled = false;
    });

    await loadStockInReport(currentPage);
    await loadSummaryReport();
});

function formatDate(date) {
    var year = date.getFullYear();
    var month = String(date.getMonth() + 1).padStart(2, "0");
    var day = String(date.getDate()).padStart(2, "0");
    return year + "-" + month + "-" + day;
}

function loadDefaultDateRange() {
    var today = new Date();
    var firstDay = new Date(
        today.getFullYear(),
        today.getMonth(),
        1
    );

    defaultStartDate = formatDate(firstDay);
    defaultEndDate = formatDate(today);
}

function initStockDatePicker() {
    startDatePicker = window.app.createAirDatepicker("#startDate", {
        locale: window.AirDatepickerLocaleTh,
        // view: "months",
        // minView: "months",
        dateFormat: "dd/MM/yyyy",
        autoClose: true,
        selectedDates: [new Date(defaultStartDate)],
        maxDate: new Date(defaultEndDate),
    });


    endDatePicker = window.app.createAirDatepicker("#endDate", {
        locale: window.AirDatepickerLocaleTh,
        dateFormat: "dd/MM/yyyy",
        autoClose: true,
        selectedDates: [new Date(defaultEndDate)],
        minDate: new Date(defaultStartDate),
        maxDate: new Date(defaultEndDate),
    });
}

function loadFilterFromQueryString(setDefault = false) {

    // อ่านค่าจาก URL ถ้าไม่มีให้ใช้ค่าเริ่มต้น
    var startDate = window.app.getQueryParam(
        "startDate",
        defaultStartDate
    );

    var endDate = window.app.getQueryParam(
        "endDate",
        defaultEndDate
    );

    if (setDefault) {
        startDatePicker.update({
            selectedDates: [new Date(defaultStartDate)],
            maxDate: new Date(defaultEndDate)
        });

        endDatePicker.update({
            selectedDates: [new Date(defaultEndDate)],
            minDate: new Date(defaultStartDate),
            maxDate: new Date(defaultEndDate)
        });
    } else {
        startDatePicker.update({
            selectedDates: [new Date(startDate)],
            maxDate: new Date(endDate)
        });

        endDatePicker.update({
            selectedDates: [new Date(endDate)],
            minDate: new Date(startDate),
            maxDate: new Date(defaultEndDate)
        });
    }
}

async function loadStockInReport(page) {
    page = parseInt(page, 10) || 1;
    var pageSize = reportPagination.getPageSize();

    if (!startDatePicker.selectedDates[0] || !endDatePicker.selectedDates[0]) {
        await window.app.showWarning("กรุณาเลือกวันที่เริ่มต้นและวันที่สิ้นสุด");
        return;
    }

    window.app.setQueryParams({
        startDate: formatDate(new Date(startDatePicker.selectedDates[0])),
        endDate: formatDate(new Date(endDatePicker.selectedDates[0])),
        page: page,
        pageSize: pageSize
    }, true);

    try {
        // showScLoading();
        // const delay = Delay(350);

        var response = await $.ajax({
            url: window.app.apiUrl("api/warehouse/getStockFgHistory.php"),
            type: "GET",
            dataType: "json",
            data: {
                startDate: formatDate(new Date(startDatePicker.selectedDates[0])),
                endDate: formatDate(new Date(endDatePicker.selectedDates[0])),
                page: page,
                pageSize: pageSize
            }
        });

        // await delay;

        if (!response.success) {
            await window.app.showError(response.message || "ไม่สามารถดึงข้อมูลได้");
            return;
        }

        renderStockTable(response.data.items);

        reportPagination.render(
            response.data.pagination
        );

        if (window.appAnimation) {
            appAnimation.showTableRows();
        }

    } catch (xhr) {
        var message = "เกิดข้อผิดพลาดในการเชื่อมต่อ API";

        if (
            xhr.responseJSON &&
            xhr.responseJSON.message
        ) {
            message = xhr.responseJSON.message;
        }

        await window.app.showError(message);
    } finally {
        // hideScLoading();
    }
}

async function loadSummaryReport() {
    try {
        var response = await $.ajax({
            url: window.app.apiUrl("api/warehouse/getStockFgSummary.php"),
            type: "GET",
            dataType: "json",
            data: {
                startDate: formatDate(new Date(startDatePicker.selectedDates[0])),
                endDate: formatDate(new Date(endDatePicker.selectedDates[0]))
            }
        });

        if (!response.success) {
            throw new Error(response.message || "ไม่สามารถดึงข้อมูลได้");
        }

        if (window.appAnimation) {
            appAnimation.animateNumber(
                "#totalItems",
                response.data.totalItems
            );

            appAnimation.animateNumber(
                "#totalStockIn",
                response.data.totalStockIn
            );

            appAnimation.animateNumber(
                "#totalStockOut",
                response.data.totalStockOut
            );

            appAnimation.animateNumber(
                "#totalBalance",
                response.data.totalBalance
            );
        } else {
            $("#totalItems").text(window.app.formatNumber(response.data.totalItems));
            $("#totalStockIn").text(window.app.formatNumber(response.data.totalStockIn));
            $("#totalStockOut").text(window.app.formatNumber(response.data.totalStockOut));
            $("#totalBalance").text(window.app.formatNumber(response.data.totalBalance));
        }

    } catch (xhr) {
        var message = "เกิดข้อผิดพลาดในการเชื่อมต่อ API";
        if (
            xhr.responseJSON &&
            xhr.responseJSON.message
        ) {
            message = xhr.responseJSON.message;
        }
        throw new Error(message);
    }
}

function renderStockTable(rows) {
    var html = "";

    if (!rows || rows.length === 0) {
        html = `
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        ไม่พบข้อมูล
                    </td>
                </tr>
            `;
    } else {
        var currentPage = parseInt(
            window.app.getQueryParam("page", 1),
            10
        ) || 1;

        var currentPageSize = parseInt(
            window.app.getQueryParam("pageSize", 10),
            10
        ) || 10;

        $.each(rows, function (index, row) {
            var rowNumber = (currentPage - 1) * currentPageSize + index + 1;
            html += `
                    <tr>
                        <td>${rowNumber}</td>
                        <td>${escapeHtml(row.LotNo)}</td>
                        <td>${escapeHtml(row.PartNo)}</td>
                        <td>${escapeHtml(row.PartName)}</td>
                        <td class="text-right">
                            ${window.app.formatNumber(row.QtyPlan)}
                        </td>
                        <td class="text-right">
                            ${window.app.formatNumber(row.QtyIn)}
                        </td>
                        <td class="text-right">
                            ${window.app.formatNumber(row.QtyOut)}
                        </td>
                        <td class="text-right">
                            ${window.app.formatNumber(row.QtyBalance)}
                        </td>
                    </tr>
                `;
        });
    }

    $("#reportTable tbody").html(html);
}


function escapeHtml(value) {
    return $("<div>")
        .text(value === null ? "" : value)
        .html();
}