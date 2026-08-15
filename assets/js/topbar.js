(function ($) {
    "use strict";

    var $form = $("#changeOwnPasswordForm");
    var $profileForm = $("#editOwnProfileForm");

    $profileForm.on("submit", async function (event) {
        event.preventDefault();
        var $button = $("#btnSaveOwnProfile").prop("disabled", true);

        try {
            var response = await $.ajax({
                url: window.app.apiUrl("api/users/updateOwnProfile.php"),
                method: "POST",
                dataType: "json",
                data: new FormData($profileForm[0]),
                processData: false,
                contentType: false
            });

            var profile = response.data || {};
            $(".js-current-display-name").text(profile.displayName || "Unknown User");
            $("#profileHeroPosition").text(profile.position || "-");
            if (profile.imageUrl) {
                $(".js-topbar-profile-image").attr("src", window.app.apiUrl(profile.imageUrl)).removeClass("d-none")
                    .next("i").addClass("d-none");
            }
            $("#profileModal").modal("hide");
            await window.app.showSuccess("บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว");
        } catch (xhr) {
            await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการบันทึกโปรไฟล์");
        } finally {
            $button.prop("disabled", false);
        }
    });

    function bindImagePreview(inputSelector, previewSelector, placeholderSelector) {
        $(inputSelector).on("change", function () {
            if (!this.files || !this.files[0]) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                $(previewSelector).attr("src", event.target.result).removeClass("d-none");
                if (placeholderSelector) {
                    $(placeholderSelector).addClass("d-none");
                }
            };
            reader.readAsDataURL(this.files[0]);
        });
    }

    bindImagePreview("#ownProfileImage", "#ownProfileImagePreview", "");
    bindImagePreview("#ownProfileSignature", "#ownProfileSignaturePreview", "#ownProfileSignaturePlaceholder");

    $form.on("submit", async function (event) {
        event.preventDefault();

        var newPassword = $("#newPassword").val();
        var confirmPassword = $("#confirmPassword").val();

        if (newPassword !== confirmPassword) {
            await window.app.showWarning("รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน");
            return;
        }

        var $button = $("#btnChangeOwnPassword");
        $button.prop("disabled", true);

        try {
            var response = await $.ajax({
                url: window.app.apiUrl("api/users/changeOwnPassword.php"),
                method: "POST",
                dataType: "json",
                data: $form.serialize()
            });

            if (!response.success) {
                await window.app.showError(response.message || "ไม่สามารถเปลี่ยนรหัสผ่านได้");
                return;
            }

            $("#changePasswordModal").modal("hide");
            $form[0].reset();
            await window.app.showSuccess("เปลี่ยนรหัสผ่านเรียบร้อยแล้ว");
        } catch (xhr) {
            await window.app.handleAjaxError(xhr, "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน");
        } finally {
            $button.prop("disabled", false);
        }
    });

    $("#changePasswordModal").on("hidden.bs.modal", function () {
        $form[0].reset();
    });
})(jQuery);
