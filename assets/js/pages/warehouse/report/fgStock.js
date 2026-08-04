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

    $("#btnExportExcel").on("click", async function (event) {
        await downloadExcel(event);
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


async function downloadExcel(event) {
    var button = event.currentTarget;
    var delay = window.app.delay(350);
    var downloadXhr = null;
    var objectUrl = null;

    button.disabled = true;
    window.app.showScLoading();

    try {
        if (
            !startDatePicker.selectedDates[0] ||
            !endDatePicker.selectedDates[0]
        ) {
            throw new Error(
                "กรุณาระบุวันที่เริ่มต้นและวันที่สิ้นสุด"
            );
        }

        var startDate = formatDate(
            new Date(startDatePicker.selectedDates[0])
        );

        var endDate = formatDate(
            new Date(endDatePicker.selectedDates[0])
        );

        var url =
            window.app.apiUrl(
                "api/warehouse/exportStockFgExcel.php"
            ) +
            "?startDate=" +
            encodeURIComponent(startDate) +
            "&endDate=" +
            encodeURIComponent(endDate);

        /*
         * ยิง API และรับข้อมูลเป็น Blob
         */
        var blob = await $.ajax({
            url: url,
            method: "GET",
            cache: false,
            processData: false,

            xhr: function () {
                downloadXhr = $.ajaxSettings.xhr();
                downloadXhr.responseType = "blob";

                return downloadXhr;
            }
        });

        var contentType =
            downloadXhr.getResponseHeader(
                "Content-Type"
            ) || "";

        /*
         * ป้องกัน API ส่ง JSON Error กลับมาด้วย HTTP 200
         */
        if (
            contentType.indexOf("application/json") !== -1
        ) {
            var responseText = await blob.text();
            var responseJson = JSON.parse(responseText);

            throw new Error(
                responseJson.message ||
                "ไม่สามารถ Export Excel ได้"
            );
        }

        if (!blob || blob.size === 0) {
            throw new Error(
                "ไฟล์ Excel ที่ได้รับไม่มีข้อมูล"
            );
        }

        /*
         * อ่านชื่อไฟล์จาก Content-Disposition
         */
        var filename =
            "FG_Stock_In_Out_" +
            startDate +
            "_to_" +
            endDate +
            ".xlsx";

        var contentDisposition =
            downloadXhr.getResponseHeader(
                "Content-Disposition"
            );

        if (contentDisposition) {
            var filenameMatch = contentDisposition.match(
                /filename="?([^"]+)"?/i
            );

            if (
                filenameMatch &&
                filenameMatch[1]
            ) {
                filename = filenameMatch[1].trim();
            }
        }

        /*
         * สร้าง URL ชั่วคราวจาก Blob
         */
        objectUrl = URL.createObjectURL(blob);

        var link = document.createElement("a");

        link.href = objectUrl;
        link.download = filename;
        link.style.display = "none";

        document.body.appendChild(link);

        link.click();
        link.remove();

    } catch (error) {
        var message =
            error.message ||
            "ไม่สามารถ Export Excel ได้";

        /*
         * ถ้า API ตอบ Error เป็น JSON Blob
         */
        if (
            downloadXhr &&
            downloadXhr.response instanceof Blob &&
            downloadXhr.response.size > 0
        ) {
            try {
                var errorText =
                    await downloadXhr.response.text();

                var errorResponse =
                    JSON.parse(errorText);

                if (errorResponse.message) {
                    message = errorResponse.message;
                }

            } catch (parseError) {
                /*
                 * ใช้ข้อความเดิมหาก Response ไม่ใช่ JSON
                 */
            }
        }

        if (
            window.app &&
            typeof window.app.showError === "function"
        ) {
            window.app.showError(message);
        } else {
            alert(message);
        }

    } finally {
        if (objectUrl) {
            setTimeout(function () {
                URL.revokeObjectURL(objectUrl);
            }, 1000);
        }

        await delay;

        button.disabled = false;
        window.app.hideScLoading();
    }
}