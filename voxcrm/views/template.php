<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="google-site-verification" content="uCAf_UPwHFGd7zTfU4FBB5zT6lRcRW03adtf-rnXgI0" />
  <title>
    <?php if(isset($title) && ! empty($title)): ?>
      <?php echo $title; ?> | VOX CRM
    <?php else: ?>
      VOX CRM
    <?php endif; ?>
  </title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width">

  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
  <style>
	  body {
      padding-top: 60px;
      padding-bottom: 40px;
    }
  </style>
  <link rel="stylesheet" href="/assets/css/bootstrap-responsive.css">
  <link rel="stylesheet" href="/assets/css/main.css">

  <link rel="stylesheet" href="/assets/cssmenu/styles.css">

  <link rel="stylesheet" href="/assets/footable/css/footable.core.css">
  <link rel="stylesheet" href="/assets/footable/css/footable.metro.css">
  <link rel="stylesheet" href="/assets/footable/css/footable.custom.css">

  <link rel="stylesheet" href="/assets/css/custom_styles.css">


  <?php if( ! empty($css)): ?>
    <?php foreach($css as $c): ?>
      <link rel="stylesheet" href="/assets/<?php echo $c; ?>">
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if( ! empty($print_css)): ?>
  <?php foreach($print_css as $c): ?>
    <link rel="stylesheet" href="/assets/<?php echo $c; ?>" media="print">
    <?php endforeach; ?>
  <?php endif; ?>

  <script type="text/javascript" src="/assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<?php if($this->session->userdata('mail')): ?>
  <header>
    <?php $this->load->view('includes/header', $menu); ?>
  </header>
<?php endif; ?>

<div class="container">
  <?php if(isset($title) && ! empty($title)): ?>
    <h1><?php echo $title; ?></h1>
  <?php endif; ?>

  <!-- message, errors -->
  <?php if(isset($message) && $message): ?>
    <div class="alert alert-success">
      <button data-dismiss="alert" class="close" type="button">×</button>
      <?php echo $message; ?>
    </div>
  <?php endif; ?>

  <?php if(isset($error) && $error): ?>
  <div class="alert alert-error">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <?php echo $error; ?>
  </div>
  <?php endif; ?>

  <?php $this->load->view('templates/' . $template); ?>
  <hr>

  <footer>
    <?php $this->load->view('includes/footer'); ?>
  </footer>

</div> <!-- /container -->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">window.jQuery || document.write('<script src="/assets/js/vendor/jquery-1.8.3.min.js"><\/script>')</script>

<script type="text/javascript" src="/assets/js/vendor/bootstrap.min.js"></script>

<script type="text/javascript" src="/assets/cssmenu/menu_jquery.js"></script>

<script type="text/javascript" src="/assets/js/plugins.js"></script>
<script src="/assets/js/main.js"></script>

<script type="text/javascript" src="/assets/footable/js/footable.js"></script>

<script type="text/javascript" >
    $(function() {
      $('table.responsive').footable( {
		  breakpoints: {
			xlarge: 1200,
			tablet: 1024,
			large: 979,
			medium: 767,
			small: 640,
			phone: 480,
			xsmall: 320
		  }
	  });
    });
</script>

<script type="text/javascript" src="/assets/js/custom.js"></script>

<?php if( ! empty($external_js)): ?>
  <?php foreach($external_js as $j): ?>
    <script type="text/javascript" src="<?php print $j; ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

<?php if( ! empty($inline_js)): ?>
  <?php foreach($inline_js as $j): ?>
  <script>
    $(function() {
      <?php echo $j ?>
    });
  </script>
  <?php endforeach; ?>
<?php endif; ?>

<?php if( ! empty($js)): ?>
  <?php foreach($js as $j): ?>
    <script src="/assets/<?php echo $j ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

<script type="text/javascript" src="/assets/js/common.js"></script>

</body>
</html>
