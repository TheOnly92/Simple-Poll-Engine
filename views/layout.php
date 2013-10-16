<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo htmlentities($templateTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSS -->
    <link href="./static/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      .container {
        width: auto;
        max-width: 680px;
      }
    </style>
    <link href="./static/css/bootstrap-responsive.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="./static/js/html5shiv.js"></script>
    <![endif]-->

    <script src="//code.jquery.com/jquery-1.10.1.min.js"></script>
  </head>

  <body>


    <!-- Part 1: Wrap all page content here -->
    <div id="wrap">

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1><?php echo htmlentities($templateTitle); ?></h1>
        </div>
        <?php include(dirname(__FILE__).'/'.$templateFile); ?>
      </div>
    </div>

    <script src="./static/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="./static/js/highcharts.js"></script>
  </body>
</html>
