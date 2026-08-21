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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

            /* Modern DigiChefs form refresh */
            html,
            body {
                min-height: 100%;
            }

            body {
                font-family: 'Open Sans', Arial, sans-serif !important;
                background:
                    linear-gradient(135deg, rgba(14, 18, 28, 0.66), rgba(236, 31, 36, 0.18)),
                    url(https://digichefs.com/wp-content/uploads/2018/07/city.jpg) center/cover fixed no-repeat !important;
                color: #172033;
            }

            .full-bg {
                display: none;
            }

            #page-container,
            #main-container {
                min-height: 100vh;
            }

            #main-container {
                padding: 34px 15px 70px;
            }

            .col-sm-12.col-md-10.col-md-offset-1.col-lg-8.col-lg-offset-2 > a {
                display: block;
                text-align: center;
            }

            .col-sm-12.col-md-10.col-md-offset-1.col-lg-8.col-lg-offset-2 > a img {
                width: 174px;
                max-width: 58%;
                margin: 0 auto 22px !important;
                padding: 14px 18px;
                background: rgba(255, 255, 255, 0.96);
                border: 1px solid rgba(255, 255, 255, 0.72);
                border-radius: 22px;
                box-shadow: 0 22px 58px rgba(8, 16, 32, 0.22);
            }

            .block {
                overflow: hidden;
                margin-top: 0 !important;
                margin-bottom: 0;
                background: rgba(255, 255, 255, 0.97);
                border: 1px solid rgba(255, 255, 255, 0.75);
                border-radius: 30px;
                box-shadow: 0 30px 80px rgba(5, 12, 28, 0.32);
                backdrop-filter: blur(12px);
            }

            .block-title {
                position: relative;
                padding: 34px 30px 32px;
                border: 0;
                background:
                    radial-gradient(circle at 15% 15%, rgba(255,255,255,0.22), transparent 28%),
                    linear-gradient(135deg, #ec1f24 0%, #f36f22 48%, #ecb131 100%);
            }

            .block-title:after {
                content: "";
                position: absolute;
                right: -72px;
                bottom: -90px;
                width: 220px;
                height: 220px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.17);
            }

            .block-title h1 {
                position: relative;
                z-index: 1;
                margin: 0;
                color: #fff;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 34px;
                font-weight: 800;
                letter-spacing: -0.8px;
                line-height: 1.2;
            }

            .form-intro {
                max-width: 720px;
                margin: 24px auto 2px;
                padding: 0 28px;
                color: #687386;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 15px;
                line-height: 1.7;
                text-align: center;
            }

            .form-horizontal.form-bordered {
                padding: 18px 34px 34px;
            }

            .form-bordered .form-group {
                display: block;
                margin: 0;
                padding: 18px 0;
                border: 0;
                border-bottom: 1px solid #eef2f7;
            }

            .form-bordered .form-group:before,
            .form-bordered .form-group:after {
                display: none;
            }

            .form-bordered .form-group:last-child,
            .form-bordered .form-actions {
                border-bottom: 0;
            }

            .form-bordered .form-group > .control-label {
                display: block;
                width: 100%;
                max-width: 100%;
                float: none;
            }

            .form-bordered .form-group > div:not(.help-block) {
                width: 100%;
                max-width: 100%;
                float: none;
                padding-left: 0;
                padding-right: 0;
            }

            .form-bordered .form-group.form-actions > div {
                max-width: 100%;
            }

            .control-label {
                padding-top: 0 !important;
                margin-bottom: 9px;
                color: #172033;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 14px;
                font-weight: 800;
                letter-spacing: -0.1px;
                text-align: left !important;
            }

            .text-danger {
                color: #ec1f24 !important;
            }

            .form-control,
            select.form-control,
            textarea.form-control {
                min-height: 50px;
                border: 1px solid #dfe7f2;
                border-radius: 16px;
                background: #fbfcff;
                color: #1f2a44;
                box-shadow: none;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 15px;
                transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
            }

            textarea.form-control {
                min-height: 118px;
                resize: vertical;
            }

            .form-control:focus,
            select.form-control:focus,
            textarea.form-control:focus {
                border-color: #ec1f24;
                background: #fff;
                box-shadow: 0 0 0 4px rgba(236, 31, 36, 0.10);
            }

            .vertical-checkbox-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .vertical-option {
                position: relative;
                display: flex;
                align-items: center;
                gap: 10px;
                min-height: 48px;
                padding: 12px 14px;
                border: 1px solid #dfe7f2;
                border-radius: 16px;
                background: #fbfcff;
                color: #1f2a44;
                cursor: pointer;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 14px;
                font-weight: 700;
                transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background .2s ease;
            }

            .vertical-option:hover {
                border-color: rgba(236, 31, 36, 0.45);
                background: #fff;
                box-shadow: 0 10px 24px rgba(17, 24, 39, 0.08);
                transform: translateY(-1px);
            }

            .vertical-option input {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            .vertical-option span:before {
                content: "";
                display: inline-block;
                width: 18px;
                height: 18px;
                margin-right: 9px;
                vertical-align: -4px;
                border: 2px solid #cbd5e1;
                border-radius: 6px;
                background: #fff;
                transition: all .2s ease;
            }

            .vertical-option input:checked + span:before {
                border-color: #ec1f24;
                background:
                    linear-gradient(135deg, #ec1f24, #ecb131);
                box-shadow: inset 0 0 0 3px #fff;
            }

            .vertical-option:has(input:checked) {
                border-color: rgba(236, 31, 36, 0.45);
                background: #fff7ed;
                box-shadow: 0 12px 26px rgba(236, 31, 36, 0.12);
            }

            .vertical-count {
                display: none;
                margin-top: 10px;
                color: #7b8798;
                font-size: 13px;
                font-weight: 800;
            }

            .vertical-count.is-visible {
                display: block;
                color: #ec1f24;
            }

            .help-block {
                color: #7b8798;
                font-size: 13px;
            }

            .form-actions {
                padding-top: 28px !important;
                background: transparent !important;
            }

            .btn-submit {
                min-width: 184px;
                padding: 12px 34px !important;
                border: 0 !important;
                border-radius: 999px !important;
                background-image: linear-gradient(135deg, #ec1f24 0%, #f36f22 48%, #ecb131 100%) !important;
                color: #fff !important;
                font-family: 'Open Sans', Arial, sans-serif;
                font-size: 17px !important;
                font-weight: 800 !important;
                letter-spacing: 0.1px;
                box-shadow: 0 16px 34px rgba(236, 31, 36, 0.26);
            }

            .btn-submit:hover,
            .btn-submit:focus {
                transform: translateY(-2px);
                box-shadow: 0 20px 42px rgba(236, 31, 36, 0.34) !important;
            }

            .thankyou-modal {
                position: fixed;
                inset: 0;
                z-index: 100000;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: rgba(9, 14, 25, 0.62);
            }

            .thankyou-modal.is-visible {
                display: flex;
            }

            .thankyou-card {
                width: min(440px, 100%);
                overflow: hidden;
                border-radius: 28px;
                background: #fff;
                box-shadow: 0 30px 90px rgba(5, 12, 28, 0.42);
                text-align: center;
                font-family: 'Open Sans', Arial, sans-serif;
            }

            .thankyou-card-header {
                padding: 30px 24px;
                background: linear-gradient(135deg, #ec1f24 0%, #f36f22 48%, #ecb131 100%);
                color: #fff;
            }

            .thankyou-icon {
                width: 58px;
                height: 58px;
                margin: 0 auto 12px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.24);
                color: #fff;
                font-size: 34px;
                font-weight: 800;
                line-height: 58px;
            }

            .thankyou-card h2 {
                margin: 0;
                font-size: 28px;
                font-weight: 800;
                letter-spacing: -0.6px;
            }

            .thankyou-card p {
                margin: 0;
                padding: 24px 30px 6px;
                color: #5f6b7d;
                font-size: 15px;
                line-height: 1.7;
            }

            .thankyou-close {
                margin: 18px auto 28px;
                padding: 11px 30px;
                border: 0;
                border-radius: 999px;
                background: #172033;
                color: #fff;
                font-weight: 800;
            }

            @media(max-width:767px) {
                #main-container {
                    padding: 20px 10px 46px;
                }

                .block {
                    border-radius: 22px;
                }

                .block-title {
                    padding: 28px 18px 26px;
                }

                .block-title h1 {
                    font-size: 26px;
                }

                .form-intro {
                    margin-top: 18px;
                    padding: 0 18px;
                }

                .form-horizontal.form-bordered {
                    padding: 8px 18px 26px;
                }

                .form-bordered .form-group {
                    display: block;
                    padding: 15px 0;
                }

                .control-label {
                    padding-top: 0 !important;
                    margin-bottom: 8px;
                }

                .form-bordered .form-group > .control-label,
                .form-bordered .form-group > div:not(.help-block) {
                    max-width: 100%;
                    width: 100%;
                }

                .vertical-checkbox-grid {
                    grid-template-columns: 1fr;
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
                                            <div id="verticals" class="vertical-checkbox-grid">
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Pharma & Healthcare"><span>Pharma & Healthcare</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Beauty / Skin & Body Care"><span>Beauty / Skin & Body Care</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Real Estate"><span>Real Estate</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Food & FMCG"><span>Food & FMCG</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Automobile"><span>Automobile</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="BFSI / FinTech"><span>BFSI / FinTech</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Education"><span>Education</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Lifestyle & Entertainment"><span>Lifestyle & Entertainment</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="B2B / Manufacturing"><span>B2B / Manufacturing</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="B2B Services / Consulting / SAAS"><span>B2B Services / Consulting / SAAS</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Personalities"><span>Personalities</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="FnB"><span>FnB</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Travel & Hospitality"><span>Travel & Hospitality</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Shipping & Logistics"><span>Shipping & Logistics</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Community Welfare"><span>Community Welfare</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Consumer Electronics"><span>Consumer Electronics</span></label>
                                                <label class="vertical-option"><input type="checkbox" name="verticals[]" value="Other"><span>Other</span></label>
                                            </div>
                                            <div id="vertical-count" class="vertical-count"></div>
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
                                            <input type="text" id="reel_cost" name="reel_cost" class="form-control" placeholder="Example: 5000" required>
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

        <div id="thankyou-modal" class="thankyou-modal" aria-hidden="true">
            <div class="thankyou-card" role="dialog" aria-modal="true" aria-labelledby="thankyou-title">
                <div class="thankyou-card-header">
                    <div class="thankyou-icon">&#10003;</div>
                    <h2 id="thankyou-title">Thank you!</h2>
                </div>
                <p id="thankyou-message">Your details have been submitted successfully.</p>
                <button type="button" class="thankyou-close">Close</button>
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
                function showThankYouPopup(message) {
                    $('#thankyou-message').text(message || 'Your details have been submitted successfully.');
                    $('#thankyou-modal').addClass('is-visible').attr('aria-hidden', 'false');
                }

                $('.thankyou-close, #thankyou-modal').on('click', function (event) {
                    if (event.target !== this) {
                        return;
                    }

                    $('#thankyou-modal').removeClass('is-visible').attr('aria-hidden', 'true');
                });

                function updateVerticalChoiceLabel() {
                    var selectedCount = $('input[name="verticals[]"]:checked').length;
                    var $count = $('#vertical-count');

                    if (selectedCount > 0) {
                        $count.text(selectedCount + ' item' + (selectedCount === 1 ? '' : 's') + ' selected').addClass('is-visible');
                    } else {
                        $count.text('').removeClass('is-visible');
                    }
                }

                $('input[name="verticals[]"]').on('change', updateVerticalChoiceLabel);
                updateVerticalChoiceLabel();

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
                        if ($('input[name="verticals[]"]:checked').length === 0) {
                            alert('Please select at least one preferred business vertical.');
                            return false;
                        }

                        $.ajax({
                            url: 'https://digichefs.in/newhr/php_actions/createinfluencerweb.php',
                            type: 'POST',
                            data: $(form).serialize(),
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    form.reset();
                                    $('input[name="verticals[]"]').prop('checked', false);
                                    updateVerticalChoiceLabel();
                                    showThankYouPopup(response.messages || 'Thank you! Your influencer profile has been submitted successfully.');
                                } else {
                                    alert(response.messages || 'Something went wrong while submitting the form. Please try again.');
                                }
                            },
                            error: function () {
                                alert('Something went wrong while submitting the form. Please try again.');
                            }
                        });
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>
