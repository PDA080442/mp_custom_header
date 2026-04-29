<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MP_Header_Admin {

	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( MP_HEADER_FILE ), array( $this, 'action_links' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'Metaphysica Header', 'mp-header' ),
			__( 'MP Header', 'mp-header' ),
			'manage_options',
			'mp-header',
			array( $this, 'render_page' ),
			'dashicons-menu-alt3',
			60
		);
	}

	public function register_settings() {
		register_setting(
			'mp_header_group',
			MP_HEADER_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'MP_Header_Settings', 'sanitize' ),
				'default'           => MP_Header_Settings::defaults(),
			)
		);
	}

	public function enqueue( $hook ) {
		if ( $hook !== 'toplevel_page_mp-header' ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script(
			'mp-header-admin',
			MP_HEADER_URL . 'assets/js/mp-header-admin.js',
			array( 'jquery', 'wp-color-picker' ),
			MP_HEADER_VERSION,
			true
		);
	}

	public function action_links( $links ) {
		$url  = admin_url( 'admin.php?page=mp-header' );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Настройки', 'mp-header' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$opts  = MP_Header_Settings::get();
		$menus = wp_get_nav_menus();
		$o     = MP_HEADER_OPTION;

		?>
		<style>
			.mp-icon-picker{display:grid;grid-template-columns:repeat(auto-fill,minmax(132px,1fr));gap:10px;max-width:720px;margin:0;padding:0;border:0}
			.mp-icon-picker__item{position:relative;display:flex;flex-direction:column;align-items:center;gap:8px;padding:12px 8px;border:1px solid #dcdcde;border-radius:8px;background:#fff;cursor:pointer;transition:border-color .15s,box-shadow .15s}
			.mp-icon-picker__item:hover{border-color:#2271b1}
			.mp-icon-picker__item input{position:absolute;opacity:0;pointer-events:none}
			.mp-icon-picker__item input:checked + .mp-icon-picker__preview{background:#1c1812;color:#f8f4ea}
			.mp-icon-picker__item:has(input:checked){border-color:#2271b1;box-shadow:0 0 0 2px rgba(34,113,177,.2)}
			.mp-icon-picker__preview{display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:#f6f4ef;color:#1c1812;transition:background .15s,color .15s}
			.mp-icon-picker__preview svg{width:28px;height:28px;display:block}
			.mp-icon-picker__preview img{width:28px;height:28px;display:block;object-fit:contain}
			.mp-icon-picker__label{font-size:12px;line-height:1.3;text-align:center;color:#50575e}
		</style>
		<div class="wrap mp-header-admin">
			<h1><?php esc_html_e( 'Metaphysica Header', 'mp-header' ); ?></h1>
			<p>
				<?php esc_html_e( 'Настройте шапку и вставьте в любую страницу или в шаблон шапки Elementor через виджет «Короткий код»:', 'mp-header' ); ?>
				<code>[mp_header]</code>.
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'mp_header_group' ); ?>

				<h2 class="title"><?php esc_html_e( 'Меню', 'mp-header' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Пункты подтягиваются из «Внешний вид → Меню». Создайте там два меню и выберите их ниже.', 'mp-header' ); ?>
				</p>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="button_style"><?php esc_html_e( 'Стиль кнопки меню', 'mp-header' ); ?></label></th>
						<td>
							<select id="button_style" name="<?php echo esc_attr( $o ); ?>[button_style]">
								<option value="icon" <?php selected( $opts['button_style'], 'icon' ); ?>><?php esc_html_e( 'Только иконка (бургер)', 'mp-header' ); ?></option>
								<option value="text" <?php selected( $opts['button_style'], 'text' ); ?>><?php esc_html_e( 'Только текст', 'mp-header' ); ?></option>
								<option value="both" <?php selected( $opts['button_style'], 'both' ); ?>><?php esc_html_e( 'Иконка + текст', 'mp-header' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="button_text"><?php esc_html_e( 'Текст кнопки', 'mp-header' ); ?></label></th>
						<td>
							<input type="text" id="button_text" name="<?php echo esc_attr( $o ); ?>[button_text]" value="<?php echo esc_attr( $opts['button_text'] ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Используется при стилях «Только текст» и «Иконка + текст», а также как aria-label в режиме «Только иконка».', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="button_size"><?php esc_html_e( 'Размер кнопки в режиме иконки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="button_size" name="<?php echo esc_attr( $o ); ?>[button_size]" value="<?php echo esc_attr( $opts['button_size'] ); ?>" min="28" max="80" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="button_bg"><?php esc_html_e( 'Фон кнопки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="button_bg" name="<?php echo esc_attr( $o ); ?>[button_bg]" value="<?php echo esc_attr( $opts['button_bg'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="button_icon_color"><?php esc_html_e( 'Цвет иконки / текста', 'mp-header' ); ?></label></th>
						<td><input type="text" id="button_icon_color" name="<?php echo esc_attr( $o ); ?>[button_icon_color]" value="<?php echo esc_attr( $opts['button_icon_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="button_border_color"><?php esc_html_e( 'Цвет обводки кнопки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="button_border_color" name="<?php echo esc_attr( $o ); ?>[button_border_color]" value="<?php echo esc_attr( $opts['button_border_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="button_border_width"><?php esc_html_e( 'Толщина обводки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="button_border_width" name="<?php echo esc_attr( $o ); ?>[button_border_width]" value="<?php echo esc_attr( $opts['button_border_width'] ); ?>" min="0" max="10" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="burger_width"><?php esc_html_e( 'Ширина бургера (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="burger_width" name="<?php echo esc_attr( $o ); ?>[burger_width]" value="<?php echo esc_attr( $opts['burger_width'] ); ?>" min="10" max="40" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="burger_height"><?php esc_html_e( 'Высота бургера (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="burger_height" name="<?php echo esc_attr( $o ); ?>[burger_height]" value="<?php echo esc_attr( $opts['burger_height'] ); ?>" min="8" max="30" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="burger_thickness"><?php esc_html_e( 'Толщина полосок бургера (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="burger_thickness" name="<?php echo esc_attr( $o ); ?>[burger_thickness]" value="<?php echo esc_attr( $opts['burger_thickness'] ); ?>" min="1" max="6" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="column1_title"><?php esc_html_e( 'Колонка 1 · заголовок', 'mp-header' ); ?></label></th>
						<td><input type="text" id="column1_title" name="<?php echo esc_attr( $o ); ?>[column1_title]" value="<?php echo esc_attr( $opts['column1_title'] ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th><label for="column1_menu"><?php esc_html_e( 'Колонка 1 · меню', 'mp-header' ); ?></label></th>
						<td>
							<select id="column1_menu" name="<?php echo esc_attr( $o ); ?>[column1_menu]">
								<option value="0"><?php esc_html_e( '— Не показывать —', 'mp-header' ); ?></option>
								<?php foreach ( $menus as $menu ) : ?>
									<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $opts['column1_menu'], $menu->term_id ); ?>>
										<?php echo esc_html( $menu->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="column2_title"><?php esc_html_e( 'Колонка 2 · заголовок', 'mp-header' ); ?></label></th>
						<td><input type="text" id="column2_title" name="<?php echo esc_attr( $o ); ?>[column2_title]" value="<?php echo esc_attr( $opts['column2_title'] ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th><label for="column2_menu"><?php esc_html_e( 'Колонка 2 · меню', 'mp-header' ); ?></label></th>
						<td>
							<select id="column2_menu" name="<?php echo esc_attr( $o ); ?>[column2_menu]">
								<option value="0"><?php esc_html_e( '— Не показывать —', 'mp-header' ); ?></option>
								<?php foreach ( $menus as $menu ) : ?>
									<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $opts['column2_menu'], $menu->term_id ); ?>>
										<?php echo esc_html( $menu->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Телефоны', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="phones_hint"><?php esc_html_e( 'Надпись в попапе', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phones_hint" name="<?php echo esc_attr( $o ); ?>[phones_hint]" value="<?php echo esc_attr( $opts['phones_hint'] ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th><label for="phone1_label"><?php esc_html_e( 'Телефон 1 · подпись', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone1_label" name="<?php echo esc_attr( $o ); ?>[phone1_label]" value="<?php echo esc_attr( $opts['phone1_label'] ); ?>" class="regular-text" placeholder="8 (800) 250-28-32"></td>
					</tr>
					<tr>
						<th><label for="phone1_tel"><?php esc_html_e( 'Телефон 1 · для tel:', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone1_tel" name="<?php echo esc_attr( $o ); ?>[phone1_tel]" value="<?php echo esc_attr( $opts['phone1_tel'] ); ?>" class="regular-text" placeholder="+78002502832"></td>
					</tr>
					<tr>
						<th><label for="phone2_label"><?php esc_html_e( 'Телефон 2 · подпись', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone2_label" name="<?php echo esc_attr( $o ); ?>[phone2_label]" value="<?php echo esc_attr( $opts['phone2_label'] ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th><label for="phone2_tel"><?php esc_html_e( 'Телефон 2 · для tel:', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone2_tel" name="<?php echo esc_attr( $o ); ?>[phone2_tel]" value="<?php echo esc_attr( $opts['phone2_tel'] ); ?>" class="regular-text"></td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Логотип и ссылки', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="logo_url"><?php esc_html_e( 'Логотип', 'mp-header' ); ?></label></th>
						<td>
							<input type="text" id="logo_url" name="<?php echo esc_attr( $o ); ?>[logo_url]" value="<?php echo esc_attr( $opts['logo_url'] ); ?>" class="regular-text mp-logo-url" placeholder="https://...">
							<button type="button" class="button mp-upload-logo"><?php esc_html_e( 'Выбрать из медиатеки', 'mp-header' ); ?></button>
						</td>
					</tr>
					<tr>
						<th><label for="home_url"><?php esc_html_e( 'Ссылка по клику на логотип', 'mp-header' ); ?></label></th>
						<td><input type="text" id="home_url" name="<?php echo esc_attr( $o ); ?>[home_url]" value="<?php echo esc_attr( $opts['home_url'] ); ?>" class="regular-text" placeholder="/"></td>
					</tr>
					<tr>
						<th><label for="account_url"><?php esc_html_e( 'Ссылка иконки аккаунта', 'mp-header' ); ?></label></th>
						<td><input type="text" id="account_url" name="<?php echo esc_attr( $o ); ?>[account_url]" value="<?php echo esc_attr( $opts['account_url'] ); ?>" class="regular-text" placeholder="/my-account/"></td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Цвета', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="pill_bg_from"><?php esc_html_e( 'Капсула · верх градиента', 'mp-header' ); ?></label></th>
						<td><input type="text" id="pill_bg_from" name="<?php echo esc_attr( $o ); ?>[pill_bg_from]" value="<?php echo esc_attr( $opts['pill_bg_from'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="pill_bg_to"><?php esc_html_e( 'Капсула · низ градиента', 'mp-header' ); ?></label></th>
						<td><input type="text" id="pill_bg_to" name="<?php echo esc_attr( $o ); ?>[pill_bg_to]" value="<?php echo esc_attr( $opts['pill_bg_to'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="stroke_color"><?php esc_html_e( 'Обводка', 'mp-header' ); ?></label></th>
						<td><input type="text" id="stroke_color" name="<?php echo esc_attr( $o ); ?>[stroke_color]" value="<?php echo esc_attr( $opts['stroke_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="text_color"><?php esc_html_e( 'Текст (основной)', 'mp-header' ); ?></label></th>
						<td><input type="text" id="text_color" name="<?php echo esc_attr( $o ); ?>[text_color]" value="<?php echo esc_attr( $opts['text_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="soft_color"><?php esc_html_e( 'Текст (приглушённый)', 'mp-header' ); ?></label></th>
						<td><input type="text" id="soft_color" name="<?php echo esc_attr( $o ); ?>[soft_color]" value="<?php echo esc_attr( $opts['soft_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="accent_color"><?php esc_html_e( 'Акцент (заголовки / hover)', 'mp-header' ); ?></label></th>
						<td><input type="text" id="accent_color" name="<?php echo esc_attr( $o ); ?>[accent_color]" value="<?php echo esc_attr( $opts['accent_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="panel_bg"><?php esc_html_e( 'Фон панели меню', 'mp-header' ); ?></label></th>
						<td><input type="text" id="panel_bg" name="<?php echo esc_attr( $o ); ?>[panel_bg]" value="<?php echo esc_attr( $opts['panel_bg'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Фон полосы хедера', 'mp-header' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $o ); ?>[strip_bg_enabled]" value="1" <?php checked( $opts['strip_bg_enabled'], 1 ); ?>>
								<?php esc_html_e( 'Показывать фон у полосы хедера', 'mp-header' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Если выключено — капсула лежит прямо на контенте (полоса прозрачная).', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="strip_bg"><?php esc_html_e( 'Цвет фона полосы', 'mp-header' ); ?></label></th>
						<td><input type="text" id="strip_bg" name="<?php echo esc_attr( $o ); ?>[strip_bg]" value="<?php echo esc_attr( $opts['strip_bg'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Туманный фон (blur)', 'mp-header' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $o ); ?>[fog_enabled]" value="1" <?php checked( $opts['fog_enabled'], 1 ); ?>>
								<?php esc_html_e( 'Включить стеклянный (прозрачный + размытый) эффект', 'mp-header' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="fog_scope"><?php esc_html_e( 'Где применять эффект', 'mp-header' ); ?></label></th>
						<td>
							<select id="fog_scope" name="<?php echo esc_attr( $o ); ?>[fog_scope]">
								<option value="strip" <?php selected( $opts['fog_scope'], 'strip' ); ?>><?php esc_html_e( 'Только полоса хедера', 'mp-header' ); ?></option>
								<option value="pill" <?php selected( $opts['fog_scope'], 'pill' ); ?>><?php esc_html_e( 'Только капсула', 'mp-header' ); ?></option>
								<option value="both" <?php selected( $opts['fog_scope'], 'both' ); ?>><?php esc_html_e( 'Полоса и капсула', 'mp-header' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="fog_tint"><?php esc_html_e( 'Цвет тумана', 'mp-header' ); ?></label></th>
						<td><input type="text" id="fog_tint" name="<?php echo esc_attr( $o ); ?>[fog_tint]" value="<?php echo esc_attr( $opts['fog_tint'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="fog_opacity"><?php esc_html_e( 'Прозрачность тумана (%)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="fog_opacity" name="<?php echo esc_attr( $o ); ?>[fog_opacity]" value="<?php echo esc_attr( $opts['fog_opacity'] ); ?>" min="0" max="100" class="small-text">
							<p class="description"><?php esc_html_e( '0 — полностью прозрачный, 100 — полностью плотный.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="fog_blur"><?php esc_html_e( 'Сила blur (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="fog_blur" name="<?php echo esc_attr( $o ); ?>[fog_blur]" value="<?php echo esc_attr( $opts['fog_blur'] ); ?>" min="0" max="40" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="fog_saturate"><?php esc_html_e( 'Насыщенность (%)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="fog_saturate" name="<?php echo esc_attr( $o ); ?>[fog_saturate]" value="<?php echo esc_attr( $opts['fog_saturate'] ); ?>" min="50" max="250" class="small-text">
							<p class="description"><?php esc_html_e( 'Можно усилить или приглушить цвет контента за блюром.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="strip_border_color"><?php esc_html_e( 'Цвет обводки капсулы', 'mp-header' ); ?></label></th>
						<td><input type="text" id="strip_border_color" name="<?php echo esc_attr( $o ); ?>[strip_border_color]" value="<?php echo esc_attr( $opts['strip_border_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="strip_border_width"><?php esc_html_e( 'Толщина обводки (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="strip_border_width" name="<?php echo esc_attr( $o ); ?>[strip_border_width]" value="<?php echo esc_attr( $opts['strip_border_width'] ); ?>" min="0" max="10" class="small-text">
							<span class="description"><?php esc_html_e( '0 — обводка выключена', 'mp-header' ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="strip_border_pos"><?php esc_html_e( 'Где показывать обводку', 'mp-header' ); ?></label></th>
						<td>
							<select id="strip_border_pos" name="<?php echo esc_attr( $o ); ?>[strip_border_pos]">
								<option value="none"   <?php selected( $opts['strip_border_pos'], 'none' );   ?>><?php esc_html_e( 'Не показывать', 'mp-header' ); ?></option>
								<option value="all"    <?php selected( $opts['strip_border_pos'], 'all' );    ?>><?php esc_html_e( 'По всей капсуле', 'mp-header' ); ?></option>
								<option value="bottom" <?php selected( $opts['strip_border_pos'], 'bottom' ); ?>><?php esc_html_e( 'Только снизу', 'mp-header' ); ?></option>
								<option value="top"    <?php selected( $opts['strip_border_pos'], 'top' );    ?>><?php esc_html_e( 'Только сверху', 'mp-header' ); ?></option>
								<option value="both"   <?php selected( $opts['strip_border_pos'], 'both' );   ?>><?php esc_html_e( 'Сверху и снизу', 'mp-header' ); ?></option>
							</select>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Поведение и размеры', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="margin_top"><?php esc_html_e( 'Отступ капсулы сверху (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="margin_top" name="<?php echo esc_attr( $o ); ?>[margin_top]" value="<?php echo esc_attr( $opts['margin_top'] ); ?>" min="0" max="300" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="margin_bottom"><?php esc_html_e( 'Отступ капсулы снизу (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="margin_bottom" name="<?php echo esc_attr( $o ); ?>[margin_bottom]" value="<?php echo esc_attr( $opts['margin_bottom'] ); ?>" min="0" max="300" class="small-text">
							<p class="description"><?php esc_html_e( 'Отступ от капсулы до верхней и нижней границы полосы хедера (чтобы не примыкало к контенту).', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Прилипание', 'mp-header' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $o ); ?>[sticky_top]" value="1" <?php checked( $opts['sticky_top'], 1 ); ?>>
								<?php esc_html_e( 'Фиксировать хедер сверху (position: fixed)', 'mp-header' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Плавающая капсула при скролле', 'mp-header' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $o ); ?>[scroll_float]" value="1" <?php checked( $opts['scroll_float'], 1 ); ?>>
								<?php esc_html_e( 'При прокрутке страницы скрывать фон полосы — остаётся только капсула, прилипшая сверху', 'mp-header' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Работает вместе с настройкой «Показывать фон у полосы хедера»: наверху страницы фон виден, при скролле пропадает.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="scroll_offset"><?php esc_html_e( 'Порог срабатывания (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="scroll_offset" name="<?php echo esc_attr( $o ); ?>[scroll_offset]" value="<?php echo esc_attr( $opts['scroll_offset'] ); ?>" min="0" max="600" class="small-text">
							<p class="description"><?php esc_html_e( 'Через сколько пикселей прокрутки включить плавающий режим.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Скрывать при прокрутке вниз', 'mp-header' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $o ); ?>[hide_on_scroll]" value="1" <?php checked( $opts['hide_on_scroll'], 1 ); ?>>
								<?php esc_html_e( 'Прячем хедер при прокрутке вниз и показываем обратно при прокрутке вверх', 'mp-header' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Работает при включённом «Прилипание» или «Плавающая капсула при скролле». Когда открыто меню — хедер не прячется.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="hide_delta"><?php esc_html_e( 'Чувствительность (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="hide_delta" name="<?php echo esc_attr( $o ); ?>[hide_delta]" value="<?php echo esc_attr( $opts['hide_delta'] ); ?>" min="0" max="200" class="small-text">
							<p class="description"><?php esc_html_e( 'На сколько пикселей нужно прокрутить, чтобы сменилось состояние. Больше значение — меньше «дёрганий».', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="hide_min_top"><?php esc_html_e( 'Не прятать до прокрутки на (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="hide_min_top" name="<?php echo esc_attr( $o ); ?>[hide_min_top]" value="<?php echo esc_attr( $opts['hide_min_top'] ); ?>" min="0" max="1000" class="small-text">
							<p class="description"><?php esc_html_e( 'Пока страница проскроллена меньше этого значения — хедер не скрывается. Удобно, чтобы на самом верху он всегда был виден.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Закрытие меню', 'mp-header' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[close_on_click]" value="1" <?php checked( $opts['close_on_click'], 1 ); ?>> <?php esc_html_e( 'Закрывать при клике по пункту', 'mp-header' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[close_on_outside]" value="1" <?php checked( $opts['close_on_outside'], 1 ); ?>> <?php esc_html_e( 'Закрывать при клике вне меню', 'mp-header' ); ?></label>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Шрифты', 'mp-header' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[load_fonts]" value="1" <?php checked( $opts['load_fonts'], 1 ); ?>> <?php esc_html_e( 'Подгружать Google Fonts (Outfit + Cormorant Garamond)', 'mp-header' ); ?></label>
						</td>
					</tr>
					<tr>
						<th><label for="width_desktop"><?php esc_html_e( 'Ширина, десктоп (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="width_desktop" name="<?php echo esc_attr( $o ); ?>[width_desktop]" value="<?php echo esc_attr( $opts['width_desktop'] ); ?>" min="600" max="1600" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="width_tablet"><?php esc_html_e( 'Ширина, планшет (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="width_tablet" name="<?php echo esc_attr( $o ); ?>[width_tablet]" value="<?php echo esc_attr( $opts['width_tablet'] ); ?>" min="500" max="1200" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="width_mobile"><?php esc_html_e( 'Ширина, мобильная (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="width_mobile" name="<?php echo esc_attr( $o ); ?>[width_mobile]" value="<?php echo esc_attr( $opts['width_mobile'] ); ?>" min="300" max="600" class="small-text"></td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Иконки: телефон', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><?php esc_html_e( 'Стиль иконки', 'mp-header' ); ?></th>
						<td>
							<?php
							$phone_icons   = MP_Header_Shortcodes::phone_icons();
							$current_icon  = isset( $opts['phone_icon'] ) ? $opts['phone_icon'] : MP_Header_Shortcodes::default_phone_icon();
							if ( ! isset( $phone_icons[ $current_icon ] ) ) {
								$current_icon = MP_Header_Shortcodes::default_phone_icon();
							}
							$allowed_svg = array(
								'svg'       => array( 'xmlns' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true, 'class' => true ),
								'path'      => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'opacity' => true ),
								'circle'    => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ),
								'rect'      => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true ),
								'img'       => array( 'src' => true, 'alt' => true, 'class' => true, 'aria-hidden' => true ),
							);
							?>
							<fieldset class="mp-icon-picker">
								<?php foreach ( $phone_icons as $key => $icon ) : ?>
									<label class="mp-icon-picker__item">
										<input type="radio" name="<?php echo esc_attr( $o ); ?>[phone_icon]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current_icon, $key ); ?>>
										<span class="mp-icon-picker__preview"><?php echo wp_kses( $icon['svg'], $allowed_svg ); ?></span>
										<span class="mp-icon-picker__label"><?php echo esc_html( $icon['label'] ); ?></span>
									</label>
								<?php endforeach; ?>
							</fieldset>
							<p class="description"><?php esc_html_e( 'Цвет иконки берётся из настройки «Цвет иконки» ниже.', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="phone_bg"><?php esc_html_e( 'Фон кнопки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone_bg" name="<?php echo esc_attr( $o ); ?>[phone_bg]" value="<?php echo esc_attr( $opts['phone_bg'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="phone_border_color"><?php esc_html_e( 'Цвет обводки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone_border_color" name="<?php echo esc_attr( $o ); ?>[phone_border_color]" value="<?php echo esc_attr( $opts['phone_border_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="phone_border_width"><?php esc_html_e( 'Толщина обводки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="phone_border_width" name="<?php echo esc_attr( $o ); ?>[phone_border_width]" value="<?php echo esc_attr( $opts['phone_border_width'] ); ?>" min="0" max="10" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="phone_icon_color"><?php esc_html_e( 'Цвет иконки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="phone_icon_color" name="<?php echo esc_attr( $o ); ?>[phone_icon_color]" value="<?php echo esc_attr( $opts['phone_icon_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="phone_size"><?php esc_html_e( 'Размер кнопки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="phone_size" name="<?php echo esc_attr( $o ); ?>[phone_size]" value="<?php echo esc_attr( $opts['phone_size'] ); ?>" min="28" max="80" class="small-text"></td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Иконки: аккаунт', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="account_bg"><?php esc_html_e( 'Фон кнопки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="account_bg" name="<?php echo esc_attr( $o ); ?>[account_bg]" value="<?php echo esc_attr( $opts['account_bg'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="account_border_color"><?php esc_html_e( 'Цвет обводки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="account_border_color" name="<?php echo esc_attr( $o ); ?>[account_border_color]" value="<?php echo esc_attr( $opts['account_border_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="account_border_width"><?php esc_html_e( 'Толщина обводки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="account_border_width" name="<?php echo esc_attr( $o ); ?>[account_border_width]" value="<?php echo esc_attr( $opts['account_border_width'] ); ?>" min="0" max="10" class="small-text"></td>
					</tr>
					<tr>
						<th><label for="account_icon_color"><?php esc_html_e( 'Цвет иконки', 'mp-header' ); ?></label></th>
						<td><input type="text" id="account_icon_color" name="<?php echo esc_attr( $o ); ?>[account_icon_color]" value="<?php echo esc_attr( $opts['account_icon_color'] ); ?>" class="mp-color"></td>
					</tr>
					<tr>
						<th><label for="account_size"><?php esc_html_e( 'Размер кнопки (px)', 'mp-header' ); ?></label></th>
						<td><input type="number" id="account_size" name="<?php echo esc_attr( $o ); ?>[account_size]" value="<?php echo esc_attr( $opts['account_size'] ); ?>" min="28" max="80" class="small-text"></td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Выпадающее меню', 'mp-header' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="menu_width"><?php esc_html_e( 'Ширина выпадающего меню (px)', 'mp-header' ); ?></label></th>
						<td>
							<input type="number" id="menu_width" name="<?php echo esc_attr( $o ); ?>[menu_width]" value="<?php echo esc_attr( $opts['menu_width'] ); ?>" min="0" max="1600" class="small-text">
							<p class="description"><?php esc_html_e( '0 — на всю ширину капсулы (по умолчанию).', 'mp-header' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="menu_align"><?php esc_html_e( 'Выравнивание меню', 'mp-header' ); ?></label></th>
						<td>
							<select id="menu_align" name="<?php echo esc_attr( $o ); ?>[menu_align]">
								<option value="left"   <?php selected( $opts['menu_align'], 'left' );   ?>><?php esc_html_e( 'По левому краю', 'mp-header' ); ?></option>
								<option value="center" <?php selected( $opts['menu_align'], 'center' ); ?>><?php esc_html_e( 'По центру', 'mp-header' ); ?></option>
								<option value="right"  <?php selected( $opts['menu_align'], 'right' );  ?>><?php esc_html_e( 'По правому краю', 'mp-header' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Работает, только если задана ширина меню.', 'mp-header' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'Как вставить', 'mp-header' ); ?></h2>
			<p><?php esc_html_e( 'В Elementor: «Короткий код» → ', 'mp-header' ); ?> <code>[mp_header]</code>. <?php esc_html_e( 'Можно вставлять и в любую страницу/пост/виджет.', 'mp-header' ); ?></p>
			<p><?php esc_html_e( 'Контент темы получает автоматический отступ сверху, если включено «Прилипание». Если где-то хедер не виден — проверьте, что родной хедер темы отключён (в настройках темы/Elementor).', 'mp-header' ); ?></p>
		</div>
		<?php
	}
}
