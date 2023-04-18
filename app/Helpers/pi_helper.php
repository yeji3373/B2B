<?php
function makeHtml() {
  $html = '';

  $html = "<!DOCTYPE html>
          <html>
            <head>
              <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
              <!--<link href='https://hangeul.pstatic.net/hangeul_static/css/nanum-gothic-coding.css' rel='stylesheet'>-->
              <!--<link href='https://hangeul.pstatic.net/hangeul_static/css/nanum-gothic.css' rel='stylesheet'>-->
              <style>
                @page { margin: 30px 20px; }

                * { font-family: NanumGothic, sans-serif; box-sizing: border-box; }
              
                body { font-size: 7.5pt; box-sizing: border-box; }
                .pi-form-container { padding: 0; min-height: 100vh; }
                .pi-form-container p { margin: 0; }
                .pi-form-container .pi-company-name { width: 100%; border-bottom: 1px solid black; }
                .pi-form-container .pi-company-name .btn { display: none;}
                .pi-form-container .pi-company-name > div { margin-bottom: 2px; font-size: 25pt; border-bottom: 1px solid black; }
                .pi-form-container .pi-date { text-align: right; }
                .pi-form-container .pi-seller-buyer { display: table; width: 100%; margin-bottom: 15px; }
                .pi-form-container .pi-seller-buyer > div { display: table-cell; width: 50%; }
                .pi-form-container .pi-order-no { position: relative; height: 24px; }
                .pi-form-container .pi-order-no > div { position: absolute; right: 0; min-width: 200px; width: auto; padding-bottom: 3px; border-bottom: 1px solid black; }

                .pi-form-container .fw-bold { font-weight: bold; }
                .pi-form-container .text-start { text-align: left; }
                .pi-form-container .text-end { text-align: right; }
                .pi-form-container .text-center { text-align: center; }
                .pi-form-container .fst-italic { font-style: italic; }
                .pi-form-container .text-decoration-underline { text-decoration: underline; }
                .pi-form-container .mb-1 { margin-bottom: 2px; }
                .pi-form-container .mb-2 { margin-bottom: 4px; }
                .pi-form-container .d-flex { display: grid; }
                .pi-form-container .fs-6 { font-size: 9pt; }
                .pi-form-container .color-red { color: red; }
                .pi-form-container .text-uppercase { text-transform: uppercase; }

                .pi-form-container table { width: 100%; border: 1px solid black; padding: 0; margin: 0; border-spacing: 0; }
                .pi-form-container table tr { border: 0; border-spacing: 0; padding: 0; }
                .pi-form-container table thead tr th, .pi-form-container table tbody tr td { text-align: center; padding: 3px 5px; }
                .pi-form-container table tbody tr:first-child td { border-top: 1px solid black; }
                .pi-form-container table th, .pi-form-container table td { border-right: 1px solid black; }
                .pi-form-container table th:last-child, .pi-form-container table td:last-child { border-right-width: 0; }
                .pi-form-container table tbody td, .pi-form-container table tfoot td { border-bottom: 1px solid black; }
                .pi-form-container table tfoot tr:last-child td { border-bottom-width: 0; }
                .pi-form-container table tfoot td { padding: 6px 5px; }
                .pi-form-container .invoice-notice { width: 100%; margin: 20px auto 15px; }
                .pi-form-container .invoice-notice table { width: 100%; font-size: 8pt; border-width: 0; }
                .pi-form-container .invoice-notice table td { border-width: 0; text-align: left; }
                .pi-form-container .invoice-notice table td:first-child { width: 15%; text-align: left; vertical-align: top; }
                .pi-form-container .invoice-notice table td:last-child { width: 85%; text-align: left; }
                .pi-form-container .sign { width: 30%; margin-left: auto; }
                .pi-form-container .sign div:first-child { height: 15px; }
                .pi-form-container .sign div:last-child { padding-top: 20px; }
                .pi-form-container .sign .sign-div { height: auto; border-bottom: 1px solid black;}
                .pi-form-container .sign .sign-div img { height: 40px; }
              </style>
            </head>
            <body>";

  return $html;
}