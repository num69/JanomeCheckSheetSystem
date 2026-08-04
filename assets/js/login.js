$(function () {
    const username = $('#username');
    const password = $('#password');
    const btnLogin = $('#loginButton');
    let isSubmitting = false;

    $("#loginForm").on("submit", async function (event) {
        event.preventDefault();

        if(isSubmitting){
            return;
        }
        isSubmitting = true;

        try{
            await login();
        }
        finally{
            isSubmitting = false;
        }
    });

    async function login() {
        const usernameVal = username.val()?.trim();
        const passwordVal = password.val()?.trim();

        if (!usernameVal || !passwordVal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter both username and password.',
            });
            return;
        }

        btnLogin.disabled = true;

        window.app.storage.remove(window.APP_CONFIG.SIDEBAR_MENU_CACHE_KEY);

        window.app.showScLoading();

        const delay = window.app.delay(500);

        try {

            const response = await $.ajax({
                url: 'api/auth/login.php',
                method: 'POST',
                data: { username: usernameVal, password: passwordVal },
            });

            await delay; 

            window.app.hideScLoading();

            if (response.success) {
                await window.app.showSuccess('เข้าสู่ระบบสำเร็จ กำลังเปลี่ยนหน้า...', { timer: 1500 });
                location.href = 'home.php';
            } else {
                await window.app.showWarning(response.message || 'Invalid username or password.');
            }
        } catch (error) {
            await delay;
            window.app.hideScLoading();
            const response = error.responseJSON || {};
            const message = response.message || 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ';
            if (error.status !== 500) {
                await window.app.showWarning(message);
            }
            else {
                await window.app.showError(message);
            }
        } finally {
            btnLogin.disabled = false;
            window.app.hideScLoading();
        }
    }
});