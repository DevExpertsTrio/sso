<?php

namespace BPCSSO\Frontend\Oauth;

class oauth {
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_render_view() { ?>
		<div>Hi you are in oauth.</div>
		<?php
	}

}


