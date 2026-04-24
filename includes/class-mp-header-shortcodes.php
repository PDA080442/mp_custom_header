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
		$opts  = MP_Header_Settings::get();
		$icons = self::phone_icons();
		$key   = isset( $opts['phone_icon'] ) ? $opts['phone_icon'] : self::default_phone_icon();
		if ( ! isset( $icons[ $key ] ) ) {
			$key = self::default_phone_icon();
		}
		$svg = $icons[ $key ]['svg'];
		return preg_replace( '/<svg\b/', '<svg class="mp-header__icon"', $svg, 1 );
	}

	/**
	 * Ключ иконки телефона, используемый по умолчанию.
	 */
	public static function default_phone_icon() {
		return 'rounded';
	}

	/**
	 * Реестр иконок телефона для выбора в админке.
	 * Каждый элемент — label (для карточки) и svg (полная разметка <svg>…</svg>).
	 */
	public static function phone_icons() {
		return array(
			'outline' => array(
				'label' => __( 'Контур, классика', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6.3 6.3l1.3-1.3a2 2 0 0 1 2.1-.5c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2.1z"/></svg>',
			),
			'outline-thin' => array(
				'label' => __( 'Контур, тонкая', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6.3 6.3l1.3-1.3a2 2 0 0 1 2.1-.5c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2.1z"/></svg>',
			),
			'solid' => array(
				'label' => __( 'Заливка, классика', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .57 3.6 1 1 0 0 1-.25 1L6.6 10.8z"/></svg>',
			),
			'rounded' => array(
				'label' => __( 'Скруглённая, объёмная', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.3 2.1a3 3 0 0 0-4.2 0L2 3.2a4 4 0 0 0-1 3.8A20 20 0 0 0 17 23a4 4 0 0 0 3.8-1l1.1-1.1a3 3 0 0 0 0-4.2l-2.7-2.7a3 3 0 0 0-4.2 0 2 2 0 0 1-2.6.2 16 16 0 0 1-3.6-3.6 2 2 0 0 1 .2-2.6 3 3 0 0 0 0-4.2L7.3 2.1z"/></svg>',
			),
			'chunky' => array(
				'label' => __( 'Толстая трубка', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.4 14.2l-2.8-1.1a2.4 2.4 0 0 0-2.7.7l-.6.7a10.8 10.8 0 0 1-4.8-4.8l.7-.6a2.4 2.4 0 0 0 .7-2.7L8.8 3.6A2.4 2.4 0 0 0 6 2l-2.2.7A2.8 2.8 0 0 0 2 5.6a19 19 0 0 0 16.4 16.4 2.8 2.8 0 0 0 2.9-1.8l.7-2.2a2.4 2.4 0 0 0-1.6-2.8z"/></svg>',
			),
			'circle' => array(
				'label' => __( 'В круге', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="currentColor"/><path d="M15.9 15.8l-1.2-.3a.9.9 0 0 0-.9.2l-.8.8a7.4 7.4 0 0 1-3.5-3.5l.8-.8a.9.9 0 0 0 .2-.9l-.3-1.2a.9.9 0 0 0-.9-.7H8a.9.9 0 0 0-.9 1 9.5 9.5 0 0 0 8 8 .9.9 0 0 0 1-.9v-1.3a.9.9 0 0 0-.2-.4z" fill="#fff"/></svg>',
			),
			'square' => array(
				'label' => __( 'В скруглённом квадрате', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" fill="currentColor"/><path d="M15.9 15.8l-1.2-.3a.9.9 0 0 0-.9.2l-.8.8a7.4 7.4 0 0 1-3.5-3.5l.8-.8a.9.9 0 0 0 .2-.9l-.3-1.2a.9.9 0 0 0-.9-.7H8a.9.9 0 0 0-.9 1 9.5 9.5 0 0 0 8 8 .9.9 0 0 0 1-.9v-1.3a.9.9 0 0 0-.2-.4z" fill="#fff"/></svg>',
			),
			'ring' => array(
				'label' => __( 'Со звуковыми волнами', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 15.9v2.8a2 2 0 0 1-2.2 2A17.8 17.8 0 0 1 2.1 5.2 2 2 0 0 1 4.1 3h2.7a2 2 0 0 1 2 1.7c.1.8.3 1.6.6 2.4a2 2 0 0 1-.5 2.1L8 10a14.4 14.4 0 0 0 5.7 5.7l.9-.9a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.4.6a2 2 0 0 1 1.9 2z"/><path d="M15 4a4 4 0 0 1 4 4M15 1a7 7 0 0 1 7 7"/></svg>',
			),
			'handset' => array(
				'label' => __( 'Старая трубка', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.4 3.1a3 3 0 0 1 4.2 0l1.4 1.4a3 3 0 0 1 0 4.2L9.3 9.4a.8.8 0 0 0-.1 1 13 13 0 0 0 4.4 4.4.8.8 0 0 0 1-.1l.7-.7a3 3 0 0 1 4.2 0l1.4 1.4a3 3 0 0 1 0 4.2l-1 1c-1.6 1.6-4.1 2-6.3.8A22 22 0 0 1 2.3 10.3c-1.1-2.1-.8-4.7.8-6.3l1.3-.9z"/></svg>',
			),
			'bubble' => array(
				'label' => __( 'С облаком сообщения', 'mp-header' ),
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2.5h-11A2.5 2.5 0 0 0 7 5v6.5A2.5 2.5 0 0 0 9.5 14H11v3l4-3h5.5A2.5 2.5 0 0 0 23 11.5V5a2.5 2.5 0 0 0-2.5-2.5z" fill="currentColor" opacity=".35"/><path d="M6.3 10.3a13 13 0 0 0 5.4 5.4l1.4-1.4a1 1 0 0 1 1-.2c.9.3 1.8.5 2.7.5a1 1 0 0 1 1 1v2.3a1 1 0 0 1-1 1A15 15 0 0 1 2.1 3.9a1 1 0 0 1 1-1h2.3a1 1 0 0 1 1 1c0 .9.2 1.8.5 2.7a1 1 0 0 1-.2 1L5.3 8.9a1 1 0 0 0 0 1.4z" fill="currentColor"/></svg>',
			),
		);
	}

	private function icon_user() {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true" class="mp-header__icon"><circle cx="12" cy="8" r="3.5"/><path d="M5 20.5c1.2-3.2 3.6-4.5 7-4.5s5.8 1.3 7 4.5"/></svg>';
	}
}
