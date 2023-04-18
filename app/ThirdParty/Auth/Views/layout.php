<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="/css/common.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="/js/common.js"></script>
  <script type="text/javascript" src="/js/login.js"></script>
  <style type="text/css">
    body {
      color: #444;
      background: #dbdbdb;
    }
    main { display: flex; }
    section h1 {
      text-align: center;
      border-bottom: 1px solid;
      padding-bottom: 0.8rem;
    }    
    main > section {
      padding: 1.5rem 3rem;
      box-sizing: border-box;
      margin: auto;
      background: #fff;
      border-radius: 4.2px;
      box-shadow: 0 3px 10px -2px rgba(0, 0, 0, 0.2);
    }  
    .login_section { width: 25%; }
    .register_section { width: 60%; }
    .register_section form { display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 1.5rem; align-items: baseline; }
    .register_section form fieldset { border: 1px solid #b8b8b8; border-radius: 4.2px; padding: 0.2rem 1.5rem; }
    .register_section form .grid-footer { grid-area: footer; grid-column: auto / span 2; text-align: -webkit-right;}
    .register_section form fieldset > div { position: relative; margin: 0.8rem auto; padding: 0.8rem 0 0; }
    .register_section form fieldset > div > label { 
      position: absolute;
      left: 0.5rem;
      top: 0;
      pointer-events: none;
      text-align: left;
      transform: translate(0, -1%);
      background: linear-gradient(#fff 15%, #f9f9f9 85%);
    }
    .register_section form .buyer-phone-group {
      display: flex;
      flex-direction: row;
      flex-wrap: nowrap;
      align-items: center;
    }
    .register_section form .buyer-phone-group select { 
      width: 20%;
    }
    .checkbox-group {
      width: 100%;
      box-sizing: border-box;
      padding: 0.8rem;
      background-color: #f9f9f9;
      border: 1px solid #e5e5e5;
      border-radius: 3px;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
    }
    .checkbox-group > label {
      min-width: 50%;
    }
    .countries { display: none; }
    .countries [class^='region_'] {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
    }

    .countries [class^='region_'] label {
      min-width: 46%;
      margin: 0 0.8rem 0 0;
    }

    .auth-menu {
      position: fixed;
      left: 0;
      top: 0;
      z-index: 10001;
      width: 100%;
      box-sizing: border-box;
      line-height: 40px;
      background-color: #444;
      color: #fff;
      font-size: 14px;
      padding-left: 15px;
      padding-right: 15px;
    }
    .auth-menu a {
      color: #fff;
      text-decoration: none;
      font-weight: bold;
    }
    .notification {
      padding: 10px;
      background-color: #eee;
      font-weight: bold;
      margin-bottom: 30px;
    }

    button {
      background-color: #caff4b;
      box-shadow: none;
      border-width: 0;
      font-weight: bold;
      border-radius: 5px;
      margin: auto;
      padding: 0.8rem 4rem;
    }
  </style>
</head>
<body> 
<?php
if ( session()->isLoggedIn ) {
	echo view('Auth\Views\_navbar');
}
?>

<main role="main" class="wrapper">
	<?=$this->renderSection('main') ?>
</main>

</body>
</html>