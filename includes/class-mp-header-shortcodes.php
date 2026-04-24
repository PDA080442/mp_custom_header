<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MP_Header_Shortcodes {

	public function init() {
		add_shortcode( 'mp_header', array( $this, 'render' ) );
	}

	public function render( $atts = array() ) {
		$opts = MP_Header_Settings::get();

		$atts = shortcode_atts( array(
			'class' => '',
		), is_array( $atts ) ? $atts : array(), 'mp_header' );

		$root_classes = 'mp-header';
		if ( ! empty( $opts['sticky_top'] ) ) {
			$root_classes .= ' is-sticky';
		}
		if ( ! empty( $opts['scroll_float'] ) ) {
			$root_classes .= ' mp-header--float';
		}
		if ( ! empty( $atts['class'] ) ) {
			$root_classes .= ' ' . sanitize_html_class( $atts['class'] );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $root_classes ); ?>">
			<div class="mp-header__wrap">
				<div class="mp-header__shell">
					<div class="mp-header__pill">

						<div class="mp-header__left">
							<?php
							$btn_style = $opts['button_style'];
							$btn_class = 'mp-header__menu-btn mp-header__menu-btn--' . $btn_style;
							?>
							<button type="button" class="<?php echo esc_attr( $btn_class ); ?>" aria-expanded="false" aria-controls="mp-header-menu" aria-label="<?php echo esc_attr( $opts['button_text'] ); ?>">
								<?php if ( $btn_style !== 'text' ) : ?>
									<span class="mp-header__burger" aria-hidden="true">
										<span></span><span></span><span></span>
									</span>
								<?php endif; ?>
								<?php if ( $btn_style !== 'icon' ) : ?>
									<span class="mp-header__menu-btn-text"><?php echo esc_html( $opts['button_text'] ); ?></span>
								<?php endif; ?>
							</button>

							<?php if ( ! empty( $opts['phone1_tel'] ) || ! empty( $opts['phone2_tel'] ) ) : ?>
							<div class="mp-header__phone" tabindex="0" aria-haspopup="true">
								<?php echo $this->icon_phone(); ?>
								<div class="mp-header__phone-pop" role="tooltip">
									<?php if ( ! empty( $opts['phones_hint'] ) ) : ?>
										<span class="mp-header__phone-hint"><?php echo esc_html( $opts['phones_hint'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $opts['phone1_tel'] ) ) : ?>
										<a href="tel:<?php echo esc_attr( $opts['phone1_tel'] ); ?>"><?php echo esc_html( $opts['phone1_label'] ?: $opts['phone1_tel'] ); ?></a>
									<?php endif; ?>
									<?php if ( ! empty( $opts['phone2_tel'] ) ) : ?>
										<a href="tel:<?php echo esc_attr( $opts['phone2_tel'] ); ?>"><?php echo esc_html( $opts['phone2_label'] ?: $opts['phone2_tel'] ); ?></a>
									<?php endif; ?>
								</div>
							</div>
							<?php endif; ?>
						</div>

						<div class="mp-header__center">
							<?php if ( ! empty( $opts['logo_url'] ) ) : ?>
								<a class="mp-header__logo" href="<?php echo esc_url( $opts['home_url'] ); ?>">
									<img src="<?php echo esc_url( $opts['logo_url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
								</a>
							<?php endif; ?>
						</div>

						<div class="mp-header__right">
							<a class="mp-header__account" href="<?php echo esc_url( $opts['account_url'] ); ?>" aria-label="<?php esc_attr_e( 'Мой аккаунт', 'mp-header' ); ?>">
								<?php echo $this->icon_user(); ?>
							</a>
						</div>

					</div>

					<nav class="mp-header__menu" id="mp-header-menu" aria-label="<?php esc_attr_e( 'Меню', 'mp-header' ); ?>">
						<div class="mp-header__menu-inner">
							<?php $this->render_column( $opts['column1_title'], $opts['column1_menu'] ); ?>
							<?php $this->render_column( $opts['column2_title'], $opts['column2_menu'] ); ?>
						</div>
					</nav>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_column( $title, $menu_id ) {
		if ( empty( $menu_id ) ) {
			return;
		}
		echo '<div class="mp-header__menu-col">';
		if ( ! empty( $title ) ) {
			echo '<h2 class="mp-header__menu-title">' . esc_html( $title ) . '</h2>';
		}
		wp_nav_menu( array(
			'menu'            => (int) $menu_id,
			'container'       => false,
			'menu_class'      => 'mp-header__menu-list',
			'depth'           => 0,
			'fallback_cb'     => '__return_empty_string',
		) );
		echo '</div>';
	}

	private function icon_phone() {
		return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="mp-header__icon"><path d="M7.3 2.1a3 3 0 0 0-4.2 0L2 3.2a4 4 0 0 0-1 3.8A20 20 0 0 0 17 23a4 4 0 0 0 3.8-1l1.1-1.1a3 3 0 0 0 0-4.2l-2.7-2.7a3 3 0 0 0-4.2 0 2 2 0 0 1-2.6.2 16 16 0 0 1-3.6-3.6 2 2 0 0 1 .2-2.6 3 3 0 0 0 0-4.2L7.3 2.1z"/></svg>';
	}

	private function icon_user() {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true" class="mp-header__icon"><circle cx="12" cy="8" r="3.5"/><path d="M5 20.5c1.2-3.2 3.6-4.5 7-4.5s5.8 1.3 7 4.5"/></svg>';
	}
}
