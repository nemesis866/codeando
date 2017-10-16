<?php
/************************************************
Template para la zona de usuario libre de la
plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

class Template
{
	// Muestra un div con las redes sociales para poder compartir
	public function mostrar_redes()
	{
		$social = new Social();
		// id="social" al div principal
		?>
		<div>
			<div class="social">
				<?php $social->button_gplus('standard'); ?>
			</div>
			<div class="social">
				<?php $social->button_twitter_share('large'); ?>
			</div>
			<div class="social">
				<?php $social->button_twitter_follow('codeando_org'); ?>
			</div>
			<div class="social">
				<?php $social->button_fb_share(); ?>
			</div>
		</div>
		<?php
	}
	// Muestra el header de la plantilla del email
	public function email_header($content)
	{
		return "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
			<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			<title>Codeando.org</title>
			<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
		</head>
		<body>
		<div>
			<div style='background-color: #55ACEE;padding: 30px 0;text-align: center;'>
				<img src='https://lh3.googleusercontent.com/-6LGASJ9n7XM/Ve4o1JvhJoI/AAAAAAAACKg/EMt7SghuOI0/s450-Ic42/Captura.PNG'>
			</div>
			<div style='font-size: 18px;font-family: arial, helvetica;padding: 15px;'>
				$content";
	}
	// Muestra el footer de la plantilla de email
	public function email_footer($site_name)
	{
		return "</div>
			<div style='background: #222;color: #ccc;text-align: center;padding: 30px 0;'>
				<hr style='width:90%;background-color:#ccc;'>
				<p>Favor de no responder este email ya que se genera de forma automatica y no tendra respuesta alguna.</p>
				<p>Power by <a href='http://codeando.org' style='color:#ccc;'>$site_name</a></p>
			</div>
		</div>
		</body>
		</html>";
	}
}