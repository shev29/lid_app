<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PT. Berdiri Matahari Logistik</title>
    <meta name="description" content="LOGISTEED - PT. Berdiri Matahari Logistik">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Google Chrome, Firefox & Opera -->
    <meta name="theme-color" content="#e9e9e9">
    <!-- Safari iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#e9e9e9">
    <!-- Windows Phone -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-navbutton-color" content="#e9e9e9">
    <link rel="stylesheet" type="text/css" href="css/app/login.css">
    <link rel="shortcut icon" href="assets/images/logisteed-favicon-48.png">
    <link rel="icon" href="assets/images/logisteed-favicon-48.png">

    <link rel="stylesheet" type="text/css" href="css/vendor/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/fontawesome.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/regular.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/solid.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/duotone.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/v4-font-face.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/vendor/fontawesome6/css/v4-shims.css"/>
	<link rel="stylesheet" type="text/css" href="css/vendor/snackbar.css">
</head>

<body>
    <div class="container">
        <div class="row login_container justify-content-md-center">
			<div class="row mb-4" style="display: block;">
				<div class="p-0 text-left">
					<a href="{{ route('login') }}" id="forgotPassword"  style="color: #1f1f1f !important"><i class="fa-solid fa-arrow-left"></i> Back to Login Page</a>
				</div>
			</div>
            <div class="">
                <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formChangePassword"  onkeydown="return event.key != 'Enter';">
					@if ($valid == true)
						<input type="hidden" name="token" value="{{ $token }}">
						<div class="form-group mb-3">
							<label for="newPassword" class="form-label required">New Password :</label>
							<div class="form-group d-flex align-items-center position-relative">
								<input type="password" class="form-control form-control-lg" id="newPassword" name="newPassword" autocomplete="false">
								<span class="position-absolute end-0 top-0 h-100 d-flex"><button type="button" class="btn btn-light showPassword" data-current="hide">Show</button></span>
							</div>
						</div>

						<div class="form-group mb-3">
							<label for="newPasswordConfirm" class="form-label required">Confirm New Password :</label>
							<div class="form-group d-flex align-items-center position-relative">
								<input type="password" class="form-control form-control-lg" id="newPasswordConfirm" name="newPasswordConfirm" autocomplete="false">
								<span class="position-absolute end-0 top-0 h-100 d-flex"><button type="button" class="btn btn-light showPassword" data-current="hide">Show</button></span>
							</div>
						</div>

						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-3" style="margin-top: 5px;">
							<button type="button" class="btn btn-form btn-secondary col-xs-12 col-sm-12 col-md-12 col-lg-12 changePasswordGroup" id="changePassword">Change Password</button>
						</div>
					@else
						<div class="alert alert-danger m-0 w-100 p-2" role="alert">Password reset link has expired, please create forgot password again.</div>
					@endif

                    <div class="row" style="margin: 30px auto 13px auto;text-align:center">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            &copy; <?php echo date('Y');?> PT. Berdiri Matahari Logistik
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="js/vendor/jquery-2.0.2.min.js"></script>
    <script src="js/vendor/jquery-ui-1.10.3.min.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
	<script src="js/vendor/snackbar.js"></script>
    <script>
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

		$.fn.btnLoading = async function(callback) {
			var $clickedButton = $(this);
			var originalText = $clickedButton.html();
			$clickedButton.closest('div').find('button').prop('disabled', true);
			$clickedButton.html('<i class="fas fa-spinner fa-spin"></i> Please wait');

			try {
				await callback();
			} catch (error) {
				console.error("Something went wrong:", error);
			} finally {
				$clickedButton.closest('div').find('button').prop('disabled', false);
				$clickedButton.html(originalText);
			}
		};

		$(document).on('click', '#changePassword', function () {
			let $this = $(this);
			// let thisHtml = $this.html();
			Snackbar.close();
			clearValidation();
			const formData = new FormData($('#formChangePassword')[0]);
			formData.append('type', 'FORGOT');

			$(this).btnLoading(async function() {
				try {
					$('.changePasswordGroup').prop('disabled', true);
					$this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
					const response = await fetch('/updatePassword', {
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
						let countdown = 4;
						Snackbar.show({
							pos: 'bottom-center',
							duration: '6000',
							text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']} and automatically direct to login page in <span id="snackbar-countdown" style="color:#fff !important">${countdown}</span> seconds.`
						});

						let countdownInterval = setInterval(() => {
							countdown--;
							document.getElementById('snackbar-countdown').textContent = countdown;
							if (countdown <= 1) {
								clearInterval(countdownInterval);
								window.location.href = result['redirect'];
							}
						}, 1000);
					}
					else if (response.status === 401) {
						Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page' });
					}
					else if (response.status === 422) {
						handleValidationErrors(result.errors);
						Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
					}
					else if (response.status === 419) {
						handleValidationErrors(result.errors);
						Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
					}
					else {
						Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}` });
					}
				}
				catch (error) {
					Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
				}
				finally {
					$('.changePasswordGroup').prop('disabled', false);
				}
			});
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