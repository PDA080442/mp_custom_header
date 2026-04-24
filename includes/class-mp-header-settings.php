<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MP_Header_Settings {

	public static function defaults() {
		return array(
			/* Кнопка «Меню» */
			'button_text'         => 'Меню',
			'button_style'        => 'icon',
			'button_size'         => 44,
			'button_bg'           => '#1c1812',
			'button_icon_color'   => '#f8f4ea',
			'button_border_color' => '#1c1812',
			'button_border_width' => 0,
			'burger_width'        => 20,
			'burger_height'       => 14,
			'burger_thickness'    => 2,

			/* Колонки меню */
			'column1_title'      => 'Каталог',
			'column1_menu'       => 0,
			'column2_title'      => 'Инфо',
			'column2_menu'       => 0,

			/* Телефоны */
			'phones_hint'        => 'Телефоны',
			'phone1_label'       => '8 (800) 250-28-32',
			'phone1_tel'         => '+78002502832',
			'phone2_label'       => '8 (985) 096-47-77',
			'phone2_tel'         => '+79850964777',

			/* Логотип / ссылки */
			'logo_url'           => '',
			'home_url'           => '/',
			'account_url'        => '/my-account/',

			/* Цвета и вид */
			'pill_bg_from'       => '#fffef9',
			'pill_bg_to'         => '#f5f0e6',
			'stroke_color'       => '#e5dcc8',
			'text_color'         => '#1c1812',
			'soft_color'         => '#5c5345',
			'accent_color'       => '#8b7355',
			'panel_bg'           => '#fffef9',
			'strip_bg_enabled'   => 0,
			'strip_bg'           => '#ffffff',

			/* Обводка капсулы */
			'strip_border_color' => '#000000',
			'strip_border_width' => 1,
			'strip_border_pos'   => 'all',

			/* Внешние отступы капсулы (внутри полосы хедера) */
			'margin_top'         => 16,
			'margin_bottom'      => 16,

			/* Поведение */
			'sticky_top'         => 0,
			'scroll_float'       => 0,
			'scroll_offset'      => 8,
			'close_on_click'     => 1,
			'close_on_outside'   => 1,
			'load_fonts'         => 1,

			/* Ширины контейнера */
			'width_desktop'      => 1020,
			'width_tablet'       => 820,
			'width_mobile'       => 390,

			/* Выпадающее меню */
			'menu_width'         => 0,
			'menu_align'         => 'center',

			/* Кнопка телефона */
			'phone_icon'         => 'rounded',
			'phone_bg'           => '#ffffff',
			'phone_border_color' => '#e5dcc8',
			'phone_border_width' => 1,
			'phone_icon_color'   => '#1c1812',
			'phone_size'         => 44,

			/* Кнопка аккаунта */
			'account_bg'           => '#ffffff',
			'account_border_color' => '#e5dcc8',
			'account_border_width' => 1,
			'account_icon_color'   => '#1c1812',
			'account_size'         => 44,
		);
	}

	public static function get() {
		$opts = get_option( MP_HEADER_OPTION, array() );
		if ( ! is_array( $opts ) ) {
			$opts = array();
		}
		return array_merge( self::defaults(), $opts );
	}

	public static function sanitize( $input ) {
		$defaults = self::defaults();
		$output   = self::get();
		if ( ! is_array( $input ) ) {
			return $output;
		}

		$output['button_text']      = sanitize_text_field( $input['button_text'] ?? $defaults['button_text'] );
		$btn_style = sanitize_text_field( $input['button_style'] ?? $defaults['button_style'] );
		$output['button_style']     = in_array( $btn_style, array( 'text', 'icon', 'both' ), true ) ? $btn_style : $defaults['button_style'];
		$output['button_size']      = self::range( $input['button_size'] ?? 0, 28, 80, $defaults['button_size'] );
		$output['button_bg']           = self::sanitize_color( $input['button_bg'] ?? $defaults['button_bg'], $defaults['button_bg'] );
		$output['button_icon_color']   = self::sanitize_color( $input['button_icon_color'] ?? $defaults['button_icon_color'], $defaults['button_icon_color'] );
		$output['button_border_color'] = self::sanitize_color( $input['button_border_color'] ?? $defaults['button_border_color'], $defaults['button_border_color'] );
		$output['button_border_width'] = self::range( $input['button_border_width'] ?? 0, 0, 10, $defaults['button_border_width'] );
		$output['burger_width']        = self::range( $input['burger_width'] ?? 0, 10, 40, $defaults['burger_width'] );
		$output['burger_height']       = self::range( $input['burger_height'] ?? 0, 8, 30, $defaults['burger_height'] );
		$output['burger_thickness']    = self::range( $input['burger_thickness'] ?? 0, 1, 6, $defaults['burger_thickness'] );

		$output['column1_title']    = sanitize_text_field( $input['column1_title'] ?? $defaults['column1_title'] );
		$output['column1_menu']     = absint( $input['column1_menu'] ?? 0 );
		$output['column2_title']    = sanitize_text_field( $input['column2_title'] ?? $defaults['column2_title'] );
		$output['column2_menu']     = absint( $input['column2_menu'] ?? 0 );

		$output['phones_hint']      = sanitize_text_field( $input['phones_hint'] ?? $defaults['phones_hint'] );
		$output['phone1_label']     = sanitize_text_field( $input['phone1_label'] ?? '' );
		$output['phone1_tel']       = self::sanitize_tel( $input['phone1_tel'] ?? '' );
		$output['phone2_label']     = sanitize_text_field( $input['phone2_label'] ?? '' );
		$output['phone2_tel']       = self::sanitize_tel( $input['phone2_tel'] ?? '' );

		$output['logo_url']         = esc_url_raw( $input['logo_url'] ?? '' );
		$output['home_url']         = esc_url_raw( $input['home_url'] ?? '/' );
		$output['account_url']      = esc_url_raw( $input['account_url'] ?? '/my-account/' );

		foreach ( array( 'pill_bg_from', 'pill_bg_to', 'stroke_color', 'text_color', 'soft_color', 'accent_color', 'panel_bg' ) as $k ) {
			$output[ $k ] = self::sanitize_color( $input[ $k ] ?? $defaults[ $k ], $defaults[ $k ] );
		}
		$output['strip_bg_enabled'] = empty( $input['strip_bg_enabled'] ) ? 0 : 1;
		$strip = trim( (string) ( $input['strip_bg'] ?? '' ) );
		$output['strip_bg'] = $strip === '' ? $defaults['strip_bg'] : self::sanitize_color( $strip, $defaults['strip_bg'] );

		$output['strip_border_color'] = self::sanitize_color( $input['strip_border_color'] ?? $defaults['strip_border_color'], $defaults['strip_border_color'] );
		$output['strip_border_width'] = self::range( $input['strip_border_width'] ?? 0, 0, 10, $defaults['strip_border_width'] );
		$pos = sanitize_text_field( $input['strip_border_pos'] ?? $defaults['strip_border_pos'] );
		$output['strip_border_pos']   = in_array( $pos, array( 'none', 'all', 'top', 'bottom', 'both' ), true ) ? $pos : $defaults['strip_border_pos'];

		$output['margin_top']    = self::range( $input['margin_top']    ?? 0, 0, 300, $defaults['margin_top'] );
		$output['margin_bottom'] = self::range( $input['margin_bottom'] ?? 0, 0, 300, $defaults['margin_bottom'] );

		$output['sticky_top']       = empty( $input['sticky_top'] ) ? 0 : 1;
		$output['scroll_float']     = empty( $input['scroll_float'] ) ? 0 : 1;
		$output['scroll_offset']    = self::range( $input['scroll_offset'] ?? 0, 0, 600, $defaults['scroll_offset'] );
		$output['close_on_click']   = empty( $input['close_on_click'] ) ? 0 : 1;
		$output['close_on_outside'] = empty( $input['close_on_outside'] ) ? 0 : 1;
		$output['load_fonts']       = empty( $input['load_fonts'] ) ? 0 : 1;

		$output['width_desktop']    = self::range( $input['width_desktop'] ?? 0, 600, 1600, $defaults['width_desktop'] );
		$output['width_tablet']     = self::range( $input['width_tablet']  ?? 0, 500, 1200, $defaults['width_tablet'] );
		$output['width_mobile']     = self::range( $input['width_mobile']  ?? 0, 300, 600,  $defaults['width_mobile'] );

		$output['menu_width']       = self::range( $input['menu_width']    ?? 0, 0, 1600, $defaults['menu_width'] );
		$align = sanitize_text_field( $input['menu_align'] ?? $defaults['menu_align'] );
		$output['menu_align']       = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : $defaults['menu_align'];

		foreach ( array( 'phone', 'account' ) as $prefix ) {
			$output[ $prefix . '_bg' ]           = self::sanitize_color( $input[ $prefix . '_bg' ] ?? $defaults[ $prefix . '_bg' ], $defaults[ $prefix . '_bg' ] );
			$output[ $prefix . '_border_color' ] = self::sanitize_color( $input[ $prefix . '_border_color' ] ?? $defaults[ $prefix . '_border_color' ], $defaults[ $prefix . '_border_color' ] );
			$output[ $prefix . '_border_width' ] = self::range( $input[ $prefix . '_border_width' ] ?? 0, 0, 10, $defaults[ $prefix . '_border_width' ] );
			$output[ $prefix . '_icon_color' ]   = self::sanitize_color( $input[ $prefix . '_icon_color' ] ?? $defaults[ $prefix . '_icon_color' ], $defaults[ $prefix . '_icon_color' ] );
			$output[ $prefix . '_size' ]         = self::range( $input[ $prefix . '_size' ] ?? 0, 28, 80, $defaults[ $prefix . '_size' ] );
		}

		$icon_key = sanitize_key( $input['phone_icon'] ?? $defaults['phone_icon'] );
		if ( class_exists( 'MP_Header_Shortcodes' ) ) {
			$allowed = array_keys( MP_Header_Shortcodes::phone_icons() );
			if ( ! in_array( $icon_key, $allowed, true ) ) {
				$icon_key = $defaults['phone_icon'];
			}
		}
		$output['phone_icon'] = $icon_key;

		return $output;
	}

	private static function sanitize_tel( $value ) {
		return preg_replace( '/[^0-9+]/', '', (string) $value );
	}

	private static function sanitize_color( $value, $fallback ) {
		$value = trim( (string) $value );
		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
			return $value;
		}
		return $fallback;
	}

	private static function range( $value, $min, $max, $fallback ) {
		$value = absint( $value );
		if ( $value < $min || $value > $max ) {
			return $fallback;
		}
		return $value;
	}
}
