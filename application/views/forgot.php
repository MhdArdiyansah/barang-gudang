
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
							<h1 class="h4 text-gray-900 mb-4">Barang-Gudang</h1>
								<h1 class="h4 text-gray-900 mb-4">Forgot Password Page</h1>
							</div>
							<?= $this->session->flashdata('flash'); ?>
							<form class="user" action="<?= base_url(); ?>login/forgot" method="post">
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