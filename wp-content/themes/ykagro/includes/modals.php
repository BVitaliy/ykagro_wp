<?php // Modals + popups. ?>
<?php // data-lenis-prevent on the wrapper: it is the scroll container, so it must
      // keep scrolling while Lenis is stopped by the modal lock. ?>
<div class="modal js-modal" data-lenis-prevent aria-hidden="true">
  <div class="modal__overlay js-modal-close"></div>

  <!-- Написати нам / заявка (Figma 685:1338) -->
  <div class="modal__panel modal__panel--form" data-modal="appointment" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <h2 class="modal__title h3 clr-black">Створюймо майбутнє<br>бізнесу разом.</h2>
      <?php get_template_part( 'template-parts/form-block' ); ?>
    </div>
  </div>

  <!-- Деталі напряму (Figma 747:4583) -->
  <div class="modal__panel modal__panel--detail" data-modal="detail" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <picture class="modal__image">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image-mob.webp' ) ); ?>" media="(max-width: 767.98px)" type="image/webp">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image.webp' ) ); ?>" type="image/webp">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image-mob.jpg' ) ); ?>" media="(max-width: 767.98px)">
        <img src="<?php echo esc_url( yka_img( 'modals/detail-image.jpg' ) ); ?>" alt="" width="1218" height="672" loading="lazy" decoding="async">
      </picture>
      <h2 class="modal__title h4 clr-black js-detail-title">Контрактне виробництво кормів</h2>
      <p class="modal__text text-lg clr-gray js-detail-text">Створюємо комбікорми під вашим брендом або рецептурою, гарантуючи точність виробництва, контроль якості та своєчасне виконання замовлень.</p>
      <a href="direction-detail.php" class="btn modal__cta js-detail-cta">
        <span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
        Детальніше про напрям
      </a>
    </div>
  </div>

  <!-- Учасник команди (about) -->
  <?php // Панелі учасників команди генеруються з ACF у
  // includes/page-builder/layout-about-team.php — вони є редагованим контентом. ?>

  <div class="modal__panel modal__panel--video" data-modal="video" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__video">
      <video class="js-modal-video" controls playsinline webkit-playsinline preload="none"></video>
    </div>
  </div>

  <!-- Дякуємо -->
  <div class="modal__panel modal__panel--thanks" data-modal="thanks" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <span class="modal__icon"><?php yka_icon( 'icons/modal-thanks.svg' ); ?></span>
      <h2 class="modal__title h4 clr-black">Ваше повідомлення успішно надіслано</h2>
      <p class="modal__text text-lg clr-gray">Дякуємо! Найближчим часом ми зв'яжемося з вами для уточнення деталей.</p>
    </div>
  </div>
</div>

<!-- Cookie popup — bottom-left, no overlay -->
<div class="popup popup--cookie js-popup" data-popup="cookie">
  <span class="popup__cookie-icon icon" aria-hidden="true"><?php yka_icon( 'icons/cookie.svg' ); ?></span>
  <button class="popup__close js-popup-close" type="button" aria-label="Закрити">
    <?php yka_icon( 'icons/close.svg' ); ?>
  </button>
  <div class="popup__content">
    <p class="popup__title h6 clr-black">Ми використовуємо файли cookie</p>
    <p class="popup__text text-sm clr-gray">Ми використовуємо <a href="privacy.php">cookies</a>, щоб зробити ваш досвід на сайті зручнішим та приємнішим. Натискаючи «Прийняти», ви погоджуєтеся на використання cookies відповідно до нашої <a href="privacy.php">Політики конфіденційності.</a></p>
    <div class="popup__actions">
      <button class="link-more popup__accept js-popup-close" type="button">
        Прийняти
        <span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
      </button>
    </div>
  </div>
</div>
