<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <title>Influencer Collaboration Form | DigiChefs</title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">

        <link rel="shortcut icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="57x57">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="72x72">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="76x76">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="114x114">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="120x120">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="144x144">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="152x152">
        <link rel="apple-touch-icon" href="https://digichefs.com/wp-content/uploads/2019/11/ms-icon-310x310.png?fit=32%2C32&ssl=1" sizes="180x180">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/plugins.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/themes.css">

        <style type="text/css">
            .row.cta {
                background-image: url(https://digichefs.com/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/2020/08/DC_August_Banner-1.jpg.webp);
                background-size: cover;
                padding: 25px 0;
                margin-left: -20px;
                margin-right: -20px;
            }
            .cta a {
                color: #000;
            }
            .cta img {
                width: 60%;
            }
            .cta h2 {
                font-size: 21px;
            }
            body {
                background-image: url(https://digichefs.com/wp-content/uploads/2018/07/city.jpg) !important;
                background-attachment: fixed;
                background-size: cover;
            }
            #page-container,
            #sidebar {
                background: none;
            }
            .block {
                margin-top: 25px;
            }
            .form-intro {
                color: #6b7280;
                font-size: 15px;
                line-height: 1.6;
                margin: 0 25px 20px;
                text-align: center;
            }
            #loader {
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                position: fixed;
                display: none;
                z-index: 99998;
                top: 0;
            }
            .show {
                display: block !important;
            }
            .loader-parent {
                display: table;
                width: 100%;
                height: 100%;
            }
            .loader-center {
                display: table-cell;
                width: 100%;
                height: 100%;
                vertical-align: middle;
            }
            .chosen-container-multi.chosen-container .chosen-choices::after {
                background: url("images/chosen-sprite.png") no-repeat 3px 4px;
                width: 16px;
                height: 100%;
                content: " ";
                position: absolute;
                right: 0;
            }
            .lds-ellipsis {
                position: relative;
                vertical-align: middle;
                width: 64px;
                margin: 0 auto;
                height: 64px;
            }
            .lds-ellipsis div {
                position: absolute;
                top: 27px;
                width: 11px;
                height: 11px;
                border-radius: 50%;
                background: #fff;
                animation-timing-function: cubic-bezier(0, 1, 1, 0);
            }
            .lds-ellipsis div:nth-child(1) {
                left: 6px;
                animation: lds-ellipsis1 0.6s infinite;
            }
            .lds-ellipsis div:nth-child(2) {
                left: 6px;
                animation: lds-ellipsis2 0.6s infinite;
            }
            .lds-ellipsis div:nth-child(3) {
                left: 26px;
                animation: lds-ellipsis2 0.6s infinite;
            }
            .lds-ellipsis div:nth-child(4) {
                left: 45px;
                animation: lds-ellipsis3 0.6s infinite;
            }
            .btn-submit {
                display: inline-block;
                position: relative;
                padding: 0.2em 2em;
                font-weight: 500;
                font-size: 20px;
                color: #fff;
                border-radius: 4px;
                border: 2px solid #fff !important;
                background-color: #ec1f24 !important;
                line-height: 1.7em !important;
                transition: all .2s;
            }
            .btn-submit:after,
            .btn-submit:before {
                position: absolute;
                margin-left: -1em;
                opacity: 0;
                text-shadow: none;
                font-size: 32px;
                font-weight: 400;
                font-style: normal;
                font-variant: none;
                line-height: 1em;
                text-transform: none;
                content: "\35";
                -webkit-transition: all .2s;
                -moz-transition: all .2s;
                transition: all .2s;
            }
            .btn-submit:hover {
                background-color: #00acc1;
                color: #fff !important;
                box-shadow: 0px 2px 18px 0px rgba(0,0,0,0.3) !important;
                background-image: linear-gradient(90deg,#ec2124 20%,#ecb131 100%) !important;
            }
            @keyframes lds-ellipsis1 {
                0% {
                    transform: scale(0);
                }
                100% {
                    transform: scale(1);
                }
            }
            @keyframes lds-ellipsis3 {
                0% {
                    transform: scale(1);
                }
                100% {
                    transform: scale(0);
                }
            }
            @keyframes lds-ellipsis2 {
                0% {
                    transform: translate(0, 0);
                }
                100% {
                    transform: translate(19px, 0);
                }
            }
            @media(max-width:767px) {
                .cta {
                    min-height: 420px;
                    background-image: url(https://digichefs.com/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/2020/08/DC_August_Banner-mobile.jpg.webp) !important;
                    background-position: bottom;
                }
                .cta img {
                    width: 100%;
                }
            }
        </style>

        <script src="js/vendor/modernizr-3.3.1.min.js"></script>
    </head>
    <body>
        <img src="img/placeholders/layout/login2_full_bg.jpg" alt="Full Background" class="full-bg animation-pulseSlow">

        <div id="page-wrapper" class="page-loading">
            <div class="preloader">
                <div class="inner">
                    <div class="preloader-spinner themed-background hidden-lt-ie10"></div>
                    <h3 class="text-primary visible-lt-ie10"><strong>Loading..</strong></h3>
                </div>
            </div>

            <div id="page-container" class="header-fixed-top">
                <div id="main-container">
                    <div class="row">
                        <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                            <a href="https://digichefs.com">
                                <img class="center-block" style="margin-top:20px" src="https://digichefs.com/wp-content/uploads/2019/11/2019_dc-logo_low.png" alt="DigiChefs">
                            </a>

                            <div class="block">
                                <div class="block-title">
                                    <h1 class="text-center center-block" style="display: block;">Influencer Collaboration Form</h1>
                                </div>

                                <p class="form-intro">Share your profile, preferred brand categories and indicative reel cost. Our team will review your details and reach out when there is a relevant collaboration fit.</p>

                                <form id="influencer-form" method="post" class="form-horizontal form-bordered">
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="name">Name<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Your full name" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="channel_link">Channel link / Handle link<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="url" id="channel_link" name="channel_link" class="form-control" placeholder="Instagram / YouTube / LinkedIn profile link" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="phone">Phone number<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone number" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="email">Email ID<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="verticals">Preferred business verticals to work with<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <select id="verticals" name="verticals[]" class="mselect form-control" multiple required data-placeholder="Select preferred verticals">
                                                <option value="Pharma & Healthcare">Pharma & Healthcare</option>
                                                <option value="Beauty / Skin & Body Care">Beauty / Skin & Body Care</option>
                                                <option value="Real Estate">Real Estate</option>
                                                <option value="Food & FMCG">Food & FMCG</option>
                                                <option value="Automobile">Automobile</option>
                                                <option value="BFSI / FinTech">BFSI / FinTech</option>
                                                <option value="Education">Education</option>
                                                <option value="Lifestyle & Entertainment">Lifestyle & Entertainment</option>
                                                <option value="B2B / Manufacturing">B2B / Manufacturing</option>
                                                <option value="B2B Services / Consulting / SAAS">B2B Services / Consulting / SAAS</option>
                                                <option value="Personalities">Personalities</option>
                                                <option value="FnB">FnB</option>
                                                <option value="Travel & Hospitality">Travel & Hospitality</option>
                                                <option value="Shipping & Logistics">Shipping & Logistics</option>
                                                <option value="Community Welfare">Community Welfare</option>
                                                <option value="Consumer Electronics">Consumer Electronics</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="media_kit">Media kit / portfolio link</label>
                                        <div class="col-md-6">
                                            <input type="url" id="media_kit" name="media_kit" class="form-control" placeholder="Media kit or portfolio link, if any">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="reel_cost">Ball park cost per reel up to 60 seconds<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" id="reel_cost" name="reel_cost" class="form-control" placeholder="Example: 25000" required>
                                            <span class="help-block">Indicative number only. Final budget expectations will be discussed on call.</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="past_collabs">Past brand collab links</label>
                                        <div class="col-md-6">
                                            <textarea id="past_collabs" name="past_collabs" class="form-control" rows="4" placeholder="Paste past collaboration links, one per line"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group form-actions">
                                        <div class="col-md-12" style="text-align: center;">
                                            <button type="submit" class="btn btn-effect-ripple btn-submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="loader" style="display: none;">
            <div class="overlay"></div>
            <div class="loader-parent">
                <div class="loader-center">
                    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                </div>
            </div>
        </div>

        <script src="js/vendor/jquery-2.2.4.min.js"></script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/app.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
        <script>
            var $loading = $('#loader').hide();

            jQuery(document).ajaxStart(function () {
                $loading.show();
            }).ajaxStop(function () {
                $loading.hide();
            });

            $(function () {
                if ($.fn.chosen) {
                    $('.mselect').chosen({ width: '100%' });
                }

                $('#influencer-form').validate({
                    errorClass: 'help-block animation-pullUp',
                    errorElement: 'div',
                    errorPlacement: function (error, element) {
                        element.parents('.form-group > div').append(error);
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    },
                    success: function (element) {
                        element.closest('.form-group').removeClass('has-success has-error');
                        element.closest('.help-block').remove();
                    },
                    submitHandler: function (form) {
                        if (!$('#verticals').val() || $('#verticals').val().length === 0) {
                            alert('Please select at least one preferred business vertical.');
                            return false;
                        }

                        $.ajax({
                            url: 'php_actions/createinfluencerweb.php',
                            type: 'POST',
                            data: $(form).serialize(),
                            dataType: 'json',
                            success: function (response) {
                                alert(response.messages || 'Thank you! Your details have been submitted.');
                                if (response.success) {
                                    form.reset();
                                    if ($.fn.chosen) {
                                        $('.mselect').val('').trigger('chosen:updated');
                                    }
                                }
                            },
                            error: function (xhr, status, error) {
                                var message = 'Something went wrong while submitting the form.';
                                if (xhr && xhr.responseText) {
                                    try {
                                        var response = JSON.parse(xhr.responseText);
                                        if (response && response.messages) {
                                            message = response.messages;
                                        }
                                    } catch (e) {
                                        message += '\n\nServer response: ' + xhr.responseText.substring(0, 300);
                                    }
                                } else if (error) {
                                    message += '\n\n' + error;
                                }
                                alert(message);
                            }
                        });
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>
