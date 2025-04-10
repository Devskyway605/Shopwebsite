export function loadLogin() {
    document.getElementById("loginModal").innerHTML = `
        <div class="modal modal-lg fade" id="loginModal" aria-hidden="true" aria-labelledby="loginModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-cover">
                    <div class="row justify-content-center">
                        <div class="col-6 w-50 modal_left_login text-white text-center">
                            <div class="align-content-center text-start p-4">
                                <h3 class="text-center mt-5">管理者登入</h3>
                                <label for="Modal-username-login" class="form-label"
                                    aria-placeholder="使用者帳號">Username</label>
                                <input type="text" id="username_login" placeholder="使用者帳號" class="form-control" />
                                <label for="username-login-modal" class="form-label mt-2"
                                    aria-placeholder="使用者帳號">Password</label>
                                <input type="password" id="password_login" placeholder="使用者密碼" class="form-control" />
                                <div class="form-check mt-3">
                                    <input type="checkbox" id="Modal-keeplogin" class="form-check-input" />
                                    <label class="form-check-label" for="Modal-keeplogin">保持登入</label>
                                </div>
                            </div>
                            <div class="row justify-content-center mt-2">
                                <button id="ok_btn_login" class="btn btn-lg border-1">登入</button>
                                <div class="hr-container">
                                    或
                                </div>
                                <div class="" width="200">
                                    <a href="#registerModal" class="btn btn-lg btn-primary text-white border-1"
                                        data-bs-target="#registerModal" data-bs-toggle="modal"
                                        data-bs-dismiss="modal">註冊成為會員</a>
                                </div>

                            </div>

                        </div>
                        <div class="col-6 w-50 modal_right_login bg-cover text-end">
                            <button type="button" class="btn-close me-3 mt-3" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}