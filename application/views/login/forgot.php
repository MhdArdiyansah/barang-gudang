<!doctype html>
<html lang="en">
  <head>
  	<title><?= $title; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= base_url()?>/assets/fontawesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	</head>
	<body>
	<section class="ftco-section">    
        <div class="container">
            <!-- Outer Row -->
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                                <div class="col-lg">
                                    <div class="p-5">
                                        <div class="text-center">
                                        <!-- <?= $this->session->flashdata('flash'); ?> -->
                                        <h1 class="h4 text-gray-900 mb-4">Employee Recruitment</h1>
                                            <h1 class="h4 text-gray-900 mb-4">Forgot Password Page</h1>
                                        </div>
                                        <?= $this->session->flashdata('flash'); ?>
                                        <form class="user" action="<?= base_url(); ?>register/forgot" method="post">
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-user"
                                                    id="email" name="email" aria-describedby="emailHelp"
                                            placeholder="Enter Email" value="<?= set_value('email'); ?>" ><?= form_error('email', '<small class="text-danger pl-3">', '</small>');?>
                                            </div>
                                        
                                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                                Reset Password
                                        </button>
                                        </form>
                                        <hr> 
                                        <div class="text-center">
                                            <a class="small" href="<?= base_url('login') ?>">Back to Login Page</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</section>

	<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
	</body>
</html>


    