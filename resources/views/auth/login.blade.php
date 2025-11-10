<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ config('app.name') }}
    </title>
    <meta name="description" content="LOGISTEED - Taking on the Future">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Google Chrome, Firefox & Opera -->
    <meta name="theme-color" content="#e9e9e9">
    <!-- Safari iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#e9e9e9">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#e9e9e9">
    <link rel="stylesheet" type="text/css" href="css/app/login.css?v=6.1">
    <link rel="shortcut icon" href="assets/images/logisteed-favicon-48.png">
    <link rel="icon" href="assets/images/logisteed-favicon-48.png">

    <link rel="stylesheet" type="text/css" href="css/vendor/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/fontawesome.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/regular.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/solid.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/duotone.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/v4-font-face.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/v4-shims.css"/>
	<link rel="stylesheet" type="text/css" href="css/vendor/snackbar.css?v=6.1">
</head>

<body>
    <div class="container">
        <div class="row login_container justify-content-md-center">
            <div class="login_section">
                <div id="login_company">
                    <img src="assets/images/logisteed-logo-blue-min.png" alt="LOGISTEED">
                    <span>Taking on the Future</span>
                </div>
                <form role="form" id="login-form" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Username">
                        <label for="user_name">Username or Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <span class="position-absolute end-0 top-0 h-100 d-flex"><button type="button" class="btn btn-light showPassword" data-current="hide">Show</button></span>
                        <label for="password">Password</label>
                    </div>

                    <div class="mt-3 mb-2" style="text-align: left">
                        <label class="dropdown-item d-flex align-items-center">
                            <input type="checkbox" class="form-check-input me-2" id="remember" name="remember"> Remember Me
                        </label>
                    </div>

                    @if($redirectUri)
                        <input type="hidden" name="redirect_uri" value="{{ $redirectUri }}">
                    @endif

                </form>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-3" style="margin-top: 5px;">
                    <div class="response-messages" style="text-align:center"></div>
                    <button type="button" class="btn btn-form btn-primary col-xs-12 col-sm-12 col-md-12 col-lg-12" id="loginButton" style="">Login</button>
                </div>

                <div class="row" style="display: block;">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: right">
                        <a href="javascript:void(0);" id="forgotPassword">Reset Password ?</a>
                    </div>
                </div>

                <div class="row" style="margin: 30px auto 13px auto;">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        &copy; <?php echo date('Y');?> PT. Berdiri Matahari Logistik
                    </div>
                </div>

                {{-- <div class="row" style="display: block;">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('changeLanguange') }}" title="English">English</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{ route('changeLanguange') }}" title="Japanese">日本語</a>
                    </div>
                </div> --}}
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalMdGeneral" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false" tabindex="-1"
        aria-labelledby="modalMdGeneral" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-md my-md-2-2 top-20" id="modalMdGeneralDialog">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalMdGeneralHeader" style="background-color:#fff !important; border-bottom: none !important;">
                    <h6 class="modal-title modal-title-black" id="modalMdGeneralTitle" style="font-size:1rem">Forgot Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-3" id="modalMdGeneralBody">
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formForgotPassword" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email" class="form-label required" id="">Email :</label>
                            <input type="text" class="form-control form-control-lg" id="email" name="email" autocomplete="false">
                        </div>
                    </form>
                </div>
                <div class="modal-footer pb-3" id="modalMdGeneralFooter" style="background-color:#fff !important; border-top: none !important;">
                    <div class="container-fluid p-0">
                        <div class="row justify-content-center w-100 mx-0">
                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                <button type="button" class="btn btn-default w-100 me-2 changePasswordGroup" data-bs-dismiss="modal" title="Close">
                                    Close
                                </button>
                            </div>
                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                <button type="button" class="btn btn-form btn-secondary w-100 color-white changePasswordGroup" id="resetPasswordButton" data-type="" data-form="" data-token="" title="Reset password">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/vendor/jquery-2.0.2.min.js"></script>
    <script src="js/vendor/jquery-ui-1.10.3.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
	<script src="js/vendor/snackbar.js"></script>
    <script>
		$(document).ready(function(){
            document.getElementById("password").addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    document.getElementById("loginButton").click();
                }
            });
            document.getElementById("loginButton").addEventListener("click", async function(event) {
                event.preventDefault();
				const $this = $(this);
				const thisHtml = $this.html();
				event.preventDefault();

				$('.form-control').removeClass('is-invalid');
				$this.prop('disabled', true);
		    	$this.html('Please wait...');
			
				const userName = document.getElementById("user_name").value;
				const userPassword = document.getElementById("password").value;
				let processForm = true;
				if(userName == ''){
					processForm = false;
					document.getElementById("user_name").classList.add("is-invalid");
				}

				if(userPassword == ''){
					processForm = false;
					document.getElementById("password").classList.add("is-invalid");
				}

				if(processForm === false){
					Snackbar.show({pos: 'bottom-center', text: 'Input username or password'});
					$this.prop('disabled', false);
		    		$this.html(thisHtml);
					return false;
				}				

                let form = $('#login-form');
                let formData = new FormData(form[0]);

                try {
                    const response = await fetch('{{ route("authLogin") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'Referer': window.location.href
                        }
                    });

                    const data = await response.json();
                    if (response.status === 200) {
                        window.location.href = data.redirect;
                        $this.html(data.message);
                        window.location.href = data.redirect;
                    }
                    else if (response.status === 419) {
                        $this.prop('disabled', false);
                        $this.html(thisHtml);
                        Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
                        $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page</p>`);
                    }
                    else {
                        $this.prop('disabled', false);
                        $this.html(thisHtml);
                        if(data.key == 'user_name'){
                            document.getElementById("user_name").classList.add("is-invalid");
                        }
                        else if(data.key == 'password'){
                            document.getElementById("password").classList.add("is-invalid");
                        }

                        Snackbar.show({pos: 'bottom-center', text: data.message});
                    }
                }
                catch (error) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
                    $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}</p>`);
                }
			});

            document.getElementById("forgotPassword").addEventListener("click", async function(event) {
                event.preventDefault();
                $('#email').val('');
                clearValidation();
                Snackbar.close();
                $('.modal').modal('hide');
                $('#modalMdGeneral').modal('show');
            });

            document.getElementById("resetPasswordButton").addEventListener("click", async function(event) {
                event.preventDefault();
				const $this = $(this);
				const thisHtml = $this.html();
				event.preventDefault();

				$('.form-control').removeClass('is-invalid');
				$('.changePasswordGroup').prop('disabled', true);
		    	$this.html('Please wait...');

                let form = $('#formForgotPassword');
                let formData = new FormData(form[0]);
                // formData.append('token', $('#token').attr('token'));

                try {
                    const response = await fetch('{{ route("resetPassword") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Referer': window.location.href,
                        }
                    });

                    const result = await response.json();
                    if (response.status === 200) {
                        $('.changePasswordGroup').prop('disabled', false);
                        $this.html(thisHtml);
                        $('.modal').modal('hide');
                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result.message}` });
                    }
                    else if (response.status === 422) {
                        handleValidationErrors(result.errors);
                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
                    }
                    else {
                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${result['message']}` });
                    }
                }
                catch (error) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
                }
                finally {
                    $('.changePasswordGroup').prop('disabled', false);
                    $this.html(thisHtml);
                }
			});

            function clearValidation(){
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            }

            function handleValidationErrors(errors) {
                clearValidation();
                $.each(errors, function(field, messages) {
                    let ele;
                    const match = field.match(/^(.*?)\.(\d+)$/);
                    if (match) {
                        const fieldName = match[1] + '[]';
                        const index = match[2];

                        if($(`[name="${fieldName}"]`).attr('type') === 'file' && selectedFiles.length > 0){
                            ele = $(`.fileListItem`).eq(index);
                        }
                        else {
                            ele = $(`[name="${fieldName}"]`).eq(index);
                            if (ele.hasClass('vscomp-hidden-input')) {
                                ele = ele.closest('.vscomp-ele');
                            }
                        }
                    }
                    else {
                        ele = $(`[name="${field}"]`);
                    }

                    if (ele.length) {
                        let feedback;
                        if (ele.hasClass('date-picker-input')) {
                            ele.parent().addClass('is-invalid');
                            ele.parent().parent().addClass('is-invalid');
                            feedback = ele.parent().next('.invalid-feedback');
                            if (feedback.length === 0) {
                                feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                                ele.parent().parent().after(feedback);
                            }
                        }
                        else if (ele.closest('.form-group').hasClass('d-flex')) {
                            ele.addClass('is-invalid');
                            ele.closest('.form-group').addClass('is-invalid');
                            feedback =  ele.closest('.form-group').parent().find('.invalid-feedback');
                            if (feedback.length === 0) {
                                feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                                ele.closest('.form-group').parent().append(feedback);
                            }
                        }
                        else if (ele.hasClass('select2')) {
                            ele.addClass('is-invalid');
                            ele.next().find('.select2-selection').addClass('is-invalid');
                            feedback = ele.parent().find('.invalid-feedback');
                            if (feedback.length === 0) {
                                feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                                ele.parent().append(feedback);
                            }
                        }
                        else {
                            ele.addClass('is-invalid');
                            if(ele.parent().hasClass('form-group')){
                                feedback = ele.parent('.form-group').find('.invalid-feedback');
                                if (feedback.length === 0) {
                                    feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                                    ele.parent('.form-group').append(feedback);
                                }
                            }
                            else {
                                feedback = ele.parent().find('.invalid-feedback');
                                if (feedback.length === 0) {
                                    feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                                    ele.parent().append(feedback);
                                }
                            }
                        }
                    }
                });
            }
		});

        $(document).on('click', '.showPassword', function () {
            if($(this).attr('data-current') == 'hide') {
                $(this).closest('div').find('input').attr('type', 'text');
                $(this).html('Hide');
                $(this).attr('data-current', 'show');
            }
            else {
                $(this).closest('div').find('input').attr('type', 'password');
                $(this).html('Show');
                $(this).attr('data-current', 'hide');
            }
        });
	</script>
</body>

</html>