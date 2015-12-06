<div class="modal fade" id="login-modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Login</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger" v-if="hasFeedback" id="alert-form-login">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Oops!&nbsp;</strong>
					<ol v-if="isError">
						<li v-repeat="errorData" v-text="$value"></li>
					</ol>
				</div>
				<form id="form-login" role="form" v-on="submit: sendData">
					<div class="form-group">
						<label for="email" class="control-label sr-only">Email</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-fw fa-envelope-o"></i></span>
							<input
								type="email"
								name="email"
								v-model="formData.email"
								class="form-control"
								id="email"
								placeholder="Your email" required>
						</div>
					</div>
				
					<div class="form-group">
						<label for="password" class="control-label sr-only">Password</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
							<input
								type="password"
								name="password"
								v-model="formData.password"
								class="form-control"
								id="password"
								placeholder="Your password" required>
						</div>
					</div>

					<div class="form-group">
						<div class="checkbox">
							<label for="remember" class="control-label">
								<input
									type="checkbox"
									name="remember"
									v-model="formData.remember"
									id="remember"
									placeholder="Your password">
								Remember me
							</label>
						</div>
					</div>

					<input type="hidden" v-el="token" name="${ csrf_key }" value="${ csrf_token }">
				
					<button type="submit" class="btn btn-danger btn-block" data-loading-text="Signin in...">Login</button>
				</form>
			</div>
			<div class="modal-footer">
				<a href="${ urlFor('register') }" id="button-to-signup">Don't have account yet? &raquo;</a>
			</div>
		</div>
	</div>
</div>