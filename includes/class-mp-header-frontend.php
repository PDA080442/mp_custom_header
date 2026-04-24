<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MP_Header_Frontend {

	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {
		$opts = MP_Header_Settings::get();

		if ( ! empty( $opts['load_fonts'] ) ) {
			wp_enqueue_style(
				'mp-header-fonts',
				'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Outfit:wght@400;500;600&display=swap',
				array(),
				null
			);
		}

		wp_enqueue_style(
			'mp-header',
			MP_HEADER_URL . 'assets/css/mp-header.css',
			array(),
			MP_HEADER_VERSION
		);

		$css  = ':root{';
		$css .= '--mph-pill-from:' . $opts['pill_bg_from'] . ';';
		$css .= '--mph-pill-to:'   . $opts['pill_bg_to']   . ';';
		$css .= '--mph-stroke:'    . $opts['stroke_color'] . ';';
		$css .= '--mph-text:'      . $opts['text_color']   . ';';
		$css .= '--mph-soft:'      . $opts['soft_color']   . ';';
		$css .= '--mph-accent:'    . $opts['accent_color'] . ';';
		$css .= '--mph-panel-bg:'  . $opts['panel_bg']     . ';';
		if ( ! empty( $opts['strip_bg_enabled'] ) ) {
			$strip_bg = trim( (string) $opts['strip_bg'] );
			if ( $strip_bg !== '' ) {
				$css .= '--mph-strip-bg:' . $strip_bg . ';';
			}
		} else {
			$css .= '--mph-strip-bg:transparent;';
		}
		$css .= '--mph-w-desktop:' . absint( $opts['width_desktop'] ) . 'px;';
		$css .= '--mph-w-tablet:'  . absint( $opts['width_tablet'] )  . 'px;';
		$css .= '--mph-w-mobile:'  . absint( $opts['width_mobile'] )  . 'px;';
		$css .= '--mph-btn-size:'  . absint( $opts['button_size'] )   . 'px;';
		$css .= '}';

		/* Отступы и обводка капсулы */
		$mt = absint( $opts['margin_top'] );
		$mb = absint( $opts['margin_bottom'] );
		$bw = absint( $opts['strip_border_width'] );
		$bc = $opts['strip_border_color'];
		$bp = $opts['strip_border_pos'];

		$pill_css = '.mp-header .mp-header__pill{';
		$pill_css .= 'margin-top:' . $mt . 'px;';
		$pill_css .= 'margin-bottom:' . $mb . 'px;';
		if ( $bw > 0 && $bp !== 'none' ) {
			if ( $bp === 'all' ) {
				$pill_css .= 'border:' . $bw . 'px solid ' . $bc . ' !important;';
			} else {
				$pill_css .= 'border:0 !important;';
				if ( $bp === 'top' || $bp === 'both' ) {
					$pill_css .= 'border-top:' . $bw . 'px solid ' . $bc . ' !important;';
				}
				if ( $bp === 'bottom' || $bp === 'both' ) {
					$pill_css .= 'border-bottom:' . $bw . 'px solid ' . $bc . ' !important;';
				}
			}
		}
		$pill_css .= '}';
		$css .= $pill_css;

		/* Стили кнопки меню */
		$btn_style = $opts['button_style'];
		$btn_size  = absint( $opts['button_size'] );
		$btn_bw    = absint( $opts['button_border_width'] );
		$btn_rule  = '.mp-header .mp-header__menu-btn';
		$css .= $btn_rule . ',' . $btn_rule . ':hover,' . $btn_rule . ':focus{';
		$css .= 'background:' . $opts['button_bg'] . ' !important;';
		$css .= 'color:' . $opts['button_icon_color'] . ' !important;';
		if ( $btn_bw > 0 ) {
			$css .= 'border:' . $btn_bw . 'px solid ' . $opts['button_border_color'] . ' !important;';
		} else {
			$css .= 'border:0 !important;';
		}
		$css .= '}';
		if ( $btn_style === 'icon' ) {
			$css .= '.mp-header .mp-header__menu-btn--icon{';
			$css .= 'width:' . $btn_size . 'px !important;';
			$css .= 'height:' . $btn_size . 'px !important;';
			$css .= 'padding:0 !important;';
			$css .= 'border-radius:999px !important;';
			$css .= '}';
		}

		$bw = absint( $opts['burger_width'] );
		$bh = absint( $opts['burger_height'] );
		$bt = absint( $opts['burger_thickness'] );
		$css .= '.mp-header .mp-header__burger{';
		$css .= 'width:' . $bw . 'px !important;';
		$css .= 'height:' . $bh . 'px !important;';
		$css .= '}';
		$css .= '.mp-header .mp-header__burger > span{';
		$css .= 'height:' . $bt . 'px !important;';
		$css .= 'background:' . $opts['button_icon_color'] . ' !important;';
		$css .= 'display:block !important;';
		$css .= 'width:100% !important;';
		$css .= '}';

		/* Стили иконок телефона и аккаунта */
		$ph_size = absint( $opts['phone_size'] );
		$ph_bw   = absint( $opts['phone_border_width'] );
		$ph_rule = '.mp-header .mp-header__phone';
		$css .= $ph_rule . ',' . $ph_rule . ':hover,' . $ph_rule . ':focus{';
		$css .= 'width:' . $ph_size . 'px !important;';
		$css .= 'height:' . $ph_size . 'px !important;';
		$css .= 'background:' . $opts['phone_bg'] . ' !important;';
		if ( $ph_bw > 0 ) {
			$css .= 'border:' . $ph_bw . 'px solid ' . $opts['phone_border_color'] . ' !important;';
		} else {
			$css .= 'border:0 !important;';
		}
		$css .= 'color:' . $opts['phone_icon_color'] . ' !important;';
		$css .= '}';

		$ac_size = absint( $opts['account_size'] );
		$ac_bw   = absint( $opts['account_border_width'] );
		$ac_rule = '.mp-header .mp-header__account';
		$css .= $ac_rule . ',' . $ac_rule . ':hover,' . $ac_rule . ':focus{';
		$css .= 'width:' . $ac_size . 'px !important;';
		$css .= 'height:' . $ac_size . 'px !important;';
		$css .= 'background:' . $opts['account_bg'] . ' !important;';
		if ( $ac_bw > 0 ) {
			$css .= 'border:' . $ac_bw . 'px solid ' . $opts['account_border_color'] . ' !important;';
		} else {
			$css .= 'border:0 !important;';
		}
		$css .= 'color:' . $opts['account_icon_color'] . ' !important;';
		$css .= '}';

		/* Ширина и выравнивание выпадающего меню */
		$mw = absint( $opts['menu_width'] );
		if ( $mw > 0 ) {
			$align = $opts['menu_align'];
			$menu_css = '.mp-header .mp-header__menu{';
			$menu_css .= 'width:' . $mw . 'px;';
			if ( $align === 'left' ) {
				$menu_css .= 'left:0;right:auto;margin-left:0;';
			} elseif ( $align === 'right' ) {
				$menu_css .= 'right:0;left:auto;margin-left:0;';
			} else {
				$menu_css .= 'left:50%;right:auto;margin-left:-' . intval( $mw / 2 ) . 'px;';
			}
			$menu_css .= '}';
			/* На мобильных ширину сбрасываем — там узко */
			$menu_css .= '@media (max-width:640px){.mp-header .mp-header__menu{width:auto;left:0;right:0;margin-left:0;}}';
			$css .= $menu_css;
		}

		wp_add_inline_style( 'mp-header', $css );

		wp_enqueue_script(
			'mp-header',
			MP_HEADER_URL . 'assets/js/mp-header.js',
			array(),
			MP_HEADER_VERSION,
			true
		);
		wp_localize_script( 'mp-header', 'MP_HEADER_CFG', array(
			'closeOnClick'   => ! empty( $opts['close_on_click'] ),
			'closeOnOutside' => ! empty( $opts['close_on_outside'] ),
			'scrollFloat'    => ! empty( $opts['scroll_float'] ),
			'scrollOffset'   => absint( $opts['scroll_offset'] ),
			'hideOnScroll'   => ! empty( $opts['hide_on_scroll'] ),
			'hideDelta'      => absint( $opts['hide_delta'] ),
			'hideMinTop'     => absint( $opts['hide_min_top'] ),
			'stickyTop'      => ! empty( $opts['sticky_top'] ),
		) );
	}
}
