export function loadHeader() {
  document.getElementById("header").innerHTML = `
        <header>
            <nav class="navbar navbar-light bg-light navbar-expand-lg bg-body-tertiary fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand ms-4" href="index.html">
                    <img src="img/web/logo_sm.png" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">導覽列</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body container-fluid justify-content-center" id="offcanvasNavbar">
                        <ul class="navbar-nav justify-content-end flex-grow-1 me-5 ">
                            <li class="nav-item mt-2">
                                <a class="nav-link active" aria-current="page" href="index.html">首頁</a>
                            </li>
                            <li class="nav-item mt-2">
                                <a class="nav-link" href="#">最新消息</a>
                            </li>
                            <li class="nav-item mt-2">
                                <a class="nav-link" href="product_webpage.html">最新產品</a>
                            </li>
                            <li class="nav-item mt-2">
                                <a class="nav-link" href="#">關於我們</a>
                            </li>
                            <li id="manager_function" class="nav-item dropdown d-none mt-2">
                                <a class="nav-link text-primary" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    管理員功能
                                </a>
                                <ul class="dropdown-menu text-center">
                                    <li><a class="dropdown-item" href="member_control_panel.html">會員管理</a></li>
                                    <li><a class="dropdown-item" href="product_C_panel.html">新增商品</a></li>
                                    <li><a class="dropdown-item" href="product_control_panel.html">產品管理</a></li>
                                    <li><a class="dropdown-item" href="#">後臺數據</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">未來功能</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown ">
                                <a href="" class="nav-link" role="button" data-bs-toggle="dropdown">
                                    <span class="me-3 d-none" id="navbar_username_showtext">
                                        <img src="#" id="navbar_memberPhoto"
                                            class="rounded rounded-circle bg-cover border border-4 border-white"
                                            style="width: 50px; height: 50px;">
                                        <span class="h5" id="navbar_username_text">XX</span>
                                    </span>
                                </a>
                                <ul id="normal_member_function" class="dropdown-menu tetx-center">
                                    <li class="dropdown-item text-center"><a href="member_info_panel.html">會員資料</a></li>
                                    <li class="dropdown-item text-center"><a href="">訂單查詢</a></li>
                                    <li class="dropdown-item text-center">
                                        <button id="navbar_btn_logout" class="btn btn-danger d-none">登出</button>
                                    </li>
                                </ul>
                            </li>

                            <li id="navbar_btn_login" class="nav-item mt-2">
                                <a href="#logimModal" class="nav-link" data-bs-target="#loginModal"
                                    data-bs-toggle="modal">
                                    <i class="fa-solid fa-user fa-xl" style="color: #384152"></i>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            <hr>
        </nav>
        </header>
    `;
}
